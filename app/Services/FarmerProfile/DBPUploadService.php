<?php

namespace App\Services\FarmerProfile;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Enums\DBPUploadKeysV3;
use App\Traits\HasFileUploads;
use App\Enums\DBPUploadKeysV3V3;
use App\Enums\DisbursementConfig;
use Illuminate\Support\Facades\DB;
use App\Enums\ReferenceNumberTypes;
use App\Repositories\Address\Province\IProvinceRepository;
use App\Repositories\FarmerImport\IFarmerImportRepository;
use App\Repositories\InReceiveFromDBP\IInReceiveFromDBPRepository;
use App\Repositories\Notification\INotificationRepository;
use Illuminate\Support\Facades\Storage;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;

class DBPUploadService implements IDBPUploadService
{

    use HasFileUploads;
    private $entries;
    private $authUser;
    private $filePath;
    private $prov;
    private $success;
    private $fails;
    private IUserAccountRepository $userAccountRepository;
    private IUserTransactionHistoryRepository $userTransactionHistory;
    private IUserBalanceInfoRepository $userBalanceInfo;
    private IInReceiveFromDBPRepository $dbpRepository;
    private IReferenceNumberService $referenceNumberService;
    private IFarmerImportRepository $farmerImportRepository;
    private IProvinceRepository $provinceRepository;
    private INotificationRepository $notificationRepository;

    public function __construct(
        IUserAccountRepository $userAccountRepository, 
        IUserTransactionHistoryRepository $userTransactionHistoryRepository,
        IUserBalanceInfoRepository $userBalanceInfo,
        IInReceiveFromDBPRepository $dbpRepository,
        IReferenceNumberService $referenceNumberService,
        IFarmerImportRepository $farmerImportRepository,
        IProvinceRepository $provinceRepository,
        INotificationRepository $notificationRepository
    )
    {
        $this->entries = collect();
        $this->userAccountRepository = $userAccountRepository;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->dbpRepository = $dbpRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->farmerImportRepository = $farmerImportRepository;
        $this->provinceRepository = $provinceRepository;
        $this->notificationRepository = $notificationRepository;
        $this->success = 0;
        $this->fails = 0;
    }

    // V3
    public function uploadSubsidyFileToS3v3($file) {
        $fileExt = $this->getFileExtensionName($file);
        $fileName = request()->user()->id . "/" . Str::random(40) . "." . $fileExt;
        $folder = Carbon::now()->format('Y-m-d');
        return $this->saveFile($file, $fileName, $folder ? $folder : 'dbp_uploads');
    }

    public function processSubsidyV3(array $attr, string $authUser) {
        $this->authUser = $authUser;
        $this->filePath = $attr['path'];

        $file = $this->s3TempUrl($attr['path']);
        $contents = file($file);
        foreach($contents as $key => $content) {
            $record = explode('|', $content);
            $this->prov = preg_replace("/\r|\n/", "", $record[DBPUploadKeysV3::remitterAddress3]);
            $errors = $this->runValidation($record);

            if(count($errors) == 0) {
                try {
                    \DB::beginTransaction();
                    $rsbsaNumber = preg_replace("/[^0-9]/", "", $record[DBPUploadKeysV3::RSBSANumber]);
                    $referenceNumber = $this->referenceNumberService->generate(ReferenceNumberTypes::ReceiveMoneyDBP);
                    $user = $this->userAccountRepository->getUserAccountByRSBSANoV2($rsbsaNumber);
                    $transaction = $this->addReceiveFromDBP($user, $referenceNumber, $record);
                    $this->addTransaction($user, $referenceNumber, $transaction, $record);
                    $this->addUserBalance($user, $referenceNumber, $record[DBPUploadKeysV3::remittanceAmount]);
                    $content = preg_replace('/\r|\n/', '', $content) . "|" . "CREDITED" . "\n";
                    $this->entries->push($content);
                    $this->success = $this->success + 1;
                    \DB::commit();
                } catch (\Exception $err) {
                    \DB::rollBack();
                    $error = "Row " . ($key + 1) . ", " . $err->getMessage();
                    $content = preg_replace('/\r|\n/', '', $content) . "|" . $error . "\n";
                    $this->entries->push($content);
                    $this->fails = $this->fails + 1;
                }
            } else {
                $error = ['Row ' . ($key + 1)];
                $error = array_merge($error, $errors);
                $error = implode(', ', $error);
                $content = preg_replace('/\r|\n/', '', $content) . "|" . $error . "\n";
                $this->entries->push($content);
                $this->fails = $this->fails + 1;
            }
            dd('awdawd');

        }

        $encodedString = $this->entries->toArray();
        $entries = implode('', $encodedString);
        //Save the JSON string to a text file.
        $fileName = $this->generateFileName($this->prov, $attr['path']);
        $path = Storage::disk('s3')->put($fileName, $entries);

        // // ADD NOTIFICATION TO AUTH USER
        $notification = $this->notificationRepository->create([
            'user_account_id' => $authUser,
            'title' => 'DBP Disbursement Upload',
            'description' => Storage::disk('s3')->temporaryUrl($fileName, Carbon::now()->addMinutes(30)),
            'status' => 1,
            'user_created' => $authUser,
            'user_updated' => $authUser
        ]);

        return [
            'path' => $fileName,
            'notification' => $notification
        ];
    }

    private function generateFileName(string $prov, string $filePath) {
        $seq = $this->farmerImportRepository->countSequenceByProvindeAndDateCreatedSubsidy($prov, Carbon::now()->format('Y-m-d'));

        
        $this->farmerImportRepository->createSubsidy([
            'filename' => $filePath,
            'province' => $prov,
            'seq' => ($seq + 1),
            'success' => $this->success,
            'fails' => $this->fails,
        ]);

        
        $seq = ($seq + 1);
        $formatSeq = $seq;
        
        if($seq < 10) {
            $formatSeq = "00" . $seq;
        } else if($seq < 100) {
            $formatSeq = "0" . $seq;
        }
        
        $province = $this->provinceRepository->getProvinceByName($prov);

        $fileName = 'DISRETRFFA' . $province->province_code . "SPTI" . Carbon::now()->format('Ymd') . $formatSeq;
        return "DBP/Subsidy/" . $fileName . ".txt";
    }

    private function addUserBalance($user, $amount)
    {
        $currentBalance = $this->userBalanceInfo->getUserBalance($user->id);
        $total = (float)$currentBalance + (float)DBPUploadKeysV3::remittanceAmount;
        return $this->userBalanceInfo->updateUserBalance($user->id, $total);
    }

    private function addTransaction($user, $referenceId, $transaction, $row)
    {
        $params = [
            'user_account_id' => $user->id,
            'transaction_id' => $transaction->id,
            'reference_number' => $referenceId,
            'total_amount' => (float)$row[DBPUploadKeysV3::remittanceAmount],
            'transaction_category_id' => DBPUploadKeysV3::transactionCategoryId,
            'user_created' => $this->authUser,
            'user_updated' => $this->authUser,
            'transaction_date' => $transaction->transaction_date,
            'funding_currency' => isset($row[DBPUploadKeysV3::currency]) ? $row[DBPUploadKeysV3::currency] : "",
            'remittance_date' => isset($row[DBPUploadKeysV3::remittanceDate]) ? $row[DBPUploadKeysV3::remittanceDate] : "",
            'service_code' => isset($row[DBPUploadKeysV3::serviceCode]) ? $row[DBPUploadKeysV3::serviceCode] : "",
            'outlet_name' => isset($row[DBPUploadKeysV3::outletName]) ? $row[DBPUploadKeysV3::outletName] : "",
            'beneficiary_name_1' => isset($row[DBPUploadKeysV3::beneficiary1]) ? $row[DBPUploadKeysV3::beneficiary1] : "",
            'beneficiary_name_2' => isset($row[DBPUploadKeysV3::beneficiary2]) ? $row[DBPUploadKeysV3::beneficiary2] : "",
            'beneficiary_name_3' => isset($row[DBPUploadKeysV3::beneficiary3]) ? $row[DBPUploadKeysV3::beneficiary3] : "",
            'beneficiary_address_1' => isset($row[DBPUploadKeysV3::beneficiary1Address]) ? $row[DBPUploadKeysV3::beneficiary1Address] : "",
            'beneficiary_address_2' => isset($row[DBPUploadKeysV3::beneficiary2Address]) ? $row[DBPUploadKeysV3::beneficiary2Address] : "",
            'beneficiary_address_3' => isset($row[DBPUploadKeysV3::beneficiary3Address]) ? $row[DBPUploadKeysV3::beneficiary3Address] : "",
            'mobile_number' => isset($row[DBPUploadKeysV3::beneficiaryPhoneNo]) ? $row[DBPUploadKeysV3::beneficiaryPhoneNo] : "",
            'message' => isset($row[DBPUploadKeysV3::message]) ? $row[DBPUploadKeysV3::message] : "",
            'remitter_name_1' => isset($row[DBPUploadKeysV3::remitterName1]) ? $row[DBPUploadKeysV3::remitterName1] : "",
            'remitter_name_2' => isset($row[DBPUploadKeysV3::remitterName2]) ? $row[DBPUploadKeysV3::remitterName2] : "",
            'remitter_address_1' => isset($row[DBPUploadKeysV3::remitterAddress1]) ? $row[DBPUploadKeysV3::remitterAddress1] : "",
            'remitter_address_2' => isset($row[DBPUploadKeysV3::remitterAddress2]) ? $row[DBPUploadKeysV3::remitterAddress2] : "",
        ];
        $this->userTransactionHistoryRepository->create($params);
    }

    private function addReceiveFromDBP($user, $referenceId, $row)
    {
        $data = [
            "user_account_id" => $user->id,
            "reference_number" => $referenceId,
            "total_amount" => (float)$row[DBPUploadKeysV3::remittanceAmount],
            "transaction_date" => date('Y-m-d H:i:s'),
            "transaction_category_id" => DBPUploadKeysV3::transactionCategoryId,
            "transaction_remarks" => '',
            "file_name" => $this->filePath,
            "status" => 'SUCCESS',
            "user_created" => $this->authUser,
            'user_updated' => $this->authUser,
            'funding_currency' => isset($row[DBPUploadKeysV3::currency]) ? $row[DBPUploadKeysV3::currency] : "",
            'remittance_date' => isset($row[DBPUploadKeysV3::remittanceDate]) ? $row[DBPUploadKeysV3::remittanceDate] : "",
            'service_code' => isset($row[DBPUploadKeysV3::serviceCode]) ? $row[DBPUploadKeysV3::serviceCode] : "",
            'outlet_name' => isset($row[DBPUploadKeysV3::outletName]) ? $row[DBPUploadKeysV3::outletName] : "",
            'beneficiary_name_1' => isset($row[DBPUploadKeysV3::beneficiary1]) ? $row[DBPUploadKeysV3::beneficiary1] : "",
            'beneficiary_name_2' => isset($row[DBPUploadKeysV3::beneficiary2]) ? $row[DBPUploadKeysV3::beneficiary2] : "",
            'beneficiary_name_3' => isset($row[DBPUploadKeysV3::beneficiary3]) ? $row[DBPUploadKeysV3::beneficiary3] : "",
            'beneficiary_address_1' => isset($row[DBPUploadKeysV3::beneficiary1Address]) ? $row[DBPUploadKeysV3::beneficiary1Address] : "",
            'beneficiary_address_2' => isset($row[DBPUploadKeysV3::beneficiary2Address]) ? $row[DBPUploadKeysV3::beneficiary2Address] : "",
            'beneficiary_address_3' => isset($row[DBPUploadKeysV3::beneficiary3Address]) ? $row[DBPUploadKeysV3::beneficiary3Address] : "",
            'mobile_number' => isset($row[DBPUploadKeysV3::beneficiaryPhoneNo]) ? $row[DBPUploadKeysV3::beneficiaryPhoneNo] : "",
            'message' => isset($row[DBPUploadKeysV3::message]) ? $row[DBPUploadKeysV3::message] : "",
            'remitter_name_1' => isset($row[DBPUploadKeysV3::remitterName1]) ? $row[DBPUploadKeysV3::remitterName1] : "",
            'remitter_name_2' => isset($row[DBPUploadKeysV3::remitterName2]) ? $row[DBPUploadKeysV3::remitterName2] : "",
            'remitter_address_1' => isset($row[DBPUploadKeysV3::remitterAddress1]) ? $row[DBPUploadKeysV3::remitterAddress1] : "",
            'remitter_address_2' => isset($row[DBPUploadKeysV3::remitterAddress2]) ? $row[DBPUploadKeysV3::remitterAddress2] : "",
        ];

        return $this->dbpRepository->create($data);
    }

    public function runValidation(array $attr) {
        $errors = [];
        // RSBSA 
        if($attr && !isset($attr[DBPUploadKeysV3::RSBSANumber])) {
            array_push($errors, 'RSBSA Number is required');
        }

        if($attr && isset($attr[DBPUploadKeysV3::RSBSANumber])) {
            $rsbsaNumber = preg_replace("/[^0-9]/", "", $attr[DBPUploadKeysV3::RSBSANumber]);
            $account = $this->userAccountRepository->getUserAccountByRSBSANoV2($rsbsaNumber);
            if(!$account) {
                array_push($errors, 'RSBSA Number doesn\'t exist in record(s)');
            }

        }

        // REMITTANCE DATE
        if($attr && !isset($attr[DBPUploadKeysV3::remittanceDate])) {
            array_push($errors, 'Remittance Date is required');
        }

        // SERVICE CODE
        if($attr && !isset($attr[DBPUploadKeysV3::serviceCode])) {
            array_push($errors, 'Service Code is required');
        }
        
        // APPLICATION NUMBER
        if($attr && !isset($attr[DBPUploadKeysV3::applicationNumber])) {
            array_push($errors, 'Application Number is required');
        }

        if($attr && isset($attr[DBPUploadKeysV3::applicationNumber])) {
            $rsbsaNumber = preg_replace("/[^0-9]/", "", $attr[DBPUploadKeysV3::RSBSANumber]);
            $record = $this->userAccountRepository->getUserAccountByRSBSANoV2($rsbsaNumber);

        if($attr && isset($attr[DBPUploadKeysV3::applicationNumber])) {
            $rsbsaNumber = preg_replace("/[^0-9]/", "", $attr[DBPUploadKeysV3::RSBSANumber]);
            $record = $this->userAccountRepository->getUserAccountByRSBSANoV2($rsbsaNumber);

            if(!$record) {
                array_push($errors, 'User Account doesn\'t exists');
            }

            
            if($attr && isset($attr[DBPUploadKeysV3::applicationNumber]) && $record) {
               $exists = $this->dbpRepository->getExistByTransactionCategory($record->id, DBPUploadKeysV3::transactionCategoryId);
                if((Integer)$exists > 0) {
                   array_push($errors, 'Disbursement record has already been uploaded(duplicate record)');
                }
            }
        }


        // REMITTANCE AMOUNT
        if($attr && !isset($attr[DBPUploadKeysV3::remittanceAmount])) {
            array_push($errors, 'Remittance Amount is required');
        }

        // OUTLET NAME
        if($attr && !isset($attr[DBPUploadKeysV3::outletName])) {
            array_push($errors, 'Outlet name is required');
        }

        // BENEFICIARY 1
        if($attr && !isset($attr[DBPUploadKeysV3::beneficiary1])) {
            array_push($errors, 'Beneficiary Name 1 is required');
        }

        // BENEFICIARY 2
       //if($attr && !isset($attr[DBPUploadKeysV3::beneficiary2])) {
            //array_push($errors, 'Beneficiary Name 2 is required');
       // }
        
        // BENEFICIARY ADDRESS 1
       // if($attr && !isset($attr[DBPUploadKeysV3::beneficiary1Address])) {
           // array_push($errors, 'Beneficiary Address 1 is required');
        //}

        // BENEFICIARY ADDRESS 2
        //if($attr && !isset($attr[DBPUploadKeysV3::beneficiary2Address])) {
            //array_push($errors, 'Beneficiary Address 2 is required');
        //}

        // REMITTER NAME 1
        //if($attr && !isset($attr[DBPUploadKeysV3::remitterName1])) {
           // array_push($errors, 'Remitter 1 Name is required');
        //}

        // REMITTER NAME 2
        //if($attr && !isset($attr[DBPUploadKeysV3::remitterName2])) {
            //array_push($errors, 'Remitter 2 Name is required');
        //}

        // REMITTER ADDRESS 1
        //if($attr && !isset($attr[DBPUploadKeysV3::remitterAddress1])) {
           // array_push($errors, 'Remitter 1 Address is required');
        //}

        // REMITTER ADDRESS 2
        //if($attr && !isset($attr[DBPUploadKeysV3::remitterAddress2])) {
            //array_push($errors, 'Remitter 2 Address is required');
        //}

        return $errors;
    }

    public function s3TempUrl(string $generated_link) {
        $temp_url = Storage::disk('s3')->temporaryUrl($generated_link, Carbon::now()->addMinutes(30));
        return $temp_url;
    }
}

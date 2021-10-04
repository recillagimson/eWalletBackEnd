<?php

namespace App\Imports\FarmerV2;

use App\Enums\DBPUploadKeys;
use Illuminate\Support\Collection;
use App\Enums\ReferenceNumberTypes;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\InReceiveFromDBP\IInReceiveFromDBPRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;

class FarmerSubsidyImportV2 implements ToCollection, WithHeadingRow, WithBatchInserts
{
    private $authUser;
    private IUserAccountRepository $userAccountRepository;
    private ITransactionCategoryRepository $transactionCategory;
    private IInReceiveFromDBPRepository $dbpRepository;
    private IReferenceNumberService $referenceNumberService;
    private IUserBalanceInfoRepository $userBalanceInfo;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private $success;
    private $errors;
    private $filePath;

    public function __construct(
        string $authUser,
        IUserAccountRepository $userAccountRepository,
        ITransactionCategoryRepository $transactionCategory,
        IInReceiveFromDBPRepository $dbpRepository,
        IReferenceNumberService $referenceNumberService,
        string $filePath,
        IUserBalanceInfoRepository $userBalanceInfo,
        IUserTransactionHistoryRepository $userTransactionHistoryRepository
    )
    {
        $this->filePath = $filePath;
        $this->authUser = $authUser;
        $this->transactionCategory = $transactionCategory;
        $this->userAccountRepository = $userAccountRepository;
        $this->dbpRepository = $dbpRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->success = new collection();
        $this->errors = new collection();
    }

    public function collection(Collection $collection)
    {
        foreach($collection as $key => $entry) {
            $errors = $this->runValidation($entry->toArray());
            if(count($errors) == 0) {
                // PROCESS INFO
                try {
                    \DB::beginTransaction();
                    $attr = $entry->toArray();
                    $rsbsaNumber = preg_replace("/[^0-9]/", "", $attr[DBPUploadKeys::rsbsaNumberSubsidy]);
                    $referenceNumber = $this->referenceNumberService->generate(ReferenceNumberTypes::ReceiveMoneyDBP);
                    $user = $this->userAccountRepository->getUserAccountByRSBSANoV2($rsbsaNumber);
                    $transaction = $this->addReceiveFromDBP($user, $referenceNumber, $attr);
                    $this->addTransaction($user, $referenceNumber, $transaction, $attr);
                    $this->addUserBalance($user, $referenceNumber, $attr[DBPUploadKeys::amount]);
                    \DB::commit();
                    $this->success->push($entry);
                } 
                catch(\Exception $errrr) {
                    \DB::rollBack();
                    $err = ['Row ' . ($key + 1)];
                    $errorString = implode(', ', array_merge($err, [$errrr->getMessage()]));
                    $remarks = ['remarks' => $errorString];
                    $this->errors->push(array_merge($remarks, $entry->toArray()));
                }
            } else {
                // HANDLE ERROR
                $err = ['Row ' . ($key + 1)];
                $errorString = implode(', ', array_merge($err, $errors));
                $remarks = ['remarks' => $errorString];
                $this->errors->push(array_merge($remarks, $entry->toArray()));
            }
        }
    }

    private function addUserBalance($user, $amount)
    {
        $currentBalance = $this->userBalanceInfo->getUserBalance($user->id);
        $total = (float)$currentBalance + (float)DBPUploadKeys::subsidyAmount;
        return $this->userBalanceInfo->updateUserBalance($user->id, $total);
    }

    private function addTransaction($user, $referenceId, $transaction, $row)
    {
        $this->userTransactionHistoryRepository->create([
            'user_account_id' => $user->id,
            'transaction_id' => $transaction->id,
            'reference_number' => $referenceId,
            'total_amount' => (float)$row[DBPUploadKeys::amount],
            'transaction_category_id' => $row[DBPUploadKeys::batchCode],
            'user_created' => $this->authUser,
            'user_updated' => $this->authUser,
            'transaction_date' => $transaction->transaction_date
        ]);
    }

    private function addReceiveFromDBP($user, $referenceId, $row)
    {
        $data = [
            "user_account_id" => $user->id,
            "reference_number" => $referenceId,
            "total_amount" => (float)$row[DBPUploadKeys::amount],
            "transaction_date" => date('Y-m-d H:i:s'),
            "transaction_category_id" => $row[DBPUploadKeys::batchCode],
            "transaction_remarks" => '',
            "file_name" => $this->filePath,
            "status" => 'SUCCESS',
            "user_created" => $this->authUser,
            'user_updated' => $this->authUser,
        ];

        return $this->dbpRepository->create($data);
    }

    public function runValidation(array $attr) {
        $errors = [];
        // VALIDATE User Account Number
        if($attr && !isset($attr[DBPUploadKeys::userAccountNumber])) {
            array_push($errors, 'User Account Number is required');
        }

        if($attr && isset($attr[DBPUploadKeys::userAccountNumber])) {
            $userAccount = $this->userAccountRepository->getUserByAccountNumber($attr[DBPUploadKeys::userAccountNumber]);
            if(!$userAccount) {
                array_push($errors, 'User Account does not exist');
            }

            if($userAccount) {
                if(!$userAccount->verified) {
                    array_push($errors, 'Unverified Account');
                }
            }
        }

        // VALIDATE RSBSA Number
        if($attr && !isset($attr[DBPUploadKeys::rsbsaNumberSubsidy])) {
            array_push($errors, 'RSBSA Number is required');
        }

        if($attr && isset($attr[DBPUploadKeys::rsbsaNumberSubsidy])) {
            $rsbsaNumber = preg_replace("/[^0-9]/", "", $attr[DBPUploadKeys::rsbsaNumberSubsidy]);
            $account = $this->userAccountRepository->getUserAccountByRSBSANoV2($rsbsaNumber);
            if(!$account) {
                array_push($errors, 'RSBSA Number doesn\'t exist in record(s)');
            }
        }

        if($attr && isset($attr[DBPUploadKeys::rsbsaNumberSubsidy]) && isset($attr[DBPUploadKeys::userAccountNumber])) {
            $exists = $this->dbpRepository->getExistByTransactionCategory($attr[DBPUploadKeys::userAccountNumber], $attr[DBPUploadKeys::rsbsaNumberSubsidy]);

            if((Integer)$exists > 1) {
                array_push($errors, 'Subsidiary for this record has already been uploaded(duplicate record)');
            }
        }

        // VALIDATE Amount
        if($attr && !isset($attr[DBPUploadKeys::amount])) {
            array_push($errors, 'Amount is required');
        }

        if($attr && isset($attr[DBPUploadKeys::amount])) {
            if(!ctype_digit($attr[DBPUploadKeys::amount])) {
                array_push($errors, 'Amount must be valid amount');
            }
        }

        // VALIDATE BAtch Code
        if($attr && !isset($attr[DBPUploadKeys::batchCode])) {
            array_push($errors, 'Batch Code is required');
        }

        if($attr && isset($attr[DBPUploadKeys::batchCode])) {
            $batchCode = $this->transactionCategory->get($attr[DBPUploadKeys::batchCode]);
            if(!$batchCode) {
                array_push($errors, 'Batch Code is invalid');
            }
        }

        return $errors;
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function batchSize(): int
    {
        return 50;
    }

    public function getFails()
    {
        return $this->errors;
    }

    public function getSuccesses()
    {
        return $this->success;
    }

    // public function getHeaders() {
    //     return $this->headers;
    // }

    // public function getProv() {
    //     return $this->province;
    // }
}

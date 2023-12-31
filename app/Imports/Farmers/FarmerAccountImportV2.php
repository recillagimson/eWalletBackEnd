<?php

namespace App\Imports\Farmers;

use DB;
use Exception;
use Carbon\Carbon;
use App\Enums\Country;
use App\Enums\Currencies;
use App\Enums\Nationality;
use App\Enums\AccountTiers;
use App\Enums\NatureOfWork;
use App\Enums\SourceOfFund;
use Illuminate\Support\Str;
use App\Enums\DBPUploadKeys;
use App\Enums\MaritalStatus;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserAccountNumber\IUserAccountNumberRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserUtilities\MaritalStatus\IMaritalStatusRepository;

class FarmerAccountImportV2 implements ToCollection, WithHeadingRow, WithBatchInserts
{

    private $errors;
    private $success;
    private IUserAccountRepository $userAccountRepository;
    private IUserDetailRepository $userDetail;
    private IMaritalStatusRepository $maritalStatus;
    private IUserAccountNumberRepository $userAccountNumbers;
    private IUserBalanceInfoRepository $userBalance;
    private $province;
    private $rsbsaNumbers;


    public function __construct(IUserDetailRepository $userDetail, string $currentUser, IMaritalStatusRepository $maritalStatus, IUserAccountNumberRepository $userAccountNumbers, IUserAccountRepository $userAccountRepository, IUserBalanceInfoRepository $userBalance)
    {
        $this->errors = collect();
        $this->success = collect();
        $this->userDetail = $userDetail;
        $this->headers = collect();
        $this->errorBag = collect();
        $this->processed = collect();
        $this->currentUser = $currentUser;
        $this->maritalStatus = $maritalStatus;
        $this->userAccountNumbers = $userAccountNumbers;
        $this->userAccountRepository = $userAccountRepository;
        $this->userBalance = $userBalance;
        $this->province = '';
        $this->rsbsaNumbers = array();
    }
    /**
    * @param Collection $collection
    */

    private function setupUserBalance(string $userId)
    {
        $balance = [
            'user_account_id' => $userId,
            'available_balance' => 0,
            'currency_id' => Currencies::philippinePeso,
            'user_created' => $this->currentUser,
            'user_updated' => $this->currentUser,
        ];

        $this->userBalance->create($balance);
    }

    private function setupUserProfile($row, $userAccount)
    {
        // $marital = $this->maritalStatus->getByDescription($row[DBPUploadKeys::maritalStatus])->id;
        $dob = is_numeric($row[DBPUploadKeys::birthDate]) ? Carbon::instance(Date::excelToDateTimeObject($row[DBPUploadKeys::birthDate])) : Carbon::parse(strtotime($row[DBPUploadKeys::birthDate]));

        $profile = [
            'entity_id' => null,
            'user_account_id' => $userAccount->id,
            'title' => null,
            'last_name' => $row[DBPUploadKeys::lastName],
            'first_name' => $row[DBPUploadKeys::firstName],
            'middle_name' => $row[DBPUploadKeys::middleName],
            'name_extension' => $row[DBPUploadKeys::extName],
            'birth_date' => $dob,
            'place_of_birth' => $row[DBPUploadKeys::birthPlace],
            'marital_status_id' => MaritalStatus::Single,
            'nationality_id' => Nationality::filipino,
            'encoded_nationality' => null,
            'occupation' => null,
            'house_no_street' => $row[DBPUploadKeys::houseNo],
            'barangay' => $row[DBPUploadKeys::barangay],
            'city' => $row[DBPUploadKeys::city],
            'province_state' => $row[DBPUploadKeys::province],
            'municipality' => $row[DBPUploadKeys::municipality],
            'country_id' => Country::PH,
            'postal_code' => null,
            'nature_of_work_id' => NatureOfWork::farmer,
            'encoded_nature_of_work' => null,
            'source_of_fund_id' => SourceOfFund::farming,
            'encoded_source_of_fund' => null,
            'mother_maidenname' => $row[DBPUploadKeys::mothersMaidenName],
            'currency_id' => Currencies::philippinePeso,
            'selfie_loction' => null,
            'signup_host_id' => null,
            'verification_status' => null,
            'user_account_status' => null,
            'emergency_lock_status' => null,
            'report_exception_status' => null,
            'user_created' => null,
            'user_updated' => null,
            'guardian_name' => null,
            'guardian_mobile_number' => null,
            'avatar_location' => null,
            'user_created' => $this->currentUser,
            'user_updated' => $this->currentUser,
            'no_of_farm_parcel' => $row[DBPUploadKeys::farmParcel],
            'total_farm_area' => $row[DBPUploadKeys::totalFarmArea],
            'district' => $row[DBPUploadKeys::district],
            'region' => $row[DBPUploadKeys::region],
            'government_id_type' => $row[DBPUploadKeys::govtidtype],
            'sex' => $row[DBPUploadKeys::sex],
            'id_number' => $row[DBPUploadKeys::idNumber]
        ];

        $this->userDetail->create($profile);
    }

    public function collection(Collection $collection)
    {
        $rsbsaNumbers = collect();
        foreach($collection as $coll) {
           
            $rsbsa = $coll->get(DBPUploadKeys::rsbsaNumber);
            if($rsbsa) {
                $rsbsaNumbers->push($rsbsa);
            }
        }
        $this->rsbsaNumbers = array_count_values($rsbsaNumbers->toArray());

        foreach($collection as $key => $entry) {
            $data = $entry->toArray();
            // HANDLE PROVINCE
            if(!$this->province && isset($data[DBPUploadKeys::province])) {
                $this->province = $data[DBPUploadKeys::province];
            }
            
            $st = trim(implode("", $data));
            if($st != "") {
                
                // HANDLE VALIDATION AND FAILED ENTRIES
                $isValid = $this->runValidation($entry->toArray(), ($key + 1));
                if($isValid) {

                    // VALIDATE IF USER DETAIL ALREADY PRESENT
                    // VALIDATE IF USER RSBSA NUMBER EXIST
                    $rsbsa_number = preg_replace("/[^0-9]/", "", $data[DBPUploadKeys::rsbsaNumber]);
                    $doesExist = $this->userDetail->getIsExistingByNameAndBirthday($data[DBPUploadKeys::firstName], $entry[DBPUploadKeys::middleName], $data[DBPUploadKeys::lastName], $data[DBPUploadKeys::birthDate]);
                    $isPresent = $this->userAccountRepository->getAccountDetailByRSBSANumber($rsbsa_number);

                    if(!$doesExist && !$isPresent){
                        DB::beginTransaction();
                        try {
                            $userAccount = $this->setupUserAccount($entry->toArray());
                            $this->setupUserProfile($entry->toArray(), $userAccount);
                            $this->setupUserBalance($userAccount->id);
                            $this->success->push(array_merge($userAccount->toArray(), $entry->toArray()));
                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            $dt = [
                                'Row ' . ($key + 1)
                            ];
                            $message = implode(', ', array_merge($dt, [$e->getMessage() . "."]));
                            $this->errors->push(array_merge(['remarks' => $message], $data));
                        }
                    } else {
                        $dt = [
                            'Row ' . ($key + 1)
                        ];
                        $message = implode(', ', array_merge($dt, ['User already exist.']));
                        $this->errors->push(array_merge(['remarks' => $message], $data));
                    }
                }
            }
        }
    }

    private function setupUserAccount($row)
    {
        $rsbsa = preg_replace("/[^0-9]/", "", $row[DBPUploadKeys::rsbsaNumber]);
        $password = $rsbsa;
        $pin = substr($rsbsa, -4); //last 4 chars of rsbsa_number

        $farmersCount = $this->userAccountRepository->getAccountsWithRSBSANumberCount();
        $padCount = Str::padLeft($farmersCount, 8, '0');
        $accountNumber = "F" . date('Y') . $padCount;

        $farmer = [
            'rsbsa_number' => $rsbsa,
            'password' => bcrypt($password),
            'pin_code' => bcrypt($pin),
            'tier_id' => AccountTiers::tier1,
            'account_number' => $accountNumber,
            'mobile_number' => "0" . trim((Integer)$row[DBPUploadKeys::mobileNumber]),
            'user_created' => $this->currentUser,
            'user_updated' => $this->currentUser,
        ];

        $record = $this->userAccountRepository->create($farmer);
        return $record;
    }

    public function runValidation(array $attr, string $row) {
        $errors = new collection([]);
        // dd($attr);
        $rsbsa_number = preg_replace("/[^0-9]/", "", $attr[DBPUploadKeys::rsbsaNumber]);
        if($attr[DBPUploadKeys::rsbsaNumber] == '') {
            $errors->push('RSBSA Number is required.');
        }
        if($this->userAccountRepository->getUserAccountByRSBSANoV2($rsbsa_number)) {
            $errors->push('RSBSA Number already exist.');
        }
        if(strlen($rsbsa_number) != 15) {
            $errors->push('Invalid RSBSA Number.');
        }
        if(isset($this->rsbsaNumbers[$attr[DBPUploadKeys::rsbsaNumber]]) && $this->rsbsaNumbers[$attr[DBPUploadKeys::rsbsaNumber]] > 1) {
        // if(isset($this->rsbsaNumbers[$attr[DBPUploadKeys::rsbsaNumber]]) && $this->rsbsaNumbers[$attr[DBPUploadKeys::rsbsaNumber]] > 1) {
            $errors->push('Multiple instance of RSBSA Reference Number ' . $attr[DBPUploadKeys::rsbsaNumber] . ".");
        }
        if($attr[DBPUploadKeys::firstName] == '') {
            $errors->push('First Name is required.');
        }
        if($attr[DBPUploadKeys::lastName] == '') {
            $errors->push('Last Name is required.');
        }

        if($attr[DBPUploadKeys::lastName] && $attr[DBPUploadKeys::middleName] && $attr[DBPUploadKeys::firstName] && $attr[DBPUploadKeys::birthDate]) {
            $dob = is_numeric($attr[DBPUploadKeys::birthDate]) ? \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($attr[DBPUploadKeys::birthDate])) : \Carbon\Carbon::parse(strtotime($attr[DBPUploadKeys::birthDate]));

            $result = $this->userDetail->getIsExistingByNameAndBirthday(
                $attr[DBPUploadKeys::firstName],
                $attr[DBPUploadKeys::middleName],
                $attr[DBPUploadKeys::lastName],
                $dob
            );

            if($result) {
                $errors->push('User Account already exists.');
            }
        }

        if($attr[DBPUploadKeys::idNumber] == '') {
            $errors->push('ID Number is required.');
        }
        if($attr[DBPUploadKeys::govtidtype] == '') {
            $errors->push('Government ID is required.');
        }
        if($attr[DBPUploadKeys::barangay] == '') {
            $errors->push('Barangay is required.');
        }
        if($attr[DBPUploadKeys::city] == '') {
            $errors->push('City is required.');
        }
        // if($attr[DBPUploadKeys::district] == '') {
        //     $errors->push('District is required.');
        // }
        if($attr[DBPUploadKeys::province] == '') {
            $errors->push('Province is required.');
        }
        if($attr[DBPUploadKeys::birthDate] == '') {
            $errors->push('Birthday is required.');
        }
   
        //if (!preg_match('/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/', $attr[DBPUploadKeys::birthDate])) {
          //  $errors->push('Invalid date format for Birthday.');
        //}
        
        if($attr[DBPUploadKeys::birthPlace] == '') {
            $errors->push('Place of birth is required.');
        }
        if($attr[DBPUploadKeys::mobileNumber] == '') {
            $errors->push('Mobile Number is required.');
        }
        if(is_numeric($attr[DBPUploadKeys::mobileNumber])) {
            if(trim(strlen((Integer)$attr[DBPUploadKeys::mobileNumber])) != 10) {
                $errors->push('Mobile Number must be 10 digits.');
            }
        } else {
            $errors->push('Invalid Mobile Number.');
        }
        //if(!ctype_digit($attr[DBPUploadKeys::mobileNumber])) {
         //   $errors->push('Invalid Mobile Number.');
       // }
        if($attr[DBPUploadKeys::sex] == '') {
            $errors->push('Sex is required.');
        }
        if($attr[DBPUploadKeys::nationality] == '') {
            $errors->push('Nationality is required.');
        }
        if($attr[DBPUploadKeys::profession] == '') {
            $errors->push('Profession is required.');
        }
        if($attr[DBPUploadKeys::sourceoffunds] == '') {
            $errors->push('Source of fund(s) is required.');
        }
        if($attr[DBPUploadKeys::mothermaidenname] == '') {
            $errors->push("Mother's maiden name is required.");
        }

        if(count($errors) == 0) {
            return true;
        }
        $data = [
            'Row ' . $row
        ];

        $message = implode(', ', array_merge($data, $errors->toArray()));
        $this->errors->push(array_merge(['remarks' => $message], $attr));
        return false;
    }

    private function generateFarmerAccountNumber() {
        $carbon = Carbon::now();
        $currentCount = $this->userAccountRepository->getAccountsWithRSBSANumberCount();
        $strNo = "F" . $carbon->format('ymd') . Str::padLeft(($currentCount + 1), 6, '0');
        return $strNo;
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

    public function getHeaders() {
        return $this->headers;
    }

    public function getProv() {
        return $this->province;
    }
}

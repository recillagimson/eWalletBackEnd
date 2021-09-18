<?php

namespace App\Imports\Farmers;

use App\Enums\Currencies;
use App\Enums\AccountTiers;
use App\Enums\DBPUploadKeys;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserAccountNumber\IUserAccountNumberRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserUtilities\MaritalStatus\IMaritalStatusRepository;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FarmerAccountImport implements ToCollection, WithValidation, SkipsOnFailure, SkipsOnError, WithEvents, WithChunkReading, WithBatchInserts, WithHeadingRow
{
    use RegistersEventListeners, RemembersRowNumber, SkipsFailures;

    /**
    * @param Collection $collection
    */    
    private $fails;
    private $successes;
    private $headers;
    private IUserDetailRepository $userDetail;
    private $currentUser;
    private IMaritalStatusRepository $maritalStatus;
    private IUserAccountNumberRepository $userAccountNumbers;
    private IUserAccountRepository $userAccountRepository;
    private IUserBalanceInfoRepository $userBalance;
    private $errorBag;
    
    public function __construct(IUserDetailRepository $userDetail, string $currentUser, IMaritalStatusRepository $maritalStatus, IUserAccountNumberRepository $userAccountNumbers, IUserAccountRepository $userAccountRepository, IUserBalanceInfoRepository $userBalance)
    {
        $this->fails = collect();
        $this->successes = collect();
        $this->userDetail = $userDetail;
        $this->headers = collect();
        $this->errorBag = collect();
        $this->currentUser = $currentUser;
        $this->maritalStatus = $maritalStatus;
        $this->userAccountNumbers = $userAccountNumbers;
        $this->userAccountRepository = $userAccountRepository;
        $this->userBalance = $userBalance;
    }

    private function setupUserAccount($row)
    {
        $rsbsa = preg_replace("/[^0-9]/", "", $row[DBPUploadKeys::rsbsaNumber]);
        $password = $rsbsa;
        $pin = substr($rsbsa, -4); //last 4 chars of rsbsa_number
        $farmer = [
            'rsbsa_number' => $rsbsa,
            'password' => bcrypt($password),
            'pin_code' => bcrypt($pin),
            'tier_id' => AccountTiers::tier1,
            'account_number' => $this->userAccountNumbers->generateNo(),
            'mobile_number' => $row[DBPUploadKeys::mobileNumber],
            'user_created' => $this->currentUser,
        ];

        return $this->userAccountRepository->create($farmer);
    }

    private function setupUserProfile($row, $userAccount)
    {
        $marital = $this->maritalStatus->getByDescription($row[DBPUploadKeys::maritalStatus])->id;
        $dob = is_numeric($row[DBPUploadKeys::birthDate]) ? \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[DBPUploadKeys::birthDate])) : \Carbon\Carbon::parse(strtotime($row[DBPUploadKeys::birthDate]));

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
            'marital_status_id' => $marital,
            'nationality_id' => null,
            'encoded_nationality' => null,
            'occupation' => null,
            'house_no_street' => $row[DBPUploadKeys::houseNo],
            'barangay' => $row[DBPUploadKeys::barangay],
            'city' => $row[DBPUploadKeys::city],
            'province_state' => $row[DBPUploadKeys::province],
            'municipality' => $row[DBPUploadKeys::municipality],
            'country_id' => null,
            'postal_code' => null,
            'nature_of_work_id' => null,
            'encoded_nature_of_work' => null,
            'source_of_fund_id' => null,
            'encoded_source_of_fund' => null,
            'mother_maidenname' => $row[DBPUploadKeys::mothersMaidenName],
            'currency_id' => null,
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
            'government_id_type' => $row[DBPUploadKeys::govtidtype]
        ];

        $this->userDetail->create($profile);
    }

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

    public function collection(Collection $collection)
    {
        foreach($collection as $index => $entry) {
            // // IF index is 0 skip because of headers
            $row = $index;
            // dd($index);
            // if($index == 0) {
            //     dd($entry);
            //     $this->headers = $entry;
            // }
            // if($index != 0) {
                \DB::beginTransaction();
                try {
                    if($this->userDetail->getIsExistingByNameAndBirthday($entry['firstname'], $entry['middlename'], $entry['lastname'], $entry['birthdateyyyy_mm_dd']) == 0 && !$this->userAccountRepository->getAccountDetailByRSBSANumber($entry['rsbsa_reference_number'])) {
                        $userAccount = $this->setupUserAccount($entry);
                        $this->setupUserProfile($entry, $userAccount);
                        $this->setupUserBalance($userAccount->id);

                        $this->successes->push(array_merge($userAccount->toArray(), $entry->toArray()));
                        \DB::commit();
                    } else {
                        $remarks = [
                            'remarks' => 'Row ' . $row . ", Duplicate Data"
                        ];
                        $this->fails->push(array_merge($remarks, $entry->toArray()));
                        \DB::rollBack();
                    }
                } catch (\Exception $e) {
                    $remarks = [
                        'remarks' => 'Row ' . $row . ", " . $e->getMessage()
                    ];
                    $this->fails->push(array_merge($remarks, $entry->toArray()));
                    \DB::rollBack();
                }
            }
        // }
    }

    // RULE
    public function rules(): array
    {
        return [
            'rsbsa_reference_number' => ['required', 'exists:user_accounts,rsbsa_number'],
            'firstname' => ['required'],
            'middlename' => [],
            'lastname' => ['required'],
            'extensionname' => [],
            'idnumber' => ['required'],
            'govtidtype' => ['required'],
            'streetno_purokno' => [],
            'barangay' => ['required'],
            'citymunicipality' => ['required'],
            'district' => ['required'],
            'province' => ['required'],
            'region' => ['required'],
            'birthdateyyyy_mm_dd' => ['required'],
            'placeofbirth' => ['required'],
            'mobileno' => ['required'],
            'sex' => ['required'],
            'nationality' => ['required'],
            'profession' => ['required'],
            'sourceoffunds' => ['required'],
            'mothermaidenname' => ['required'],
            'of_farm_parcel' => ['required'],
            'total_farm_area_ha' => ['required'],
        ];
    }

    // ERROR MESSAGES
    public function customValidationMessages() : array 
    {
        return [
            'rsbsa_reference_number.required' => 'RSBSA Number is required',
            'rsbsa_reference_number.exists' => 'RSBSA Number already exist',
            'firstname.required' => 'First Name is required',
            'lastname.required' => 'Last Name is required',
            'idnumber.required' => 'ID Number is required',
            'govtidtype.required' => 'Government ID is required',
            'barangay.required' => 'Barangay is required',
            'citymunicipality.required' => 'City/Municipality is required',
            'district.required' => 'District is required',
            'province.required' => 'Province is required',
            'region.required' => 'Region is required',
            'birthdateyyyy_mm_dd.required' => 'Birth Date is required',
            'placeofbirth.required' => 'Place of Birth is required',
            'mobileno.required' => 'Mobile Number is required',
            'sex.required' => 'Sex is required',
            'nationality.required' => 'Nationality is required',
            'profession.required' => 'Profession is required',
            'sourceoffunds.required' => 'Source of funds is required',
            'mothermaidenname.required' => 'Mothers Maiden Name is required',
            'of_farm_parcel.required' => 'Farm Parcel is required',
            'total_farm_area_ha.required' => 'Total Farm area is required',
        ];
    }

    public function chunkSize(): int
    {
        return 50;
    }
    
    public function batchSize(): int
    {
        return 50;
    }

    public function onFailure(Failure ...$failures)
    {

        $errors = [];
        $errorMessages = [];
        foreach($failures as $failure) {
            $data = $failure->values();
            if(isset($errors[$data['rsbsa_reference_number']])) {
                $errors[$data['rsbsa_reference_number']] = array_merge($errors[$data['rsbsa_reference_number']], $failure->errors());
            } else {
                $errors = array_merge($errors, [
                    $data['rsbsa_reference_number'] => $failure->errors()
                ]);
            }
        }

        foreach($errors as $key => $error) {
            $collection = new Collection($error);
            $errorString = $collection->implode(', ', ', ');
            $errorMessages = array_merge($errorMessages, [
                $key => $errorString
            ]);
        }

        foreach($errorMessages as $key => $message) {
            $data = '';
            foreach($failures as $failure) {
                $fail = $failure->values();
                if($fail['rsbsa_reference_number'] === $key) {
                    $data = $fail;
                    if($failure->row() != "") {
                        $data = array_merge(['remarks' => "Row " . $failure->row() . ", " . $message], $data);
                    }
                }
            }
            $this->fails->push($data);
        }
    }

    public function onError(\Throwable $e)
    {
        dd($e);
        // return Excel::store(new FailedUploadExport($failures), 'failedUploadList.xlsx');
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function getFails()
    {
        return $this->fails;
    }

    public function getSuccesses()
    {
        return $this->successes;
    }
}

<?php

namespace App\Imports\Farmers;

use Illuminate\Support\Model;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Validators\Failure;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserAccountNumber\IUserAccountNumberRepository;
use App\Repositories\UserUtilities\MaritalStatus\IMaritalStatusRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Enums\AccountTiers;
use App\Enums\Currencies;
use Hash;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use App\Rules\RSBSARule;
use App\Rules\RSBSAUniqueRule;
use App\Rules\MobileNumber;
use App\Models\UserUtilities\MaritalStatus;
use App\Models\UserUtilities\Nationality;
use App\Models\UserUtilities\NatureOfWork;
use App\Models\UserUtilities\SourceOfFund;
use Illuminate\Contracts\Queue\ShouldQueue;

class FarmersImport implements ToModel, WithHeadingRow, SkipsOnFailure, SkipsOnError, WithEvents, WithChunkReading, WithBatchInserts, WithValidation
{
    use RegistersEventListeners, RemembersRowNumber;

    private $infos;
    private $userId;
    private $fails;
    private $successes;
    private $rsbsaNumbers;
    private $maritalStatus;
    private $nationality;
    private $profession;
    private $sourceOfFund;
    private IUserAccountRepository $userAccounts;
    private IUserAccountNumberRepository $userAccountNumbers;
    // private IMaritalStatusRepository $maritalStatus;
    private IUserBalanceInfoRepository $userBalance;

    public function __construct(IUserAccountRepository $userAccounts,
                                IUserDetailRepository $userDetail,
                                IUserAccountNumberRepository $userAccountNumbers,
                                IMaritalStatusRepository $maritalStatus,
                                IUserBalanceInfoRepository $userBalance,
                                $authUser)
    {
        $this->userId = $authUser;
        $this->userAccounts = $userAccounts;
        $this->userDetail = $userDetail;
        $this->userAccountNumbers = $userAccountNumbers;
        $this->userBalance = $userBalance;
        $this->fails = collect();
        $this->successes = collect();
        $this->rsbsaNumbers = collect();
        $this->infos = collect();
        $this->maritalStatus = MaritalStatus::pluck('id', 'description');
        $this->nationality = Nationality::pluck('id', 'description');
        $this->profession = NatureOfWork::pluck('id', 'description');
        $this->sourceOfFund = SourceOfFund::pluck('id', 'description');
    }

    // /**
    //  * @return int
    //  */
    // public function headingRow(): int
    // {
    //     return 2;
    // }

    public function model(array $row)
    {
        if (!$this->userDetail->getIsExistingByNameAndBirthday(
                $row['firstname'], 
                $row['middlename'], 
                $row['lastname'], 
                $row['birthdateyyyy_mm_dd']) &&
            !$this->isExistingInFile(
                $row['firstname'], 
                $row['middlename'], 
                $row['lastname'], 
                $row['birthdateyyyy_mm_dd'],
                $this->getRowNumber(),
                $row
            )
        ) {
            $user = $this->setupUserAccount($row);
            $this->setupUserProfile($user->id, $row);
            $this->setupUserBalance($user->id);
            
            $usr = ['account_number' => $user->account_number];

            $this->successes->push(array_merge($usr, $row));
        } else {
            $remark['remarks']['row'] = $this->getRowNumber();
            $remark['remarks']['errors'][] = 'Duplicate Data.';
            $this->fails->push(array_merge($remark, $row));
        }
    }

    public function rules(): array
    {
        return [
            'rsbsa_reference_number' => [
                'required',
                new RSBSAUniqueRule(),
                new RSBSARule(),
                function($attribute, $value, $onFailure) {
                    if (in_array($value, $this->rsbsaNumbers->toArray())) {
                         $onFailure('RSBSA Duplicate');
                    }
                    
                    $this->rsbsaNumbers->push($value);
                }
            ], //rsbsa_reference_number = user_accounts.rsbsa_number
            'firstname' => [
                'required',
                'max:50',
                //Rule::unique('firstname', 'middlename', 'lastname', 'birthdateyyyy_mm_dd')
            ], //firstname = user_details.first_name
            'middlename' => [
                'sometimes',
                'max:50'
            ], //middlename = user_details.middle_name
            'lastname' => [
                'required',
                'max:50'
            ], //lastname = user_details.last_name
            'extensionname' => [
                'sometimes',
                'max:50'
            ], //extensionname = user_details.name_extension
            'idnumber' => [
                'required'
            ], //idnumber = user_details.id_number
            'govtidtype' => [
                'required'
            ], //govtidtype = user_details.government_id_type
            'mothermaidenname' => [
                'required'
            ], //mothermaidenname = user_details.mother_maidenname
            'sex' => 'nullable', //sex = user_details.sex
            'vw_farmerprofile_full_wmhouse_no' => [
                'max:100'
            ], //vw_farmerprofile_full_wmhouse_no = user_details.house_no_street
            'streetno_purokno' => 'required', //streetno_purokno = user_details.house_no_street
            'barangay' => 'required', //barangay = user_details.brangay
            'citymunicipality' => 'required', //citymunicipality = user_details.province_state
            'district' => 'nullable', //district = user_details.district
            'province' => 'required', //province = user_details.province_state
            'region' => 'nullable', //region = N/A
            'mobileno' => 'nullable', //mobileno = user_accounts.mobile_number and user_details.contact_no
            // 'vw_farmerprofile_full_wmeducation' => 'nullable', //vw_farmerprofile_full_wmeducation = N/A
            'birthdateyyyy_mm_dd' => [
                'required'
            ], //birthdateyyyy_mm_dd = user_details.birth_date
            'placeofbirth' => 'max:50', //placeofbirth = user_details.place_of_birth
            'nationality' => [
                'required',
                'exists:nationalities,description'
            ],
            'profession' => [
                'required',
                'exists:natures_of_work,description'
            ],
            'sourceoffunds' => [
                'required',
                'exists:source_of_funds,description'
            ],
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

    /**
     * @param Failure $failure
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $key => $fail) {
            $remark = [];
            $values = $fail->values();
            $remark['remarks']['row'] = $fail->row();
            $remark['remarks']['errors'] = $fail->errors();
            $data = array_merge($remark, $fail->values());
            $this->fails->push($data);
        }
        
    }

    /**
     * @param $e
     */
    public function onError(\Throwable $e)
    {
        dd('asd');
        return Excel::store(new FailedUploadExport($failures), 'failedUploadList.xlsx');
    }

    private function setupUserAccount($row)
    {
        $rsbsa = preg_replace("/[^0-9]/", "", $row['rsbsa_reference_number']);
        $password = $rsbsa;
        $pin = substr($rsbsa, -4); //last 4 chars of rsbsa_number
        
        $farmer = [
            'rsbsa_number' => $rsbsa,
            'password' => bcrypt($password),
            'pin_code' => bcrypt($pin),
            'tier_id' => AccountTiers::tier1,
            'account_number' => $this->userAccountNumbers->generateNo(),
            'mobile_number' => $row['mobileno'],
            'user_created' => $this->userId,
        ];

        return $this->userAccounts->create($farmer);
    }

    private function setupUserProfile($userId, $row)
    {
        $marital = isset($row['civilstatus'])&& $row['civilstatus'] ? $this->maritalStatus[ucwords(strtolower($row['civilstatus']))] : null;
        $national = isset($row['nationality'])&& $row['nationality'] ? $this->nationality[ucwords(strtolower($row['nationality']))] : null;
        $profession = isset($row['profession'])&& $row['profession'] ? $this->profession[ucwords(strtolower($row['profession']))] : null;
        $sourceoffund = isset($row['sourceoffunds'])&& $row['sourceoffunds'] ? $this->sourceOfFund[ucwords(strtolower($row['sourceoffunds']))] : null;
        $dob = is_numeric($row['birthdateyyyy_mm_dd']) ? \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['birthdateyyyy_mm_dd'])) : \Carbon\Carbon::parse(strtotime($row['birthdateyyyy_mm_dd']));

        $profile = [
            'entity_id' => null,
            'user_account_id' => $userId,
            'title' => null,
            'last_name' => $row['lastname'],
            'first_name' => $row['firstname'],
            'middle_name' => $row['middlename'],
            'name_extension' => $row['extensionname'],
            'birth_date' => $dob,
            'place_of_birth' => $row['placeofbirth'],
            'marital_status_id' => $marital,
            'nationality_id' => $national,
            'encoded_nationality' => null,
            'occupation' => null,
            'house_no_street' => $row['streetno_purokno'],
            'barangay' => $row['barangay'],
            'city' => $row['citymunicipality'],
            'province_state' => $row['province'],
            'municipality' => $row['citymunicipality'],
            'country_id' => null,
            'postal_code' => null,
            'nature_of_work_id' => $profession,
            'encoded_nature_of_work' => null,
            'source_of_fund_id' => $sourceoffund,
            'encoded_source_of_fund' => null,
            'mother_maidenname' => $row['mothermaidenname'],
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
            'user_created' => $this->userId,
            'user_updated' => $this->userId,
            'id_number' => $row['idnumber'],
            'government_id_type' => $row['govtidtype'],
            'district' => $row['district'],
        ];

        $this->userDetail->create($profile);
    }

    private function setupUserBalance(string $userId)
    {
        $balance = [
            'user_account_id' => $userId,
            'available_balance' => 0,
            'currency_id' => Currencies::philippinePeso,
            'user_created' => $this->userId,
            'user_updated' => $this->userId,
        ];

        $this->userBalance->create($balance);
    }

    public function isExistingInFile(string $fname, string $mname, string $lname, string $birthday, $rowNumber, $row)
    {
        if ($this->infos->contains("$fname $mname $lname $birthday")) {
            return true;
        }
        
        $this->infos->push("$fname $mname $lname $birthday");
        
        return false;
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
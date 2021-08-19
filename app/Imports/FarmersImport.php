<?php

namespace App\Imports;

use Illuminate\Support\Model;
use Maatwebsite\Excel\Concerns\WithChunkReading;
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
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Enums\AccountTiers;
use App\Enums\Currencies;
use Hash;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use App\Rules\RSBSARule;
use App\Rules\MobileNumber;

class FarmersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError, WithEvents, WithChunkReading
{
    use RegistersEventListeners;

    private $userId;
    private $fails;
    private $successes;
    private IUserAccountRepository $userAccounts;
    private IUserAccountNumberRepository $userAccountNumbers;
    private IMaritalStatusRepository $maritalStatus;
    private IUserBalanceRepository $userBalance;

    public function __construct(IUserAccountRepository $userAccounts,
                                IUserDetailRepository $userDetail,
                                IUserAccountNumberRepository $userAccountNumbers,
                                IMaritalStatusRepository $maritalStatus,
                                IUserBalanceRepository $userBalance,
                                $authUser)
    {
        $this->userId = $authUser;
        $this->userAccounts = $userAccounts;
        $this->userDetail = $userDetail;
        $this->userAccountNumbers = $userAccountNumbers;
        $this->maritalStatus = $maritalStatus;
        $this->userBalance = $userBalance;
        $this->fails = collect();
        $this->successes = collect();
    }

    // /**
    //  * @return int
    //  */
    // public function headingRow(): int
    // {
    //     return 2;
    // }

    /**
    * @param Collection $rows
    */
    public function model(array $row)
    {
        $user = $this->setupUserAccount($row);
        $this->setupUserProfile($user->id, $row);
        $this->setupUserBalance($user->id);
        
        $usr = ['account_number' => $user->account_number];

        $this->successes->push(array_merge($usr, $row));
    }

    public function rules(): array
    {
        return [
            'vw_farmerprofile_full_wmrsbsa_no' => [
                'required',
                'unique:user_accounts,rsbsa_number',
                new RSBSARule()
            ], //vw_farmerprofile_full_wmrsbsa_no = user_accounts.rsbsa_number
            'vw_farmerprofile_full_wmfname' => [
                'required',
                'max:50',
                Rule::unique('vw_farmerprofile_full_wmfname', 'vw_farmerprofile_full_wmmname', 'vw_farmerprofile_full_wmlname', 'vw_farmerprofile_full_wmbirthdate')
            ], //vw_farmerprofile_full_wmfname = user_details.first_name
            'vw_farmerprofile_full_wmmname' => [
                'sometimes',
                'max:50'
            ], //vw_farmerprofile_full_wmmname = user_details.middle_name
            'vw_farmerprofile_full_wmlname' => [
                'required',
                'max:50'
            ], //vw_farmerprofile_full_wmlname = user_details.last_name
            'vw_farmerprofile_full_wmext_name' => [
                'sometimes',
                'max:50'
            ], //vw_farmerprofile_full_wmext_name = user_details.name_extension
            'vw_farmerprofile_full_wmmother_maiden_name' => [
                'required'
            ], //vw_farmerprofile_full_wmmother_maiden_name = user_details.mother_maidenname
            'vw_farmerprofile_full_wmsex' => 'nullable', //vw_farmerprofile_full_wmsex = N/A
            'vw_farmerprofile_full_wmhouse_no' => [
                'max:100'
            ], //vw_farmerprofile_full_wmhouse_no = user_details.house_no_street
            'vw_farmerprofile_full_wmstreet' => 'required', //vw_farmerprofile_full_wmstreet = user_details.house_no_street
            'vw_farmerprofile_full_wmbgyname' => 'required', //vw_farmerprofile_full_wmbgyname = user_details.brangay
            'vw_farmerprofile_full_wmmunname' => 'required', //vw_farmerprofile_full_wmmunname = user_details.province_state
            'vw_farmerprofile_full_wmprovname' => 'required', //vw_farmerprofile_full_wmprovname = user_details.province_state
            'vw_farmerprofile_full_wmregshortname' => 'nullable', //vw_farmerprofile_full_wmregshortname = N/A
            'vw_farmerprofile_full_wmcontact_num' => 'nullable', //vw_farmerprofile_full_wmcontact_num = user_accounts.mobile_number and user_details.contact_no
            'vw_farmerprofile_full_wmeducation' => 'nullable', //vw_farmerprofile_full_wmeducation = N/A
            'vw_farmerprofile_full_wmbirthdate' => [
                'required'
            ], //vw_farmerprofile_full_wmbirthdate = user_details.birth_date
            'birthplace' => 'max:50', //birthplace = user_details.place_of_birth
            'vw_farmerprofile_full_wmcivil_status' => [
                'required',
                'exists:marital_status,description'
            ], //vw_farmerprofile_full_wmcivil_status = user_details.marital_status_id
            'vw_farmerprofile_full_wmreligion' => 'nullable', //vw_farmerprofile_full_wmreligion = N/A
            'vw_farmerprofile_full_wmgross_income_farming' => 'nullable', //vw_farmerprofile_full_wmgross_income_farming = N/A
            'vw_farmerprofile_full_wmgross_income_nonfarming' => 'nullable', //vw_farmerprofile_full_wmgross_income_nonfarming = N/A
            'govid.id_type' => 'nullable', //govid.id_type = N/A
            'vw_farmerprofile_full_wmgov_id_num' => 'nullable', //vw_farmerprofile_full_wmgov_id_num = N/A
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    /**
     * @param Failure $failure
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $key => $fail) {
            $values = $fail->values();
            $remark['remarks']['row'] = $fail->row();
            $remark['remarks']['errors'][] = $fail->errors()[0];
        }
        
        $data = array_merge($remark, $fail->values());
        $this->fails->push($data);
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
        $rsbsa = preg_replace("/[^0-9]/", "", $row['vw_farmerprofile_full_wmrsbsa_no']);
        $password = $rsbsa;
        $pin = substr($rsbsa, -4); //last 4 chars of rsbsa_number
        
        $farmer = [
            'rsbsa_number' => $rsbsa,
            'password' => Hash::make($password),
            'pin_code' => Hash::make($pin),
            'tier_id' => AccountTiers::tier1,
            'account_number' => $this->userAccountNumbers->generateNo(),
            'mobile_number' => $row['vw_farmerprofile_full_wmcontact_num'],
            'user_created' => $this->userId,
        ];

        return $this->userAccounts->create($farmer);
    }

    private function setupUserProfile($userId, $row)
    {
        $marital = $this->maritalStatus->getByDescription($row['vw_farmerprofile_full_wmcivil_status'])->id;
        $dob = is_numeric($row['vw_farmerprofile_full_wmbirthdate']) ? \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['vw_farmerprofile_full_wmbirthdate'])) : \Carbon\Carbon::parse(strtotime($row['vw_farmerprofile_full_wmbirthdate']));

        $profile = [
            'entity_id' => null,
            'user_account_id' => $userId,
            'title' => null,
            'last_name' => $row['vw_farmerprofile_full_wmlname'],
            'first_name' => $row['vw_farmerprofile_full_wmfname'],
            'middle_name' => $row['vw_farmerprofile_full_wmmname'],
            'name_extension' => $row['vw_farmerprofile_full_wmext_name'],
            'birth_date' => $dob,
            'place_of_birth' => $row['birthplace'],
            'marital_status_id' => $marital,
            'nationality_id' => null,
            'encoded_nationality' => null,
            'occupation' => null,
            'house_no_street' => $row['vw_farmerprofile_full_wmhouse_no'] . ' ' . $row['vw_farmerprofile_full_wmstreet'],
            'barangay' => $row['vw_farmerprofile_full_wmbgyname'],
            'city' => $row['vw_farmerprofile_full_wmmunname'],
            'province_state' => $row['vw_farmerprofile_full_wmprovname'],
            'municipality' => $row['vw_farmerprofile_full_wmmunname'],
            'country_id' => null,
            'postal_code' => null,
            'nature_of_work_id' => null,
            'encoded_nature_of_work' => null,
            'source_of_fund_id' => null,
            'encoded_source_of_fund' => null,
            'mother_maidenname' => $row['vw_farmerprofile_full_wmmother_maiden_name'],
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
            'user_updated' => $this->userId
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

    public function getFails()
    {
        return $this->fails;
    }

    public function getSuccesses()
    {
        return $this->successes;
    }
}
<?php

namespace App\Imports\Farmers;

use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class FarmerAccountImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError, WithEvents, WithChunkReading, WithBatchInserts
{
    use RegistersEventListeners, RemembersRowNumber;

    /**
    * @param Collection $collection
    */    
    private $fails;
    private $successes;
    private IUserDetailRepository $userDetail;
    
    public function __construct(IUserDetailRepository $userDetail)
    {
        $this->fails = collect();
        $this->successes = collect();
        $this->userDetail = $userDetail;
    }

    public function collection(Collection $collection)
    {
        foreach($collection as $index => $entry) {
            // IF index is 0 skip because of headers
            if($index != 0) {
                try {
                    if(!$this->userDetail->getIsExistingByNameAndBirthday($entry->firstname, $entry->middlename, $entry->lastname, $entry->birthdateyyyy_mm_dd)) {
                        $this->successes->push($entry);
                    } else {
                        $remarks = [
                            'remarks' => 'Row ' . $this->getRowNumber() . ", Duplicate Data"
                        ];
                        $this->fails->push(array_merge($remarks, $entry));
                    }
                } catch (\Exception $e) {
                    $remarks = [
                        'remarks' => 'Row ' . $this->getRowNumber() . ", " . $e->getMessage()
                    ];
                    $this->fails->push(array_merge($remarks, $entry->toArray()));
                }
            }
        }
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
            'birthdateyyyy_mm_dd' => 'Birth Date is required',
            'placeofbirth' => 'Place of Birth is required',
            'mobileno' => 'Mobile Number is required',
            'sex' => 'Sex is required',
            'nationality' => 'Nationality is required',
            'profession' => 'Profession is required',
            'sourceoffunds' => 'Source of funds is required',
            'mothermaidenname' => 'Mothers Maiden Name is required',
            'of_farm_parcel' => 'Farm Parcel is required',
            'total_farm_area_ha' => 'Total Farm area is required',
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
        foreach ($failures as $key => $fail) {
            $remark = "Row " . $fail->row() . ", ";
            foreach($fail->errors() as $error) {
                $remark = $remark . " " . $error;
            }
            $remarks = [
                'remarks' => $remark
            ];
            $data = array_merge($remarks, $fail->values());
            $this->fails->push($data);
        }
    }

    public function onError(\Throwable $e)
    {
        // dd($e);
        // return Excel::store(new FailedUploadExport($failures), 'failedUploadList.xlsx');
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

<?php

namespace App\Imports\Farmers;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Repositories\InReceiveFromDBP\IInReceiveFromDBPRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategoryIds;
use App\Rules\RSBSARule;
use App\Rules\RSBSAExistsRule;

class SubsidyImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError, WithChunkReading, WithBatchInserts
{
    use RegistersEventListeners;

    private $userId;
    private $fails;
    private $successes;
    private $rsbsaNumbers;
    private IReferenceNumberService $referenceNumberService;
    private IInReceiveFromDBPRepository $inReceiveFromDBP;
    private IUserAccountRepository $userAccountRepository;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private IUserBalanceInfoRepository $userBalanceInfo;

    public function __construct(IUserAccountRepository $userAccountRepository,
                                IInReceiveFromDBPRepository $inReceiveFromDBP,
                                IUserTransactionHistoryRepository $userTransactionHistoryRepository,
                                IUserBalanceInfoRepository $userBalanceInfo,
                                IReferenceNumberService $referenceNumberService,
                                $authUser,
                                $filename)
    {
        $this->inReceiveFromDBP = $inReceiveFromDBP;
        $this->userAccountRepository = $userAccountRepository;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->referenceNumberService = $referenceNumberService;
        $this->userId = $authUser;
        $this->filename = $filename;
        $this->fails = collect();
        $this->successes = collect();
        $this->rsbsaNumbers = collect();
    }
    
    public function model(array $row)
    {
        $rsbsa = preg_replace("/[^0-9]/", "", $row['vw_farmerprofile_full_wmrsbsa_no']);
        $referenceId = $this->referenceNumberService->generate(ReferenceNumberTypes::ReceiveMoneyDBP);
        $user = $this->userAccountRepository->getUserAccountByRSBSANo($rsbsa);
        $transaction = $this->addReceiveFromDBP($user, $referenceId, $row);
        $this->addTransaction($user, $referenceId, $transaction, $row);
        $this->addUserBalance($user, $referenceId, $row['amount']);

        // $usr = ['account_number' => $user->account_number];

        // $this->successes->push(array_merge($usr, $row));
        $this->successes->push($row);
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

    public function rules(): array
    {
        return [
            'vw_farmerprofile_full_wmrsbsa_no' => [
                'required',
                new RSBSAExistsRule(),
                new RSBSARule(),
                function($attribute, $value, $onFailure) {
                    if (in_array($value, $this->rsbsaNumbers->toArray())) {
                         $onFailure('RSBSA Duplicate' . implode(', ',$this->rsbsaNumbers->toArray()));
                    }
                    
                    $this->rsbsaNumbers->push($value);
                }
            ], //vw_farmerprofile_full_wmrsbsa_no = user_accounts.rsbsa_number
            'amount' => [
                'required',
                'numeric'
            ]
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
    
    public function batchSize(): int
    {
        return 500;
    }

    private function addReceiveFromDBP($user, $referenceId, $row)
    {
        $data = [
            "user_account_id" => $user->id,
            "reference_number" => $referenceId,
            "total_amount" => (float)$row['amount'],
            "transaction_date" => date('Y-m-d H:i:s'),
            "transction_category_id" => TransactionCategoryIds::dbpSubsidy,
            "transaction_remarks" => '',
            "file_name" => $this->filename,
            "status" => 'SUCCESS',
            "user_created" => $this->userId,
            'user_updated' => $this->userId,
        ];

        return $this->inReceiveFromDBP->create($data);
    }

    private function addTransaction($user, $referenceId, $transaction, $row)
    {
        $this->userTransactionHistoryRepository->create([
            'user_account_id' => $user->id,
            'transaction_id' => $transaction->id,
            'reference_number' => $referenceId,
            'total_amount' => (float)$row['amount'],
            'transaction_category_id' => TransactionCategoryIds::dbpSubsidy,
            'user_created' => $this->userId,
            'user_updated' => $this->userId,
            'transaction_date' => $transaction->transaction_date
        ]);
    }

    private function addUserBalance($user, $amount)
    {
        $currentBalance = $this->userBalanceInfo->getUserBalance($user->id);
        $total = (float)$currentBalance + (float)$amount;
        return $this->userBalanceInfo->updateUserBalance($user->id, $total);
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

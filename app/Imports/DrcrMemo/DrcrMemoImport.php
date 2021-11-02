<?php

namespace App\Imports\DrcrMemo;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Enums\TransactionStatuses;
use App\Enums\ReferenceNumberTypes;
use App\Enums\Currencies;
use App\Enums\DrcrStatus;
use App\Enums\TransactionCategoryIds;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use Carbon\Carbon;

class DrcrMemoImport implements ToCollection, WithValidation, WithHeadingRow
{

    protected $numbers;
    private $user;
    private $controlNumber;
    private IReferenceNumberService $referenceNumberService;
    private IDrcrMemoRepository $drcrMemoRepository;
    private IUserAccountRepository $userAccountRepository;
    private IUserBalanceInfoRepository $userBalanceRepository;
    private IUserTransactionHistoryRepository $userTransHistory;


    public function __construct(
        $user,
        IReferenceNumberService $referenceNumberService,
        IDrcrMemoRepository $drcrMemoRepository,
        IUserAccountRepository $userAccountRepository,
        IUserBalanceInfoRepository $userBalanceRepository,
        IUserTransactionHistoryRepository $userTransHistory,
        $controlNumber
    ) {
        $this->numbers = collect();
        $this->user = $user;
        $this->referenceNumberService = $referenceNumberService;
        $this->drcrMemoRepository = $drcrMemoRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->userBalanceRepository = $userBalanceRepository;
        $this->userTransHistory = $userTransHistory;
        $this->controlNumber = $controlNumber;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $item) {
            $customer = $this->userAccountRepository->getUserByAccountNumber($item['account_number']);;

            $memoType = ReferenceNumberTypes::CR;
            $refNo = $this->referenceNumberService->generate($memoType);

            $newMemo = [
                'user_account_id' => $customer->id,
                'type_of_memo' => $memoType,
                'reference_number' => $refNo,
                'transaction_category_id' => TransactionCategoryIds::crMemo,
                'amount' => $item['amount'],
                'currency_id' => Currencies::philippinePeso,
                'category' => $item['category'],
                'description' => $item['transaction_description'],
                'status' => TransactionStatuses::success,
                'created_by' => $this->user->id,
                'user_created' => $this->user->id,
                'control_number_id' => $this->controlNumber,
            ];

            //Create memo
            $drcr = $this->drcrMemoRepository->create($newMemo);

            //Adjust Balance
            $balance = $this->userBalanceRepository->getUserBalance($customer->id);
            $balance += $item['amount'];
            $this->userBalanceRepository->updateUserBalance($customer->id, $balance);

            //Log transaction
            $this->userTransHistory->log($drcr->user_account_id, $drcr->transaction_category_id, $drcr->id, $drcr->reference_number, $drcr->amount, Carbon::parse($drcr->updated_at), $drcr->user_created);
        }
    }

    public function rules(): array
    {
        return [
            'account_number' => [
                'required',
                'exists:user_accounts,account_number',
                function ($attribute, $value, $fail) {
                    if ($this->numbers->contains($value)) {
                        $fail("The $attribute : $value is already existing in the file.");
                    } else {
                        $this->numbers->push($value);
                    }
                },
            ],
            'type_of_memo' => [
                'required',
                'in:CR'
            ],
            'category' => [
                'required'
            ],
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:100000'
            ],
            'currency' => [
                'required',
                'exists:currencies,code'
            ],
            'transaction_description' => [
                'sometimes'
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'account_number.exists' => ':attribute doesn\'t exist.',
            'type_of_memo.in' => 'Invalid Type Of Memo',
        ];
    }
}

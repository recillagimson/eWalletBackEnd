<?php

namespace App\Services\DrcrMemo;

use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategoryIds;
use App\Models\UserAccount;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;

class DrcrMemoService implements IDrcrMemoService
{
    private IDrcrMemoRepository $drcrMemoRepository;
    private IReferenceNumberService $referenceNumberService;

    public function __construct(IDrcrMemoRepository $drcrMemoRepository, IReferenceNumberService $referenceNumberService) {
      $this->drcrMemoRepository = $drcrMemoRepository;
      $this->referenceNumberService = $referenceNumberService;
    }

    public function getList()
    {
      return $this->drcrMemoRepository->getAll();
    }

    public function store($data, UserAccount $user)
    {
    
    if ($data['typeOfMemo'] == ReferenceNumberTypes::DR)  $typeOfMemo = ReferenceNumberTypes::DR;
    $typeOfMemo = ReferenceNumberTypes::CR;
     
    return $this->drcrMemoRepository->create([
        'user_account_id' => $user->id,
        'type_of_memo' => $data['typeOfMemo'],
        'reference_number' => $this->getReference($data),
        'transaction_category_id' => TransactionCategoryIds::drMemo,
        'amount',
        'currency_id',
        'category',
        'description',
        'status',
        'created_by',
        'created_date',
      ]);  
    }



  private function getReference($data)
  {
    if($data['typeOfMemo'] == ReferenceNumberTypes::DR) return $this->referenceNumberService->generate(ReferenceNumberTypes::DR);
    return $this->referenceNumberService->generate(ReferenceNumberTypes::CR);
  }


}

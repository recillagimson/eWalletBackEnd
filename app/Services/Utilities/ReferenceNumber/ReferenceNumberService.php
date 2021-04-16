<?php

namespace App\Services\Utilities\ReferenceNumber;

use App\Enums\ReferenceNumberTypes;
use App\Repositories\InAddMoney\IInAddMoneyRepository;

class ReferenceNumberService implements IReferenceNumberService
{
    public IInAddMoneyRepository $addMoneys;

    public function __construct(IInAddMoneyRepository $addMoneys) {
        $this->addMoneys = $addMoneys;
    }

    /**
     * Generates a Reference Number for Add Money
     * Web Bank with the format AB0000000
     * 
     * @return string referenceNumberWebBank
     */
    public function getAddMoneyRefNo()
    {
        $lastRefNoRow = $this->addMoneys->getLastByReferenceNumber();

        if (!isset($lastRefNoRow->reference_number)) {
            $lastRefNoRow['reference_number'] = 0;
            $lastRefNoRow = (object) $lastRefNoRow;
        }

        $lastRefNoInts = substr($lastRefNoRow->reference_number, 2);
        $latestRefNo = $lastRefNoInts + 1;

        return ReferenceNumberTypes::AddMoneyViaWebBank . str_pad($latestRefNo, 7, '0', STR_PAD_LEFT);
    }
}

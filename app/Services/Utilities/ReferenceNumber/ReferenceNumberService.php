<?php

namespace App\Services\Utilities\ReferenceNumber;

use App\Repositories\ReferenceCounter\IReferenceCounterRepository;
use App\Traits\Errors\WithTransactionErrors;
use Illuminate\Support\Str;

class ReferenceNumberService implements IReferenceNumberService
{
    use WithTransactionErrors;

    private IReferenceCounterRepository $refCounters;

    public function __construct(IReferenceCounterRepository $refCounters)
    {
        $this->refCounters = $refCounters;
    }

    public function generate(string $referenceType): string
    {
        $ref = $this->refCounters->getByCode($referenceType);
        if (!$ref) $this->transInvalid();

        $ref->counter += 1;
        $ref->save();

        return $referenceType . Str::padLeft($ref->counter, 7, '0');
    }
}

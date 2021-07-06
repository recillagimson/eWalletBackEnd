<?php

namespace App\Services\Utilities\PDF;

interface IPDFService
{
    public function generatePDFNoUserPassword(array $data, string $loadView);
}

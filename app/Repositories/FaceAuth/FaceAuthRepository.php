<?php

namespace App\Repositories\FaceAuth;

use App\Models\FaceAuthTransaction;
use App\Repositories\Repository;

class FaceAuthRepository extends Repository implements IFaceAuthRepository
{
    public function __construct(FaceAuthTransaction $model)
    {
        parent::__construct($model);
    }
}

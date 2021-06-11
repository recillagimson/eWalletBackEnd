<?php

namespace App\Repositories\DrcrMemo;

use App\Models\DrcrMemos;
use App\Repositories\Repository;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;

class DrcrMemoRepository extends Repository implements IDrcrMemoRepository
{
    public function __construct(DrcrMemos $model)
    {
        parent::__construct($model);
    }


}

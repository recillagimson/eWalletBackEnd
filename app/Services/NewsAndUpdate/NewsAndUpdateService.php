<?php

namespace App\Services\NewsAndUpdate;

use App\Repositories\NewsAndUpdate\INewsAndUpdateRepository;
use Carbon\Carbon;

class NewsAndUpdateService implements INewsAndUpdateService
{
    private INewsAndUpdateRepository $newsAndUpdateRepository;

    public function __construct(INewsAndUpdateRepository $newsAndUpdateRepository)
    {
        $this->newsAndUpdateRepository = $newsAndUpdateRepository;
    }

}

<?php

namespace App\Jobs\Farmers;

use App\Services\FarmerProfile\IFarmerProfileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BatchUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $filePath;
    private string $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $filePath, string $userId)
    {
        //
        $this->filePath = $filePath;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(IFarmerProfileService $profileService)
    {
        $profileService->batchUploadV2($this->filePath, $this->userId);
    }
}

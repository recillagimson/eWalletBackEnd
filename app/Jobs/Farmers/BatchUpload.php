<?php

namespace App\Jobs\Farmers;

use App\Services\FarmerProfile\IFarmerProfileService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class BatchUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $filePath;
    private string $userId;

    public int $timeout = 3600;

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
        try {
            Log::info('Batch Upload Parameters', ['filePath' => $this->filePath, 'userId' => $this->userId]);
            $profileService->batchUploadV2($this->filePath, $this->userId);
        } catch (Exception $e) {
            Log::error('Farmers Batch Upload Error', $e->getTrace());
        }
    }
}

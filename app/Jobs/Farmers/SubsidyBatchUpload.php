<?php

namespace App\Jobs\Farmers;

use App\Services\FarmerProfile\IFarmerProfileService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubsidyBatchUpload implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;
    public int $uniqueFor = 3600;
    public int $tries = 1;
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

    public function uniqueId(): string
    {
        return SubsidyBatchUpload::class;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(IFarmerProfileService $profileService)
    {
        try {
            Log::info('Subsidy Batch Upload Parameters', ['filePath' => $this->filePath, 'userId' => $this->userId]);
            $profileService->subsidyProcess($this->filePath, $this->userId);
        } catch (Exception $e) {
            Log::error('Farmers Subsidy Batch Upload Error', $e->getTrace());
        }
    }
}

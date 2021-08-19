<?php

namespace App\Jobs\Farmers;

use App\Services\FarmerProfile\IFarmerProfileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BatchUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private UploadedFile $file;
    private string $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UploadedFile $file, string $userId)
    {
        //
        $this->file = $file;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(IFarmerProfileService $profileService)
    {
        $profileService->processBatchUpload($this->file, $this->userId);
    }
}

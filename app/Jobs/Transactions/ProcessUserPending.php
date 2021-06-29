<?php

namespace App\Jobs\Transactions;

use App\Models\UserAccount;
use App\Services\Transaction\ITransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUserPending implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private UserAccount $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserAccount $user)
    {
        $this->user = $user->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ITransactionService $transactionService)
    {
        $transactionService->processUserPending($this->user);
    }
}

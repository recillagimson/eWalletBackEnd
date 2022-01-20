<?php

namespace App\Mail\BPI;

use App\Services\BPIService\Models\BPIPromoNotifcation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChineseNewYearPromo extends Mailable
{
    use Queueable, SerializesModels;

    private BPIPromoNotifcation $params;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(BPIPromoNotifcation $params)
    {
        //
        $this->params = $params;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): ChineseNewYearPromo
    {
        return $this->view('emails.bpi.chinese_new_year')
            ->subject('SquidPay - Cash-in Promo Notification')
            ->with([
                'firstName' => $this->params->firstName,
                'amount' => number_format($this->params->amount, 2),
                'refNo' => $this->params->refNo
            ]);
    }
}

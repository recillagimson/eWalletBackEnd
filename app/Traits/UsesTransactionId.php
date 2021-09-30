<?php

namespace App\Traits;

trait UsesTransactionId
{
    protected static function bootUsesTransactionId()
    {
        static::creating(function ($model) {
            $count = $model->count();
            $transactionId = $count;
            if($count < 10) {
                $transactionId = "DUP0000000" . $count;
            } else if($count < 100) {
                $transactionId = "DUP000000" . $count;
            } else if($count < 1000) {
                $transactionId = "DUP00000" . $count;
            } else if($count < 10000) {
                $transactionId = "DUP0000" . $count;
            } else if($count < 100000) {
                $transactionId = "DUP000" . $count;
            } else if($count < 1000000) {
                $transactionId = "DUP00" . $count;
            } else if($count < 10000000) {
                $transactionId = "DUP0" . $count;
            } else if($count < 100000000) {
                $transactionId = "DUP" . $count;
            }

            $model->transaction_id = $transactionId;
        });
    }
}
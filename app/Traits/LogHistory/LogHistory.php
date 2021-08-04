<?php
namespace App\Traits\LogHistory;

use Illuminate\Database\Eloquent\Collection;

trait LogHistory
{
    private function processData(Collection $data, $namedKey = false) {
        $processed_data = [];
        $current_balance = 0;
        $available_balance = 0;
        $current_id = "";
        foreach($data as $entry) {

            // Check if need to add balance base on user_account_id
            if($current_id != $entry->account_number) {
                $current_balance = 0;
                $available_balance = 0;
                $current_id = $entry->account_number;
            } else {
                // CHECK Transaction type for Current Balance
                if($entry->Type == 'CR') {
                    $current_balance = $current_balance + (Float) $entry->total_amount;
                } else {
                    $current_balance = $current_balance - (Float) $entry->total_amount;
                }

                // Check for available balance
                if($entry->Status == 'SUCCESS') {
                    if($entry->Type == 'CR') {
                        $available_balance = $available_balance + (Float) $entry->total_amount;
                    } else {
                        $available_balance = $available_balance - (Float) $entry->total_amount;
                    }
                }
            }

            if(!$namedKey) {
                $proc = [
                    $entry->transaction_date,
                    $entry->account_number,
                    $entry->first_name . " " . $entry->last_name,
                    strval($current_balance),
                    $entry->Type == 'CR' ? 'Credit' : 'Debit',
                    $entry->Description,
                    strval($entry->total_amount),
                    $entry->reference_number,
                    strval($available_balance),
                    $entry->Status
                ];
    
                array_push($processed_data, $proc);
            } else {
                $proc = [
                    "transaction_date" => $entry->transaction_date,
                    "customer_id" => $entry->account_number,
                    "customer_name" => $entry->first_name . " " . $entry->last_name,
                    "current_balance" => strval($current_balance),
                    "type" => $entry->Type == 'CR' ? 'Credit' : 'Debit',
                    "category" => $entry->Description,
                    "amount" => strval($entry->total_amount),
                    "transaction_description" => $entry->reference_number,
                    "available_balance" => strval($available_balance),
                    "status" => $entry->Status
                ];
    
                array_push($processed_data, $proc);
            }
        }

        return $processed_data;
    }
}
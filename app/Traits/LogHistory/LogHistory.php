<?php
namespace App\Traits\LogHistory;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Illuminate\Database\Eloquent\Collection;

trait LogHistory
{
    private function processData(Collection $data, $namedKey = false)
    {
        $processed_data = [];
        $current_balance = 0;
        $available_balance = 0;
        $current_id = "";
        foreach ($data as $entry) {
            // Check if need to add balance base on user_account_id
            if ($current_id != $entry->account_number) {
                $current_balance = 0;
                $available_balance = 0;
                $current_id = $entry->account_number;

                // CHECK Transaction type for Current Balance
                if ($entry->Type == 'CR') {
                    $current_balance = $current_balance + (float)$entry->total_amount;
                } else {
                    $current_balance = $current_balance - (float)$entry->total_amount;
                }

                // Check for available balance
                if ($entry->Status == 'SUCCESS') {
                    if ($entry->Type == 'CR') {
                        $available_balance = $available_balance + (float)$entry->total_amount;
                    } else {
                        $available_balance = $available_balance - (float)$entry->total_amount;
                    }
                }
            } else {
                // CHECK Transaction type for Current Balance
                if ($entry->Type == 'CR') {
                    $current_balance = $current_balance + (float)$entry->total_amount;
                } else {
                    $current_balance = $current_balance - (float)$entry->total_amount;
                }

                // Check for available balance
                if ($entry->Status == 'SUCCESS') {
                    if ($entry->Type == 'CR') {
                        $available_balance = $available_balance + (float)$entry->total_amount;
                    } else {
                        $available_balance = $available_balance - (float)$entry->total_amount;
                    }
                }
            }

            if (!$namedKey) {
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
                    $entry->Status,
                    Carbon::parse($entry->transaction_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A'),
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

    private function processDataSuperVisor(Collection $data, $namedKey = false)
    {
        $processed_data = [];
        $current_balance = 0;
        $available_balance = 0;
        $current_id = "";

        foreach ($data as $entry) {
            // Check if need to add balance base on user_account_id
            if ($current_id != $entry->account_number) {
                $current_balance = 0;
                $available_balance = 0;
                $current_id = $entry->account_number;

                // CHECK Transaction type for Current Balance
                if ($entry->Type == 'CR') {
                    $current_balance = $current_balance + (float)$entry->total_amount;
                } else {
                    $current_balance = $current_balance - (float)$entry->total_amount;
                }

                // Check for available balance
                if ($entry->Status == 'SUCCESS') {
                    if ($entry->Type == 'CR') {
                        $available_balance = $available_balance + (float)$entry->total_amount;
                    } else {
                        $available_balance = $available_balance - (float)$entry->total_amount;
                    }
                }
            } else {
                // CHECK Transaction type for Current Balance
                if ($entry->Type == 'CR') {
                    $current_balance = $current_balance + (float)$entry->total_amount;
                } else {
                    $current_balance = $current_balance - (float)$entry->total_amount;
                }

                // Check for available balance
                if ($entry->Status == 'SUCCESS') {
                    if ($entry->Type == 'CR') {
                        $available_balance = $available_balance + (float)$entry->total_amount;
                    } else {
                        $available_balance = $available_balance - (float)$entry->total_amount;
                    }
                }
            }

            // <th>Account Number</th>
            // <th>Last Name</th>
            // <th>First Name</th>
            // <th>Middle Name</th>
            // <th>Type</th>
            // <th>Reference Number</th>
            // <th>Amount</th>
            // <th>Category</th>
            // <th>Description</th>
            // <th>Remarks</th>
            // <th>Status</th>
            // <th>Created By User</th>
            // <th>Approved By</th>
            // <th>Decliend By</th>
            // <th>Transaction Date</th>
            // <th>Approved at</th>
            // <th>Declined at</th>

            if (!$namedKey) {
                $proc = [
                    $entry->account_number,
                    $entry->last_name,
                    $entry->first_name,
                    $entry->middle_name,
                    $entry->type_of_memo,
                    $entry->reference_number,
                    $entry->amount,
                    $entry->category,
                    $entry->description,
                    $entry->remarks,
                    $entry->status,
                    $entry->user_created_name,
                    $entry->approved_by_name,
                    $entry->declined_by_name,
                    $entry->transaction_date,
                    $entry->approved_at,
                    $entry->declined_at,
                    Carbon::parse($entry->transaction_date)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A'),
                    $entry->approved_at ? Carbon::parse($entry->approved_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : '',
                    $entry->declined_at ? Carbon::parse($entry->declined_at)->setTimezone('Asia/Manila')->format('m/d/Y h:i:s A') : '',
                ];
                array_push($processed_data, $proc);
            } else {
                $proc = [
                    $entry->account_number,
                    $entry->last_name,
                    $entry->first_name,
                    $entry->middle_name,
                    $entry->type_of_memo,
                    $entry->reference_number,
                    $entry->amount,
                    $entry->category,
                    $entry->description,
                    $entry->remarks,
                    $entry->status,
                    $entry->user_created_name,
                    $entry->approved_by_name,
                    $entry->declined_by_name,
                    $entry->transaction_date,
                    $entry->approved_at,
                    $entry->declined_at,
                    Carbon::parse($entry->transaction_date)->setTimezone('Asia/Manila')->format('Y-m-d'),
                    $entry->approved_at ? Carbon::parse($entry->approved_at)->setTimezone('Asia/Manila')->format('Y-m-d') : '',
                    $entry->declined_at ? Carbon::parse($entry->declined_at)->setTimezone('Asia/Manila')->format('Y-m-d') : '',
                ];
                array_push($processed_data, $proc);
            }
            
        }

        return $processed_data;
    }

    private function processDataWithRunningBalance(Collection $data, $namedKey = false)
    {
        $processed_data = [];
        $current_balance = 0;
        $available_balance = 0;
        $current_id = "";

        foreach ($data as $entry) {
            // Check if need to add balance base on user_account_id
            if ($current_id != $entry->account_number) {
                $current_balance = 0;
                $available_balance = 0;
                $current_id = $entry->account_number;

                // CHECK Transaction type for Current Balance
                if ($entry->transaction_type == 'CR') {
                    $current_balance = $current_balance + (float)$entry->total_amount;
                } else {
                    $current_balance = $current_balance - (float)$entry->total_amount;
                }

                // Check for available balance
                if ($entry->Status == 'SUCCESS') {
                    if ($entry->transaction_type == 'CR') {
                        $available_balance = $available_balance + (float)$entry->total_amount;
                    } else {
                        $available_balance = $available_balance - (float)$entry->total_amount;
                    }
                }
            } else {
                // CHECK Transaction type for Current Balance
                if ($entry->transaction_type == 'CR') {
                    $current_balance = $current_balance + (float)$entry->total_amount;
                } else {
                    $current_balance = $current_balance - (float)$entry->total_amount;
                }

                // Check for available balance
                if ($entry->Status == 'SUCCESS') {
                    if ($entry->transaction_type == 'CR') {
                        $available_balance = $available_balance + (float)$entry->total_amount;
                    } else {
                        $available_balance = $available_balance - (float)$entry->total_amount;
                    }
                }
            }



            if (!$namedKey) {
                $proc = [
                    'transaction_date' => $entry->manila_time_transaction_date,
                    'account_number' => $entry->account_number,
                    'first_name' => $entry->first_name,
                    'last_name' => $entry->last_name,
                    'middle_name' => $entry->middle_name,
                    'status' => $entry->status,
                    'transaction_type' => $entry->transaction_type,
                    'category' => $entry->category,
                    'description' => $entry->description,
                    'remarks' => $entry->remarks,
                    'reference_number' => $entry->reference_number,
                    'user_created' => $entry->user_created,
                    'approved_by_name' => $entry->approved_by_name,
                    'declined_by_name' => $entry->declined_by_name,
                    'approved_at' => $entry->manila_time_approved_at,
                    'declined_at' => $entry->manila_time_declined_at,
                    'current_balance' => $entry->current_balance,
                    'available_balance' => $available_balance,
                ];

                array_push($processed_data, $proc);
            } else {
                $proc = [
                    $entry->manila_time_transaction_date,
                    $entry->account_number,
                    $entry->first_name,
                    $entry->last_name,
                    $entry->middle_name,
                    $entry->status,
                    $entry->transaction_type,
                    $entry->category,
                    $entry->description,
                    $entry->remarks,
                    $entry->reference_number,
                    $entry->user_created,
                    $entry->approved_by_name,
                    $entry->declined_by_name,
                    $entry->manila_time_approved_at,
                    $entry->manila_time_declined_at,
                    $entry->current_balance,
                    $available_balance,
                ];
                array_push($processed_data, $proc);
            }
            
        }

        return $processed_data;
    }
}

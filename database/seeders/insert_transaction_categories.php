<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class insert_transaction_categories extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
  
        DB::table('transaction_categories')->delete();
            DB::table('transaction_categories')->insert([

                ['id' => '0ec41025-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Send Money to Bank','name' => 'WITHDRAWUBPINSTAPAY','description' => 'Withdraw Cash via UBP Instapay','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec432e7-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Send Money to Bank','name' => 'WITHDRAWUBPPESONET','description' => 'Withdraw Cash via UBP Pesonet','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec4338c-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Send Money','name' => 'MXTRANSFER','description' => 'Transfer Money From a Merchant Account to other Account','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec433f6-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Send Money','name' => 'CASHOUT','description' => 'Cashout from account to settlement Account','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec43457-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Add Money via Dragonpay','name' => 'CASHINDRAGONPAY','description' => 'Cash-in via Dragon Pay','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec434b6-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Debit Memo','name' => 'DR_MEMO','description' => 'Debit Memo for Adjustment or Commission','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec43514-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Credit Memo','name' => 'CR_MEMO','description' => 'Credit Memo for Adjustment or Commission','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec4356d-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Refund','name' => 'POSREFUND','description' => 'Refund','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec435d1-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Manual Entry','name' => 'POSMANUAL','description' => 'POS manual Transaction','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec4362b-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'POS Payment','name' => 'POSPAYMERCHANT','description' => 'POS Pay Merchant','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec43688-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'POS Payment','name' => 'POSPAYDRIVER','description' => 'POS Pay Driver','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec436e0-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Add Money to Card via POS','name' => 'POSADDFUNDS','description' => 'POS Add Funds to Card','status' => '1','user_created' => 'Migration Team'],
                ['id' => '0ec43738-9131-11eb-b44f-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Format Card','name' => 'POSFORMAT','description' => 'POS Format Card','status' => '1','user_created' => 'Migration Team'],
                ['id' => 'edf4d5d0-9299-11eb-9663-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Buy Load','name' => 'CXLOAD','description' => 'Purchase Load','status' => '1','user_created' => 'Migration Team'],
                ['id' => '1a86b905-929a-11eb-9663-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Send Money','name' => 'CXSEND','description' => 'Send Money to Squidpay Account','status' => '1','user_created' => 'Migration Team'],
                ['id' => 'c5b62dbd-95a0-11eb-8473-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Bills Payment','name' => 'BILLS','description' => 'Bills Payment','status' => '1','user_created' => 'Migration Team'],
                ['id' => 'b1792f37-929c-11eb-9663-1c1b0d14e211','old_transaction_category_id' => '','title' => 'Receive Money','name' => 'CXRECEIVE','description' => 'Receive Money from Squidpay Account','status' => '1','user_created' => 'Migration Team']
                
            ]);
    }
    

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInAddMoneyUpbDirectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_add_money_upb_direct', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->string('reference_number', 50)->unique();
            $table->decimal('total_amount', 10, 3);
            $table->datetime('transaction_date');
            $table->uuid('transaction_category_id');
            $table->string('transaction_remarks', 100);
            $table->string('status', 10);
            $table->string('ubp_reference', 50)->unique();
            $table->longText('transaction_response');
            $table->uuid('user_created');
            $table->uuid('user_updated')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_add_money_upb_directs');
    }
}

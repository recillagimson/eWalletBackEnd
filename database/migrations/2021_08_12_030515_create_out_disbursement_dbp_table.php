<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutDisbursementDbpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_disbursement_dbps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->string('reference_number', 50)->unique();
            $table->decimal('total_amount', 19, 6);
            $table->string('status', 20);
            $table->datetime('transaction_date')->default(DB::raw('CURRENT_TIMESTAMP'));
 $table->uuid('transaction_category_id')->references('id')->on('transaction_categories')->onDelete('restrict');
            $table->string('transaction_remarks', 50)->nullable();
            $table->string('disbursed_by', 36)->nullable();
            $table->uuid('user_created')->nullable();
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
        Schema::dropIfExists('out_disbursement_dbps');
    }
}

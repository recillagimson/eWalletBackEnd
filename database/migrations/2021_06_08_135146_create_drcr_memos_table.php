<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrcrMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drcr_memos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_account_id');
            $table->foreign('user_account_id')->references('id')->on('user_accounts');
            $table->string('type_of_memo', 6);
            $table->string('reference_number', 50)->unique();
            $table->uuid('transaction_category_id');
            $table->foreign('transaction_category_id')->references('id')->on('transaction_categories');
            $table->decimal('amount', 19, 6);
            $table->string('currency_id', 36);
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->string('category', 15)->nullable();
            $table->string('description', 100)->nullable();
            $table->string('status', 20);
            $table->uuid('created_by')->nullable();
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_date')->nullable();
            $table->uuid('declined_by')->nullable();
            $table->timestamp('declined_date')->nullable();
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
        Schema::dropIfExists('drcr_memos');
    }
}

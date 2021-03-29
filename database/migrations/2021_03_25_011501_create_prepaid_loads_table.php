<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrepaidLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepaid_loads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('old_prepaid_load_id');
            $table->string('prepaid_type');
            $table->string('reward_keyword')->unique();
            $table->string('amax_keyword')->unique();
            $table->decimal('amount', 10, 3);
            $table->boolean('status');
            $table->uuid('user_created')->nullable();
            $table->uuid('user_updated')->nullable();
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
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
        Schema::dropIfExists('prepaid_loads');
    }
}

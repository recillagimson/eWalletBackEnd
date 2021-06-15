<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSelfiePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_selfie_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tier_approval_id')->nullable();
            $table->uuid('user_account_id')->refences('id')->on('user_accounts')->onDelete('cascade');
            $table->string('photo_location');
            $table->string('status')->default('PENDING');
            $table->string('remarks')->nullable();
            $table->uuid('reviewed_by')->nullable();
            $table->dateTime('reviewed_date')->nullable();
            $table->uuid('user_created')->refences('id')->on('user_accounts')->onDelete('cascade');
            $table->uuid('user_updated')->refences('id')->on('user_accounts')->onDelete('cascade');
            $table->timestamps();
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
        Schema::dropIfExists('user_selfie_photos');
    }
}

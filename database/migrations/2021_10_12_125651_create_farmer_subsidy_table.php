<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFarmerSubsidyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('farmer_subsidies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('filename');
            $table->integer('seq');
            $table->string('province');
            $table->string('success');
            $table->string('fails');
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
        Schema::dropIfExists('farmer_subsidies');
    }
}

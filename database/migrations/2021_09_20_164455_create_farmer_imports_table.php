<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFarmerImportsTable extends Migration
{

    public function up()
    {
        Schema::create('farmer_imports', function (Blueprint $table) {
            $table->string('id')->nullable();
            $table->string('filename');
            $table->integer('seq')->nullable();
            $table->string('province')->nullable();
            $table->integer('success')->nullable();
            $table->integer('fails')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('farmer_imports');
    }
}

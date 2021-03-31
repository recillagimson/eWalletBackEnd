<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('id_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('description', 50)->unique();
            $table->boolean('swirecommended')->default(false);
            $table->boolean('status')->default(true);
            $table->uuid('user_created');
            $table->uuid('user_updated');
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
        Schema::dropIfExists('id_types');
    }
}

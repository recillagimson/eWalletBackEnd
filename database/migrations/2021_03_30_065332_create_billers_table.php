<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50);
            $table->string('name', 50);
            $table->string('institution', 50);
            $table->decimal('fee', 10, 3);
            $table->boolean('status')->default(true);
            $table->uuid('user_created');
            $table->uuid('user_updated');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->unique(array('code', 'name', 'institution'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billers');
    }
}

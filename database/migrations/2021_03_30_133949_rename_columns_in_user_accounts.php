<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnsInUserAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->renameColumn('ismerchant', 'is_merchant');
            $table->renameColumn('isAdmin', 'is_admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->renameColumn('is_merchant', 'ismerchant');
            $table->renameColumn('is_admin', 'isAdmin');
        });
    }
}

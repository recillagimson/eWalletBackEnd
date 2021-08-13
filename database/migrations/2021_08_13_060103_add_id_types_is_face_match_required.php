<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdTypesIsFaceMatchRequired extends Migration
{    /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
       Schema::table('id_types', function (Blueprint $table) {
           $table->boolean('is_face_match_required')->default(false)->after('is_full_name');
           
       });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
       Schema::table('id_types', function (Blueprint $table) {
           $table->dropColumn('barangay');
       });
   }
}

<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class insert_id_types extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
            DB::table('id_types')->insert([

                ['id' =>"0edb4f9f-9131-11eb-b44f-1c1b0d14e211",'type' =>"TIN ID",'description' =>"Tax Identification Card (TIN)",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb764a-9131-11eb-b44f-1c1b0d14e211",'type' =>"PASSPORT",'description' =>"Passport",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb776f-9131-11eb-b44f-1c1b0d14e211",'type' =>"BARANGAY CERTIFICATE",'description' =>"Barangay Certificate",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb77ef-9131-11eb-b44f-1c1b0d14e211",'type' =>"SSS ID",'description' =>"Social Security System (SSS) Card ",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb787d-9131-11eb-b44f-1c1b0d14e211",'type' =>"UMID",'description' =>"Unified Multi-Purpose ID",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb78f3-9131-11eb-b44f-1c1b0d14e211",'type' =>"COMPANY ID",'description' =>"Company ID(issued by private entities or instituti",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7990-9131-11eb-b44f-1c1b0d14e211",'type' =>"PHICB ",'description' =>"Philhealth Insurance Card ng Bayan (PHICB)",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7a07-9131-11eb-b44f-1c1b0d14e211",'type' =>"GOVERNMENT OFFICE AND GOCC ID",'description' =>"Government Office and Government Owned Corporation",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7a97-9131-11eb-b44f-1c1b0d14e211",'type' =>"VOTER'S ID",'description' =>"Voter's ID",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7b31-9131-11eb-b44f-1c1b0d14e211",'type' =>"DRIVER'S LICENSE",'description' =>"Driver's License",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7b9a-9131-11eb-b44f-1c1b0d14e211",'type' =>"POLICE CLEARANCE",'description' =>"Police Clearance",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7c06-9131-11eb-b44f-1c1b0d14e211",'type' =>"NBI CLEARANCE",'description' =>"National Bureau of Investigation(NBI) Clearance",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7c7e-9131-11eb-b44f-1c1b0d14e211",'type' =>"PRC ID",'description' =>"Professional Regulation Commission (PRC) ID",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7ceb-9131-11eb-b44f-1c1b0d14e211",'type' =>"POSTAL ID",'description' =>"Postal ID",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7d57-9131-11eb-b44f-1c1b0d14e211",'type' =>"ACR/ICR",'description' =>"Alien Certification of Registration / Immigrant Ce",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7dcf-9131-11eb-b44f-1c1b0d14e211",'type' =>"SENIOR CITIZEN CARD",'description' =>"Senior Citizen Card",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7e4b-9131-11eb-b44f-1c1b0d14e211",'type' =>"SCHOOL ID",'description' =>"School ID",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7ebf-9131-11eb-b44f-1c1b0d14e211",'type' =>"IBP ID",'description' =>"Integrated Bar of the Philippines ID",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7f29-9131-11eb-b44f-1c1b0d14e211",'type' =>"GSIS ID",'description' =>"Government Service Insurance System (GSIS) E-card",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb7f99-9131-11eb-b44f-1c1b0d14e211",'type' =>"OWWA ID",'description' =>"Overseas Workers Welfare Administration (OWWA) ID",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb8013-9131-11eb-b44f-1c1b0d14e211",'type' =>"NCWDP CERTIFICATE",'description' =>"Certification from the National Council for the We",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb80a1-9131-11eb-b44f-1c1b0d14e211",'type' =>"PWD ID",'description' =>"Person With Disabilities ID",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb8123-9131-11eb-b44f-1c1b0d14e211",'type' =>"DSWD CERTIFICATE",'description' =>"Department of Social Welfare and Development (DSWD",'status' =>"1",'user_created' =>"Migration Team"],
['id' =>"0edb81a3-9131-11eb-b44f-1c1b0d14e211",'type' =>"SEAMAN'S BOOK",'description' =>"Seaman’s Book",'status' =>"1",'user_created' =>"Migration Team"],

                
            ]);
    }
}

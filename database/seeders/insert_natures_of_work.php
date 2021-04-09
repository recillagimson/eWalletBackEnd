<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class insert_natures_of_work extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('natures_of_work')->delete();
            DB::table('natures_of_work')->insert([

                ['id' =>"0ed93ff9-9131-11eb-b44f-1c1b0d14e211",'description' =>"Accountant",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed9658e-9131-11eb-b44f-1c1b0d14e211",'description' =>"BPO Companies",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96614-9131-11eb-b44f-1c1b0d14e211",'description' =>"Banking",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed9666c-9131-11eb-b44f-1c1b0d14e211",'description' =>"Brokerage/Securities Sector",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed966cf-9131-11eb-b44f-1c1b0d14e211",'description' =>"Car Dealers",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96725-9131-11eb-b44f-1c1b0d14e211",'description' =>"Casinos/Gaming Clubs/Lottery Outlet",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed9677e-9131-11eb-b44f-1c1b0d14e211",'description' =>"Construction",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed967cf-9131-11eb-b44f-1c1b0d14e211",'description' =>"Doctor/Dentist/other Medical Professionals",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed9682f-9131-11eb-b44f-1c1b0d14e211",'description' =>"E-Money Issuers",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed9687f-9131-11eb-b44f-1c1b0d14e211",'description' =>"Education (Teacher, Tutor, Professor, etc)",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed968d0-9131-11eb-b44f-1c1b0d14e211",'description' =>"Embassies/Foreign Consulates",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96922-9131-11eb-b44f-1c1b0d14e211",'description' =>"FX Dealer / Money Changer",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96971-9131-11eb-b44f-1c1b0d14e211",'description' =>"Financial Services (Non Stock Savings and Loans As",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed969e8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Freelance (e.g Writer, Buy and Sell)",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96a41-9131-11eb-b44f-1c1b0d14e211",'description' =>"Government Employees",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96a94-9131-11eb-b44f-1c1b0d14e211",'description' =>"Government Service (LGUs, etc)",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96aea-9131-11eb-b44f-1c1b0d14e211",'description' =>"Housewife/Househusband/Dependent",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96b3e-9131-11eb-b44f-1c1b0d14e211",'description' =>"IT Companies",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96b8e-9131-11eb-b44f-1c1b0d14e211",'description' =>"Insurance Sector",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96bdf-9131-11eb-b44f-1c1b0d14e211",'description' =>"Jewelry Business",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96c34-9131-11eb-b44f-1c1b0d14e211",'description' =>"Lawyers and Notaries",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96c88-9131-11eb-b44f-1c1b0d14e211",'description' =>"Legal Practice (Firms)",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96cd9-9131-11eb-b44f-1c1b0d14e211",'description' =>"Lending and Financing",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96d28-9131-11eb-b44f-1c1b0d14e211",'description' =>"Manning/Employment Agencies",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96d7c-9131-11eb-b44f-1c1b0d14e211",'description' =>"Manufacturing",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96dca-9131-11eb-b44f-1c1b0d14e211",'description' =>"Military and Police",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96e17-9131-11eb-b44f-1c1b0d14e211",'description' =>"Multi Level Marketing",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96e64-9131-11eb-b44f-1c1b0d14e211",'description' =>"NGO/ Foundations/ Charities",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96eb4-9131-11eb-b44f-1c1b0d14e211",'description' =>"OFW",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96f01-9131-11eb-b44f-1c1b0d14e211",'description' =>"Other/s, please specify",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96f4e-9131-11eb-b44f-1c1b0d14e211",'description' =>"Pawnshop",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96f98-9131-11eb-b44f-1c1b0d14e211",'description' =>"Precious Metals and Stones Business",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed96fe9-9131-11eb-b44f-1c1b0d14e211",'description' =>"Real Estate",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed97033-9131-11eb-b44f-1c1b0d14e211",'description' =>"Religious Organizations",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed97083-9131-11eb-b44f-1c1b0d14e211",'description' =>"Remittance Agent",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed970d0-9131-11eb-b44f-1c1b0d14e211",'description' =>"Retiree",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed9711c-9131-11eb-b44f-1c1b0d14e211",'description' =>"Students",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed97166-9131-11eb-b44f-1c1b0d14e211",'description' =>"Transportation (Driver, Operator, etc.)",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed971b7-9131-11eb-b44f-1c1b0d14e211",'description' =>"Virtual Currencies",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed97204-9131-11eb-b44f-1c1b0d14e211",'description' =>"e-commerce/online business",'status' =>"1",'user_created' =>"Migration Team"],
                

            ]);
    }
}

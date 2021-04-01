<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class insert_currencies extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
            DB::table('currencies')->insert([
                ['id' =>"0ed21e2c-9131-11eb-b44f-1c1b0d14e211",'description' =>"UAE Dirham",'code' =>"AED",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24251-9131-11eb-b44f-1c1b0d14e211",'description' =>"Afghani",'code' =>"AFN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed242fc-9131-11eb-b44f-1c1b0d14e211",'description' =>"Lek",'code' =>"ALL",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2435b-9131-11eb-b44f-1c1b0d14e211",'description' =>"Armenian Dram",'code' =>"AMD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed243b9-9131-11eb-b44f-1c1b0d14e211",'description' =>"Netherlands Antillean Guilder",'code' =>"ANG",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24415-9131-11eb-b44f-1c1b0d14e211",'description' =>"Kwanza",'code' =>"AOA",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2446e-9131-11eb-b44f-1c1b0d14e211",'description' =>"Argentine Peso",'code' =>"ARS",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed244c2-9131-11eb-b44f-1c1b0d14e211",'description' =>"Australian Dollar",'code' =>"AUD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2451c-9131-11eb-b44f-1c1b0d14e211",'description' =>"Aruban Florin",'code' =>"AWG",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24570-9131-11eb-b44f-1c1b0d14e211",'description' =>"Azerbaijanian Manat",'code' =>"AZN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed245c3-9131-11eb-b44f-1c1b0d14e211",'description' =>"Convertible Mark",'code' =>"BAM",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24616-9131-11eb-b44f-1c1b0d14e211",'description' =>"Barbados Dollar",'code' =>"BBD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2466a-9131-11eb-b44f-1c1b0d14e211",'description' =>"Taka",'code' =>"BDT",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed246bf-9131-11eb-b44f-1c1b0d14e211",'description' =>"Bulgarian Lev",'code' =>"BGN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24715-9131-11eb-b44f-1c1b0d14e211",'description' =>"Bahraini Dinar",'code' =>"BHD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24764-9131-11eb-b44f-1c1b0d14e211",'description' =>"Burundi Franc",'code' =>"BIF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed247b8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Bermudian Dollar",'code' =>"BMD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24812-9131-11eb-b44f-1c1b0d14e211",'description' =>"Brunei Dollar",'code' =>"BND",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24865-9131-11eb-b44f-1c1b0d14e211",'description' =>"Boliviano",'code' =>"BOB",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed248b8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Mvdol",'code' =>"BOV",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24908-9131-11eb-b44f-1c1b0d14e211",'description' =>"Brazilian Real",'code' =>"BRL",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2495b-9131-11eb-b44f-1c1b0d14e211",'description' =>"Bahamian Dollar",'code' =>"BSD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed249ac-9131-11eb-b44f-1c1b0d14e211",'description' =>"Ngultrum",'code' =>"BTN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed249fa-9131-11eb-b44f-1c1b0d14e211",'description' =>"Pula",'code' =>"BWP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24a4a-9131-11eb-b44f-1c1b0d14e211",'description' =>"Belarussian Ruble",'code' =>"BYN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24a9d-9131-11eb-b44f-1c1b0d14e211",'description' =>"Belize Dollar",'code' =>"BZD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24aec-9131-11eb-b44f-1c1b0d14e211",'description' =>"Canadian Dollar",'code' =>"CAD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24b3e-9131-11eb-b44f-1c1b0d14e211",'description' =>"Congolese Franc",'code' =>"CDF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24b94-9131-11eb-b44f-1c1b0d14e211",'description' =>"WIR Euro",'code' =>"CHE",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24be8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Swiss Franc",'code' =>"CHF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24c36-9131-11eb-b44f-1c1b0d14e211",'description' =>"WIR Franc",'code' =>"CHW",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24c88-9131-11eb-b44f-1c1b0d14e211",'description' =>"Unidad de Fomento",'code' =>"CLF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24cd8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Chilean Peso",'code' =>"CLP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24d28-9131-11eb-b44f-1c1b0d14e211",'description' =>"Yuan Renminbi",'code' =>"CNY",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24d7d-9131-11eb-b44f-1c1b0d14e211",'description' =>"Colombian Peso",'code' =>"COP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24dcd-9131-11eb-b44f-1c1b0d14e211",'description' =>"Unidad de Valor Real",'code' =>"COU",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24e1e-9131-11eb-b44f-1c1b0d14e211",'description' =>"Costa Rican Colon",'code' =>"CRC",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24e70-9131-11eb-b44f-1c1b0d14e211",'description' =>"Peso Convertible",'code' =>"CUC",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24ebe-9131-11eb-b44f-1c1b0d14e211",'description' =>"Cuban Peso",'code' =>"CUP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24f12-9131-11eb-b44f-1c1b0d14e211",'description' =>"Cabo Verde Escudo",'code' =>"CVE",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24f61-9131-11eb-b44f-1c1b0d14e211",'description' =>"Czech Koruna",'code' =>"CZK",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed24fb0-9131-11eb-b44f-1c1b0d14e211",'description' =>"Djibouti Franc",'code' =>"DJF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25001-9131-11eb-b44f-1c1b0d14e211",'description' =>"Danish Krone",'code' =>"DKK",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25053-9131-11eb-b44f-1c1b0d14e211",'description' =>"Dominican Peso",'code' =>"DOP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed250a8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Algerian Dinar",'code' =>"DZD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed250f7-9131-11eb-b44f-1c1b0d14e211",'description' =>"Egyptian Pound",'code' =>"EGP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25146-9131-11eb-b44f-1c1b0d14e211",'description' =>"Nakfa",'code' =>"ERN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25194-9131-11eb-b44f-1c1b0d14e211",'description' =>"Ethiopian Birr",'code' =>"ETB",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed251e5-9131-11eb-b44f-1c1b0d14e211",'description' =>"Euro",'code' =>"EUR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25237-9131-11eb-b44f-1c1b0d14e211",'description' =>"Fiji Dollar",'code' =>"FJD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2528a-9131-11eb-b44f-1c1b0d14e211",'description' =>"Falkland Islands Pound",'code' =>"FKP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed252de-9131-11eb-b44f-1c1b0d14e211",'description' =>"Pound Sterling",'code' =>"GBP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25330-9131-11eb-b44f-1c1b0d14e211",'description' =>"Lari",'code' =>"GEL",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2537f-9131-11eb-b44f-1c1b0d14e211",'description' =>"Ghana Cedi",'code' =>"GHS",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed253d0-9131-11eb-b44f-1c1b0d14e211",'description' =>"Gibraltar Pound",'code' =>"GIP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25420-9131-11eb-b44f-1c1b0d14e211",'description' =>"Dalasi",'code' =>"GMD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25471-9131-11eb-b44f-1c1b0d14e211",'description' =>"Guinea Franc",'code' =>"GNF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed254c6-9131-11eb-b44f-1c1b0d14e211",'description' =>"Quetzal",'code' =>"GTQ",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25516-9131-11eb-b44f-1c1b0d14e211",'description' =>"Guyana Dollar",'code' =>"GYD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2556a-9131-11eb-b44f-1c1b0d14e211",'description' =>"Hong Kong Dollar",'code' =>"HKD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed255c0-9131-11eb-b44f-1c1b0d14e211",'description' =>"Lempira",'code' =>"HNL",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2560f-9131-11eb-b44f-1c1b0d14e211",'description' =>"Kuna",'code' =>"HRK",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25662-9131-11eb-b44f-1c1b0d14e211",'description' =>"Gourde",'code' =>"HTG",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed256b4-9131-11eb-b44f-1c1b0d14e211",'description' =>"Forint",'code' =>"HUF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25703-9131-11eb-b44f-1c1b0d14e211",'description' =>"Rupiah",'code' =>"IDR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25754-9131-11eb-b44f-1c1b0d14e211",'description' =>"New Israeli Sheqel",'code' =>"ILS",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed257a7-9131-11eb-b44f-1c1b0d14e211",'description' =>"Indian Rupee",'code' =>"INR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed257ff-9131-11eb-b44f-1c1b0d14e211",'description' =>"Iraqi Dinar",'code' =>"IQD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2585d-9131-11eb-b44f-1c1b0d14e211",'description' =>"Iranian Rial",'code' =>"IRR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed258b4-9131-11eb-b44f-1c1b0d14e211",'description' =>"Iceland Krona",'code' =>"ISK",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25906-9131-11eb-b44f-1c1b0d14e211",'description' =>"Jamaican Dollar",'code' =>"JMD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25956-9131-11eb-b44f-1c1b0d14e211",'description' =>"Jordanian Dinar",'code' =>"JOD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed259a8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Yen",'code' =>"JPY",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed259f8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Kenyan Shilling",'code' =>"KES",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25a4a-9131-11eb-b44f-1c1b0d14e211",'description' =>"Som",'code' =>"KGS",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25a9d-9131-11eb-b44f-1c1b0d14e211",'description' =>"Riel",'code' =>"KHR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25aee-9131-11eb-b44f-1c1b0d14e211",'description' =>"Comoro Franc",'code' =>"KMF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25b40-9131-11eb-b44f-1c1b0d14e211",'description' =>"North Korean Won",'code' =>"KPW",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25b90-9131-11eb-b44f-1c1b0d14e211",'description' =>"Won",'code' =>"KRW",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25be0-9131-11eb-b44f-1c1b0d14e211",'description' =>"Kuwaiti Dinar",'code' =>"KWD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25c33-9131-11eb-b44f-1c1b0d14e211",'description' =>"Cayman Islands Dollar",'code' =>"KYD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25c85-9131-11eb-b44f-1c1b0d14e211",'description' =>"Tenge",'code' =>"KZT",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25cd5-9131-11eb-b44f-1c1b0d14e211",'description' =>"Kip",'code' =>"LAK",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25d26-9131-11eb-b44f-1c1b0d14e211",'description' =>"Lebanese Pound",'code' =>"LBP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25d77-9131-11eb-b44f-1c1b0d14e211",'description' =>"Sri Lanka Rupee",'code' =>"LKR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25dc7-9131-11eb-b44f-1c1b0d14e211",'description' =>"Liberian Dollar",'code' =>"LRD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25e17-9131-11eb-b44f-1c1b0d14e211",'description' =>"Loti",'code' =>"LSL",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25e66-9131-11eb-b44f-1c1b0d14e211",'description' =>"Libyan Dinar",'code' =>"LYD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25eb7-9131-11eb-b44f-1c1b0d14e211",'description' =>"Moroccan Dirham",'code' =>"MAD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25f07-9131-11eb-b44f-1c1b0d14e211",'description' =>"Moldovan Leu",'code' =>"MDL",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25f57-9131-11eb-b44f-1c1b0d14e211",'description' =>"Malagasy Ariary",'code' =>"MGA",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25fa6-9131-11eb-b44f-1c1b0d14e211",'description' =>"Denar",'code' =>"MKD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed25ff6-9131-11eb-b44f-1c1b0d14e211",'description' =>"Kyat",'code' =>"MMK",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26043-9131-11eb-b44f-1c1b0d14e211",'description' =>"Tugrik",'code' =>"MNT",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26096-9131-11eb-b44f-1c1b0d14e211",'description' =>"Pataca",'code' =>"MOP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed260e3-9131-11eb-b44f-1c1b0d14e211",'description' =>"Ouguiya",'code' =>"MRU",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26132-9131-11eb-b44f-1c1b0d14e211",'description' =>"Mauritius Rupee",'code' =>"MUR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26185-9131-11eb-b44f-1c1b0d14e211",'description' =>"Rufiyaa",'code' =>"MVR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed261d4-9131-11eb-b44f-1c1b0d14e211",'description' =>"Kwacha",'code' =>"MWK",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26224-9131-11eb-b44f-1c1b0d14e211",'description' =>"Mexican Peso",'code' =>"MXN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26275-9131-11eb-b44f-1c1b0d14e211",'description' =>"Mexican Unidad de Inversion (UDI)",'code' =>"MXV",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed262cc-9131-11eb-b44f-1c1b0d14e211",'description' =>"Malaysian Ringgit",'code' =>"MYR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26320-9131-11eb-b44f-1c1b0d14e211",'description' =>"Mozambique Metical",'code' =>"MZN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26371-9131-11eb-b44f-1c1b0d14e211",'description' =>"Namibia Dollar",'code' =>"NAD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed263c2-9131-11eb-b44f-1c1b0d14e211",'description' =>"Naira",'code' =>"NGN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26413-9131-11eb-b44f-1c1b0d14e211",'description' =>"Cordoba Oro",'code' =>"NIO",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26465-9131-11eb-b44f-1c1b0d14e211",'description' =>"Norwegian Krone",'code' =>"NOK",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed264b6-9131-11eb-b44f-1c1b0d14e211",'description' =>"Nepalese Rupee",'code' =>"NPR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2650b-9131-11eb-b44f-1c1b0d14e211",'description' =>"New Zealand Dollar",'code' =>"NZD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2656e-9131-11eb-b44f-1c1b0d14e211",'description' =>"Rial Omani",'code' =>"OMR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed265c1-9131-11eb-b44f-1c1b0d14e211",'description' =>"Balboa",'code' =>"PAB",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26616-9131-11eb-b44f-1c1b0d14e211",'description' =>"Nuevo Sol",'code' =>"PEN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26670-9131-11eb-b44f-1c1b0d14e211",'description' =>"Kina",'code' =>"PGK",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed266c1-9131-11eb-b44f-1c1b0d14e211",'description' =>"Philippine Peso",'code' =>"PHP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26712-9131-11eb-b44f-1c1b0d14e211",'description' =>"Pakistan Rupee",'code' =>"PKR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26761-9131-11eb-b44f-1c1b0d14e211",'description' =>"Zloty",'code' =>"PLN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed267b5-9131-11eb-b44f-1c1b0d14e211",'description' =>"Guarani",'code' =>"PYG",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2681f-9131-11eb-b44f-1c1b0d14e211",'description' =>"Qatari Rial",'code' =>"QAR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26873-9131-11eb-b44f-1c1b0d14e211",'description' =>"Romanian Leu",'code' =>"RON",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed268c3-9131-11eb-b44f-1c1b0d14e211",'description' =>"Serbian Dinar",'code' =>"RSD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26917-9131-11eb-b44f-1c1b0d14e211",'description' =>"Russian Ruble",'code' =>"RUB",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2696b-9131-11eb-b44f-1c1b0d14e211",'description' =>"Rwanda Franc",'code' =>"RWF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed269ba-9131-11eb-b44f-1c1b0d14e211",'description' =>"Saudi Riyal",'code' =>"SAR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26a0d-9131-11eb-b44f-1c1b0d14e211",'description' =>"Solomon Islands Dollar",'code' =>"SBD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26a61-9131-11eb-b44f-1c1b0d14e211",'description' =>"Seychelles Rupee",'code' =>"SCR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26ab3-9131-11eb-b44f-1c1b0d14e211",'description' =>"Sudanese Pound",'code' =>"SDG",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26b02-9131-11eb-b44f-1c1b0d14e211",'description' =>"Swedish Krona",'code' =>"SEK",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26b51-9131-11eb-b44f-1c1b0d14e211",'description' =>"Singapore Dollar",'code' =>"SGD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26ba4-9131-11eb-b44f-1c1b0d14e211",'description' =>"Saint Helena Pound",'code' =>"SHP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26bf4-9131-11eb-b44f-1c1b0d14e211",'description' =>"Leone",'code' =>"SLL",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26c41-9131-11eb-b44f-1c1b0d14e211",'description' =>"Somali Shilling",'code' =>"SOS",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26c94-9131-11eb-b44f-1c1b0d14e211",'description' =>"Surinam Dollar",'code' =>"SRD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26ce4-9131-11eb-b44f-1c1b0d14e211",'description' =>"South Sudanese Pound",'code' =>"SSP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26d37-9131-11eb-b44f-1c1b0d14e211",'description' =>"Dobra",'code' =>"STN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26d88-9131-11eb-b44f-1c1b0d14e211",'description' =>"El Salvador Colon",'code' =>"SVC",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26dd7-9131-11eb-b44f-1c1b0d14e211",'description' =>"Syrian Pound",'code' =>"SYP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26e2c-9131-11eb-b44f-1c1b0d14e211",'description' =>"Lilangeni",'code' =>"SZL",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26e7d-9131-11eb-b44f-1c1b0d14e211",'description' =>"Baht",'code' =>"THB",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26ed1-9131-11eb-b44f-1c1b0d14e211",'description' =>"Somoni",'code' =>"TJS",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26f25-9131-11eb-b44f-1c1b0d14e211",'description' =>"Turkmenistan New Manat",'code' =>"TMT",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed26f78-9131-11eb-b44f-1c1b0d14e211",'description' =>"Tunisian Dinar",'code' =>"TND",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed29d7f-9131-11eb-b44f-1c1b0d14e211",'description' =>"Pa’anga",'code' =>"TOP",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed29f66-9131-11eb-b44f-1c1b0d14e211",'description' =>"Turkish Lira",'code' =>"TRY",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed29fcf-9131-11eb-b44f-1c1b0d14e211",'description' =>"Trinidad and Tobago Dollar",'code' =>"TTD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a031-9131-11eb-b44f-1c1b0d14e211",'description' =>"New Taiwan Dollar",'code' =>"TWD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a091-9131-11eb-b44f-1c1b0d14e211",'description' =>"Tanzanian Shilling",'code' =>"TZS",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a0e9-9131-11eb-b44f-1c1b0d14e211",'description' =>"Hryvnia",'code' =>"UAH",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a140-9131-11eb-b44f-1c1b0d14e211",'description' =>"Uganda Shilling",'code' =>"UGX",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a194-9131-11eb-b44f-1c1b0d14e211",'description' =>"US Dollar",'code' =>"USD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a1e9-9131-11eb-b44f-1c1b0d14e211",'description' =>"US Dollar (Next day)",'code' =>"USN",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a242-9131-11eb-b44f-1c1b0d14e211",'description' =>"Uruguay Peso en Unidades Indexadas (URUIURUI)",'code' =>"UYI",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a2a1-9131-11eb-b44f-1c1b0d14e211",'description' =>"Peso Uruguayo",'code' =>"UYU",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a2f6-9131-11eb-b44f-1c1b0d14e211",'description' =>"Uzbekistan Sum",'code' =>"UZS",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a34e-9131-11eb-b44f-1c1b0d14e211",'description' =>"Bolivar",'code' =>"VEF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a3a3-9131-11eb-b44f-1c1b0d14e211",'description' =>"Dong",'code' =>"VND",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a3f8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Vatu",'code' =>"VUV",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a44c-9131-11eb-b44f-1c1b0d14e211",'description' =>"Tala",'code' =>"WST",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a4a1-9131-11eb-b44f-1c1b0d14e211",'description' =>"CFA Franc BEAC",'code' =>"XAF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a4f7-9131-11eb-b44f-1c1b0d14e211",'description' =>"East Caribbean Dollar",'code' =>"XCD",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a54c-9131-11eb-b44f-1c1b0d14e211",'description' =>"SDR (Special Drawing Right)",'code' =>"XDR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a5a0-9131-11eb-b44f-1c1b0d14e211",'description' =>"CFA Franc BCEAO",'code' =>"XOF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a5f4-9131-11eb-b44f-1c1b0d14e211",'description' =>"CFP Franc",'code' =>"XPF",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a64c-9131-11eb-b44f-1c1b0d14e211",'description' =>"Sucre",'code' =>"XSU",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a69e-9131-11eb-b44f-1c1b0d14e211",'description' =>"ADB Unit of Account",'code' =>"XUA",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a6f1-9131-11eb-b44f-1c1b0d14e211",'description' =>"Yemeni Rial",'code' =>"YER",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a743-9131-11eb-b44f-1c1b0d14e211",'description' =>"Rand",'code' =>"ZAR",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a798-9131-11eb-b44f-1c1b0d14e211",'description' =>"Zambian Kwacha",'code' =>"ZMW",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed2a7ed-9131-11eb-b44f-1c1b0d14e211",'description' =>"Zimbabwe Dollar",'code' =>"ZWL",'status' =>"1",'user_created' =>"Migration Team"],
                
            
            ]);
    }
}

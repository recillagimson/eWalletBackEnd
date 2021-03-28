
/*
SET @@global.net_read_timeout =6000;
SET @@global.connect_timeout  =6000;
SET GLOBAL interactive_timeout=6000;
SET GLOBAL max_allowed_packet=16000000
*/


DELETE FROM `squidpayv3`.`billers`;
DELETE FROM `squidpayv3`.`transaction_categories`;
DELETE FROM  `squidpayv3`.`maritial_status`;
DELETE FROM  `squidpayv3`.`nationalities`;
DELETE FROM  `squidpayv3`.`countries`;
DELETE FROM  `squidpayv3`.`currencies`;
DELETE FROM  `squidpayv3`.`signup_hosts`;
DELETE FROM  `squidpayv3`.`id_types`;
DELETE FROM  `squidpayv3`.`user_accounts`;
DELETE FROM  `squidpayv3`.`user_details`;
DELETE FROM  `squidpayv3`.`user_id_photos`;
DELETE FROM  `squidpayv3`.`merchant_details`;
DELETE FROM `squidpayv3`.`natures_of_work`;
DELETE FROM `squidpayv3`.`source_of_funds`;

/*START - MIGRATION OF BILLER*/

INSERT INTO `squidpayv3`.`billers`(`id`,`code`,`name`, `institution`, `fee`, `Status`, `user_created`)
SELECT  UUID(),`biller_code`, `biller_name`, `biller_institution`, `biller_fee`, TRUE, 'Migration Team'
FROM (SELECT DISTINCT `biller_code`, `biller_name`, `biller_institution`, `biller_fee` FROM `squidpay`.`billers`) AS A;

/*END - MIGRATION OF BILLER*/





/*START - MIGRATION OF TRANSACTION CATEGORY*/
INSERT INTO `squidpayv3`.`transaction_categories`(`id`, `name`, `description`, `status`, `user_created`)
SELECT  UUID(), transactionName, transactionDescription, TRUE, 'Migration Team'
FROM (SELECT DISTINCT transactionName, transactionDescription  FROM `squidpay`.`transactioncategory` 
WHERE transactionDescription <> 'cashin via dragon pay') AS A;
/*END - MIGRATION OF TRANSACTION CATEGORY*/




/*START - INSERT OF MARITIAL STATUS*/
INSERT INTO `squidpayv3`.`maritial_status` (`id`, `description`, `status`, `legend`, `user_created`)
VALUES (UUID(), 'Single', TRUE, 'S',  'Migration Team');
INSERT INTO `squidpayv3`.`maritial_status` (`id`, `description`, `status`, `legend`, `user_created`)
VALUES (UUID(), 'Married', TRUE, 'M', 'Migration Team');
INSERT INTO `squidpayv3`.`maritial_status` (`id`, `description`, `status`, `legend`, `user_created`)
VALUES (UUID(), 'Widowed', TRUE, 'W', 'Migration Team');
INSERT INTO `squidpayv3`.`maritial_status` (`id`, `description`, `status`, `legend`, `user_created`)
VALUES (UUID(), 'Divorced', TRUE, 'D', 'Migration Team');
INSERT INTO `squidpayv3`.`maritial_status` (`id`, `description`, `status`, `legend`, `user_created`)
VALUES (UUID(), 'Separated', TRUE, 'SEP', 'Migration Team');
/*END - INSERT OF MARITIAL STATUS*/


/*START - INSERT OF NATIONALITIES*/
INSERT INTO squidpayv3.`nationalities`(id,DESCRIPTION,CODE,STATUS,user_created)
VALUES
  (UUID(),'Afghanistan','AF',TRUE,'Migration Team'),
  (UUID(),'Aland Islands','AX',TRUE,'Migration Team'),
  (UUID(),'Albania','AL',TRUE,'Migration Team'),
  (UUID(),'Algeria','DZ',TRUE,'Migration Team'),
  (UUID(),'American Samoa','AS',TRUE,'Migration Team'),
  (UUID(),'Andorra','AD',TRUE,'Migration Team'),
  (UUID(),'Angola','AO',TRUE,'Migration Team'),
  (UUID(),'Anguilla','AI',TRUE,'Migration Team'),
  (UUID(),'Antarctica','AQ',TRUE,'Migration Team'),
  (UUID(),'Antigua and Barbuda','AG',TRUE,'Migration Team'),
  (UUID(),'Argentina','AR',TRUE,'Migration Team'),
  (UUID(),'Armenia','AM',TRUE,'Migration Team'),
  (UUID(),'Aruba','AW',TRUE,'Migration Team'),
  (UUID(),'Australia','AU',TRUE,'Migration Team'),
  (UUID(),'Austria','AT',TRUE,'Migration Team'),
  (UUID(),'Azerbaijan','AZ',TRUE,'Migration Team'),
  (UUID(),'Bahamas','BS',TRUE,'Migration Team'),
  (UUID(),'Bahrain','BH',TRUE,'Migration Team'),
  (UUID(),'Bangladesh','BD',TRUE,'Migration Team'),
  (UUID(),'Barbados','BB',TRUE,'Migration Team'),
  (UUID(),'Belarus','BY',TRUE,'Migration Team'),
  (UUID(),'Belgium','BE',TRUE,'Migration Team'),
  (UUID(),'Belize','BZ',TRUE,'Migration Team'),
  (UUID(),'Benin','BJ',TRUE,'Migration Team'),
  (UUID(),'Bermuda','BM',TRUE,'Migration Team'),
  (UUID(),'Bhutan','BT',TRUE,'Migration Team'),
  (UUID(),'Bolivia','BO',TRUE,'Migration Team'),
  (UUID(),'Bosnia and Herzegovina','BA',TRUE,'Migration Team'),
  (UUID(),'Botswana','BW',TRUE,'Migration Team'),
  (UUID(),'Bouvet Island','BV',TRUE,'Migration Team'),
  (UUID(),'Brazil','BR',TRUE,'Migration Team'),
  (UUID(),'British Virgin Islands','VG',TRUE,'Migration Team'),
  (UUID(),'British Indian Ocean Territory','IO',TRUE,'Migration Team'),
  (UUID(),'Brunei Darussalam','BN',TRUE,'Migration Team'),
  (UUID(),'Bulgaria','BG',TRUE,'Migration Team'),
  (UUID(),'Burkina Faso','BF',TRUE,'Migration Team'),
  (UUID(),'Burundi','BI',TRUE,'Migration Team'),
  (UUID(),'Cambodia','KH',TRUE,'Migration Team'),
  (UUID(),'Cameroon','CM',TRUE,'Migration Team'),
  (UUID(),'Canada','CA',TRUE,'Migration Team'),
  (UUID(),'Cape Verde','CV',TRUE,'Migration Team'),
  (UUID(),'Cayman Islands','KY',TRUE,'Migration Team'),
  (UUID(),'Central African Republic','CF',TRUE,'Migration Team'),
  (UUID(),'Chad','TD',TRUE,'Migration Team'),
  (UUID(),'Chile','CL',TRUE,'Migration Team'),
  (UUID(),'China','CN',TRUE,'Migration Team'),
  (UUID(),'Hong Kong, SAR China','HK',TRUE,'Migration Team'),
  (UUID(),'Macao, SAR China','MO',TRUE,'Migration Team'),
  (UUID(),'Christmas Island','CX',TRUE,'Migration Team'),
  (UUID(),'Cocos (Keeling) Islands','CC',TRUE,'Migration Team'),
  (UUID(),'Colombia','CO',TRUE,'Migration Team'),
  (UUID(),'Comoros','KM',TRUE,'Migration Team'),
  (UUID(),'Congo (Brazzaville)','CG',TRUE,'Migration Team'),
  (UUID(),'Congo, (Kinshasa)','CD',TRUE,'Migration Team'),
  (UUID(),'Cook Islands','CK',TRUE,'Migration Team'),
  (UUID(),'Costa Rica','CR',TRUE,'Migration Team'),
  (UUID(),'Côte d''Ivoire','CI',TRUE,'Migration Team'),
  (UUID(),'Croatia','HR',TRUE,'Migration Team'),
  (UUID(),'Cuba','CU',TRUE,'Migration Team'),
  (UUID(),'Cyprus','CY',TRUE,'Migration Team'),
  (UUID(),'Czech Republic','CZ',TRUE,'Migration Team'),
  (UUID(),'Denmark','DK',TRUE,'Migration Team'),
  (UUID(),'Djibouti','DJ',TRUE,'Migration Team'),
  (UUID(),'Dominica','DM',TRUE,'Migration Team'),
  (UUID(),'Dominican Republic','DO',TRUE,'Migration Team'),
  (UUID(),'Ecuador','EC',TRUE,'Migration Team'),
  (UUID(),'Egypt','EG',TRUE,'Migration Team'),
  (UUID(),'El Salvador','SV',TRUE,'Migration Team'),
  (UUID(),'Equatorial Guinea','GQ',TRUE,'Migration Team'),
  (UUID(),'Eritrea','ER',TRUE,'Migration Team'),
  (UUID(),'Estonia','EE',TRUE,'Migration Team'),
  (UUID(),'Ethiopia','ET',TRUE,'Migration Team'),
  (UUID(),'Falkland Islands (Malvinas)','FK',TRUE,'Migration Team'),
  (UUID(),'Faroe Islands','FO',TRUE,'Migration Team'),
  (UUID(),'Fiji','FJ',TRUE,'Migration Team'),
  (UUID(),'Finland','FI',TRUE,'Migration Team'),
  (UUID(),'France','FR',TRUE,'Migration Team'),
  (UUID(),'French Guiana','GF',TRUE,'Migration Team'),
  (UUID(),'French Polynesia','PF',TRUE,'Migration Team'),
  (UUID(),'French Southern Territories','TF',TRUE,'Migration Team'),
  (UUID(),'Gabon','GA',TRUE,'Migration Team'),
  (UUID(),'Gambia','GM',TRUE,'Migration Team'),
  (UUID(),'Georgia','GE',TRUE,'Migration Team'),
  (UUID(),'Germany','DE',TRUE,'Migration Team'),
  (UUID(),'Ghana','GH',TRUE,'Migration Team'),
  (UUID(),'Gibraltar','GI',TRUE,'Migration Team'),
  (UUID(),'Greece','GR',TRUE,'Migration Team'),
  (UUID(),'Greenland','GL',TRUE,'Migration Team'),
  (UUID(),'Grenada','GD',TRUE,'Migration Team'),
  (UUID(),'Guadeloupe','GP',TRUE,'Migration Team'),
  (UUID(),'Guam','GU',TRUE,'Migration Team'),
  (UUID(),'Guatemala','GT',TRUE,'Migration Team'),
  (UUID(),'Guernsey','GG',TRUE,'Migration Team'),
  (UUID(),'Guinea','GN',TRUE,'Migration Team'),
  (UUID(),'Guinea-Bissau','GW',TRUE,'Migration Team'),
  (UUID(),'Guyana','GY',TRUE,'Migration Team'),
  (UUID(),'Haiti','HT',TRUE,'Migration Team'),
  (UUID(),'Heard and Mcdonald Islands','HM',TRUE,'Migration Team'),
  (UUID(),'Holy See (Vatican City State)','VA',TRUE,'Migration Team'),
  (UUID(),'Honduras','HN',TRUE,'Migration Team'),
  (UUID(),'Hungary','HU',TRUE,'Migration Team'),
  (UUID(),'Iceland','IS',TRUE,'Migration Team'),
  (UUID(),'India','IN',TRUE,'Migration Team'),
  (UUID(),'Indonesia','ID',TRUE,'Migration Team'),
  (UUID(),'Iran, Islamic Republic of','IR',TRUE,'Migration Team'),
  (UUID(),'Iraq','IQ',TRUE,'Migration Team'),
  (UUID(),'Ireland','IE',TRUE,'Migration Team'),
  (UUID(),'Isle of Man','IM',TRUE,'Migration Team'),
  (UUID(),'Israel','IL',TRUE,'Migration Team'),
  (UUID(),'Italy','IT',TRUE,'Migration Team'),
  (UUID(),'Jamaica','JM',TRUE,'Migration Team'),
  (UUID(),'Japan','JP',TRUE,'Migration Team'),
  (UUID(),'Jersey','JE',TRUE,'Migration Team'),
  (UUID(),'Jordan','JO',TRUE,'Migration Team'),
  (UUID(),'Kazakhstan','KZ',TRUE,'Migration Team'),
  (UUID(),'Kenya','KE',TRUE,'Migration Team'),
  (UUID(),'Kiribati','KI',TRUE,'Migration Team'),
  (UUID(),'Korea (North)','KP',TRUE,'Migration Team'),
  (UUID(),'Korea (South)','KR',TRUE,'Migration Team'),
  (UUID(),'Kuwait','KW',TRUE,'Migration Team'),
  (UUID(),'Kyrgyzstan','KG',TRUE,'Migration Team'),
  (UUID(),'Lao PDR','LA',TRUE,'Migration Team'),
  (UUID(),'Latvia','LV',TRUE,'Migration Team'),
  (UUID(),'Lebanon','LB',TRUE,'Migration Team'),
  (UUID(),'Lesotho','LS',TRUE,'Migration Team'),
  (UUID(),'Liberia','LR',TRUE,'Migration Team'),
  (UUID(),'Libya','LY',TRUE,'Migration Team'),
  (UUID(),'Liechtenstein','LI',TRUE,'Migration Team'),
  (UUID(),'Lithuania','LT',TRUE,'Migration Team'),
  (UUID(),'Luxembourg','LU',TRUE,'Migration Team'),
  (UUID(),'Macedonia, Republic of','MK',TRUE,'Migration Team'),
  (UUID(),'Madagascar','MG',TRUE,'Migration Team'),
  (UUID(),'Malawi','MW',TRUE,'Migration Team'),
  (UUID(),'Malaysia','MY',TRUE,'Migration Team'),
  (UUID(),'Maldives','MV',TRUE,'Migration Team'),
  (UUID(),'Mali','ML',TRUE,'Migration Team'),
  (UUID(),'Malta','MT',TRUE,'Migration Team'),
  (UUID(),'Marshall Islands','MH',TRUE,'Migration Team'),
  (UUID(),'Martinique','MQ',TRUE,'Migration Team'),
  (UUID(),'Mauritania','MR',TRUE,'Migration Team'),
  (UUID(),'Mauritius','MU',TRUE,'Migration Team'),
  (UUID(),'Mayotte','YT',TRUE,'Migration Team'),
  (UUID(),'Mexico','MX',TRUE,'Migration Team'),
  (UUID(),'Micronesia, Federated States of','FM',TRUE,'Migration Team'),
  (UUID(),'Moldova','MD',TRUE,'Migration Team'),
  (UUID(),'Monaco','MC',TRUE,'Migration Team'),
  (UUID(),'Mongolia','MN',TRUE,'Migration Team'),
  (UUID(),'Montenegro','ME',TRUE,'Migration Team'),
  (UUID(),'Montserrat','MS',TRUE,'Migration Team'),
  (UUID(),'Morocco','MA',TRUE,'Migration Team'),
  (UUID(),'Mozambique','MZ',TRUE,'Migration Team'),
  (UUID(),'Myanmar','MM',TRUE,'Migration Team'),
  (UUID(),'Namibia','NA',TRUE,'Migration Team'),
  (UUID(),'Nauru','NR',TRUE,'Migration Team'),
  (UUID(),'Nepal','NP',TRUE,'Migration Team'),
  (UUID(),'Netherlands','NL',TRUE,'Migration Team'),
  (UUID(),'Netherlands Antilles','AN',TRUE,'Migration Team'),
  (UUID(),'New Caledonia','NC',TRUE,'Migration Team'),
  (UUID(),'New Zealand','NZ',TRUE,'Migration Team'),
  (UUID(),'Nicaragua','NI',TRUE,'Migration Team'),
  (UUID(),'Niger','NE',TRUE,'Migration Team'),
  (UUID(),'Nigeria','NG',TRUE,'Migration Team'),
  (UUID(),'Niue','NU',TRUE,'Migration Team'),
  (UUID(),'Norfolk Island','NF',TRUE,'Migration Team'),
  (UUID(),'Northern Mariana Islands','MP',TRUE,'Migration Team'),
  (UUID(),'Norway','NO',TRUE,'Migration Team'),
  (UUID(),'Oman','OM',TRUE,'Migration Team'),
  (UUID(),'Pakistan','PK',TRUE,'Migration Team'),
  (UUID(),'Palau','PW',TRUE,'Migration Team'),
  (UUID(),'Palestinian Territory','PS',TRUE,'Migration Team'),
  (UUID(),'Panama','PA',TRUE,'Migration Team'),
  (UUID(),'Papua New Guinea','PG',TRUE,'Migration Team'),
  (UUID(),'Paraguay','PY',TRUE,'Migration Team'),
  (UUID(),'Peru','PE',TRUE,'Migration Team'),
  (UUID(),'Philippines','PH',TRUE,'Migration Team'),
  (UUID(),'Pitcairn','PN',TRUE,'Migration Team'),
  (UUID(),'Poland','PL',TRUE,'Migration Team'),
  (UUID(),'Portugal','PT',TRUE,'Migration Team'),
  (UUID(),'Puerto Rico','PR',TRUE,'Migration Team'),
  (UUID(),'Qatar','QA',TRUE,'Migration Team'),
  (UUID(),'Réunion','RE',TRUE,'Migration Team'),
  (UUID(),'Romania','RO',TRUE,'Migration Team'),
  (UUID(),'Russian Federation','RU',TRUE,'Migration Team'),
  (UUID(),'Rwanda','RW',TRUE,'Migration Team'),
  (UUID(),'Saint-Barthélemy','BL',TRUE,'Migration Team'),
  (UUID(),'Saint Helena','SH',TRUE,'Migration Team'),
  (UUID(),'Saint Kitts and Nevis','KN',TRUE,'Migration Team'),
  (UUID(),'Saint Lucia','LC',TRUE,'Migration Team'),
  (UUID(),'Saint-Martin (French part)','MF',TRUE,'Migration Team'),
  (UUID(),'Saint Pierre and Miquelon','PM',TRUE,'Migration Team'),
  (UUID(),'Saint Vincent and Grenadines','VC',TRUE,'Migration Team'),
  (UUID(),'Samoa','WS',TRUE,'Migration Team'),
  (UUID(),'San Marino','SM',TRUE,'Migration Team'),
  (UUID(),'Sao Tome and Principe','ST',TRUE,'Migration Team'),
  (UUID(),'Saudi Arabia','SA',TRUE,'Migration Team'),
  (UUID(),'Senegal','SN',TRUE,'Migration Team'),
  (UUID(),'Serbia','RS',TRUE,'Migration Team'),
  (UUID(),'Seychelles','SC',TRUE,'Migration Team'),
  (UUID(),'Sierra Leone','SL',TRUE,'Migration Team'),
  (UUID(),'Singapore','SG',TRUE,'Migration Team'),
  (UUID(),'Slovakia','SK',TRUE,'Migration Team'),
  (UUID(),'Slovenia','SI',TRUE,'Migration Team'),
  (UUID(),'Solomon Islands','SB',TRUE,'Migration Team'),
  (UUID(),'Somalia','SO',TRUE,'Migration Team'),
  (UUID(),'South Africa','ZA',TRUE,'Migration Team'),
  (UUID(),'South Georgia and the South Sandwich Islands','GS',TRUE,'Migration Team'),
  (UUID(),'South Sudan','SS',TRUE,'Migration Team'),
  (UUID(),'Spain','ES',TRUE,'Migration Team'),
  (UUID(),'Sri Lanka','LK',TRUE,'Migration Team'),
  (UUID(),'Sudan','SD',TRUE,'Migration Team'),
  (UUID(),'Suriname','SR',TRUE,'Migration Team'),
  (UUID(),'Svalbard and Jan Mayen Islands','SJ',TRUE,'Migration Team'),
  (UUID(),'Swaziland','SZ',TRUE,'Migration Team'),
  (UUID(),'Sweden','SE',TRUE,'Migration Team'),
  (UUID(),'Switzerland','CH',TRUE,'Migration Team'),
  (UUID(),'Syrian Arab Republic (Syria)','SY',TRUE,'Migration Team'),
  (UUID(),'Taiwan, Republic of China','TW',TRUE,'Migration Team'),
  (UUID(),'Tajikistan','TJ',TRUE,'Migration Team'),
  (UUID(),'Tanzania, United Republic of','TZ',TRUE,'Migration Team'),
  (UUID(),'Thailand','TH',TRUE,'Migration Team'),
  (UUID(),'Timor-Leste','TL',TRUE,'Migration Team'),
  (UUID(),'Togo','TG',TRUE,'Migration Team'),
  (UUID(),'Tokelau','TK',TRUE,'Migration Team'),
  (UUID(),'Tonga','TO',TRUE,'Migration Team'),
  (UUID(),'Trinidad and Tobago','TT',TRUE,'Migration Team'),
  (UUID(),'Tunisia','TN',TRUE,'Migration Team'),
  (UUID(),'Turkey','TR',TRUE,'Migration Team'),
  (UUID(),'Turkmenistan','TM',TRUE,'Migration Team'),
  (UUID(),'Turks and Caicos Islands','TC',TRUE,'Migration Team'),
  (UUID(),'Tuvalu','TV',TRUE,'Migration Team'),
  (UUID(),'Uganda','UG',TRUE,'Migration Team'),
  (UUID(),'Ukraine','UA',TRUE,'Migration Team'),
  (UUID(),'United Arab Emirates','AE',TRUE,'Migration Team'),
  (UUID(),'United Kingdom','GB',TRUE,'Migration Team'),
  (UUID(),'United States of America','US',TRUE,'Migration Team'),
  (UUID(),'US Minor Outlying Islands','UM',TRUE,'Migration Team'),
  (UUID(),'Uruguay','UY',TRUE,'Migration Team'),
  (UUID(),'Uzbekistan','UZ',TRUE,'Migration Team'),
  (UUID(),'Vanuatu','VU',TRUE,'Migration Team'),
  (UUID(),'Venezuela (Bolivarian Republic)','VE',TRUE,'Migration Team'),
  (UUID(),'Viet Nam','VN',TRUE,'Migration Team'),
  (UUID(),'Virgin Islands, US','VI',TRUE,'Migration Team'),
  (UUID(),'Wallis and Futuna Islands','WF',TRUE,'Migration Team'),
  (UUID(),'Western Sahara','EH',TRUE,'Migration Team'),
  (UUID(),'Yemen','YE',TRUE,'Migration Team'),
  (UUID(),'Zambia','ZM',TRUE,'Migration Team'),
  (UUID(),'Zimbabwe','ZW',TRUE,'Migration Team');
  /*END - INSERT OF NATIONALITIES*/


/*START - INSERT OF COUNTRIES*/
INSERT INTO squidpayv3.countries(id,DESCRIPTION,CODE,STATUS,user_created)
VALUES
  (UUID(),'Afghanistan','AF',TRUE,'Migration Team'),
  (UUID(),'Aland Islands','AX',TRUE,'Migration Team'),
  (UUID(),'Albania','AL',TRUE,'Migration Team'),
  (UUID(),'Algeria','DZ',TRUE,'Migration Team'),
  (UUID(),'American Samoa','AS',TRUE,'Migration Team'),
  (UUID(),'Andorra','AD',TRUE,'Migration Team'),
  (UUID(),'Angola','AO',TRUE,'Migration Team'),
  (UUID(),'Anguilla','AI',TRUE,'Migration Team'),
  (UUID(),'Antarctica','AQ',TRUE,'Migration Team'),
  (UUID(),'Antigua and Barbuda','AG',TRUE,'Migration Team'),
  (UUID(),'Argentina','AR',TRUE,'Migration Team'),
  (UUID(),'Armenia','AM',TRUE,'Migration Team'),
  (UUID(),'Aruba','AW',TRUE,'Migration Team'),
  (UUID(),'Australia','AU',TRUE,'Migration Team'),
  (UUID(),'Austria','AT',TRUE,'Migration Team'),
  (UUID(),'Azerbaijan','AZ',TRUE,'Migration Team'),
  (UUID(),'Bahamas','BS',TRUE,'Migration Team'),
  (UUID(),'Bahrain','BH',TRUE,'Migration Team'),
  (UUID(),'Bangladesh','BD',TRUE,'Migration Team'),
  (UUID(),'Barbados','BB',TRUE,'Migration Team'),
  (UUID(),'Belarus','BY',TRUE,'Migration Team'),
  (UUID(),'Belgium','BE',TRUE,'Migration Team'),
  (UUID(),'Belize','BZ',TRUE,'Migration Team'),
  (UUID(),'Benin','BJ',TRUE,'Migration Team'),
  (UUID(),'Bermuda','BM',TRUE,'Migration Team'),
  (UUID(),'Bhutan','BT',TRUE,'Migration Team'),
  (UUID(),'Bolivia','BO',TRUE,'Migration Team'),
  (UUID(),'Bosnia and Herzegovina','BA',TRUE,'Migration Team'),
  (UUID(),'Botswana','BW',TRUE,'Migration Team'),
  (UUID(),'Bouvet Island','BV',TRUE,'Migration Team'),
  (UUID(),'Brazil','BR',TRUE,'Migration Team'),
  (UUID(),'British Virgin Islands','VG',TRUE,'Migration Team'),
  (UUID(),'British Indian Ocean Territory','IO',TRUE,'Migration Team'),
  (UUID(),'Brunei Darussalam','BN',TRUE,'Migration Team'),
  (UUID(),'Bulgaria','BG',TRUE,'Migration Team'),
  (UUID(),'Burkina Faso','BF',TRUE,'Migration Team'),
  (UUID(),'Burundi','BI',TRUE,'Migration Team'),
  (UUID(),'Cambodia','KH',TRUE,'Migration Team'),
  (UUID(),'Cameroon','CM',TRUE,'Migration Team'),
  (UUID(),'Canada','CA',TRUE,'Migration Team'),
  (UUID(),'Cape Verde','CV',TRUE,'Migration Team'),
  (UUID(),'Cayman Islands','KY',TRUE,'Migration Team'),
  (UUID(),'Central African Republic','CF',TRUE,'Migration Team'),
  (UUID(),'Chad','TD',TRUE,'Migration Team'),
  (UUID(),'Chile','CL',TRUE,'Migration Team'),
  (UUID(),'China','CN',TRUE,'Migration Team'),
  (UUID(),'Hong Kong, SAR China','HK',TRUE,'Migration Team'),
  (UUID(),'Macao, SAR China','MO',TRUE,'Migration Team'),
  (UUID(),'Christmas Island','CX',TRUE,'Migration Team'),
  (UUID(),'Cocos (Keeling) Islands','CC',TRUE,'Migration Team'),
  (UUID(),'Colombia','CO',TRUE,'Migration Team'),
  (UUID(),'Comoros','KM',TRUE,'Migration Team'),
  (UUID(),'Congo (Brazzaville)','CG',TRUE,'Migration Team'),
  (UUID(),'Congo, (Kinshasa)','CD',TRUE,'Migration Team'),
  (UUID(),'Cook Islands','CK',TRUE,'Migration Team'),
  (UUID(),'Costa Rica','CR',TRUE,'Migration Team'),
  (UUID(),'Côte d''Ivoire','CI',TRUE,'Migration Team'),
  (UUID(),'Croatia','HR',TRUE,'Migration Team'),
  (UUID(),'Cuba','CU',TRUE,'Migration Team'),
  (UUID(),'Cyprus','CY',TRUE,'Migration Team'),
  (UUID(),'Czech Republic','CZ',TRUE,'Migration Team'),
  (UUID(),'Denmark','DK',TRUE,'Migration Team'),
  (UUID(),'Djibouti','DJ',TRUE,'Migration Team'),
  (UUID(),'Dominica','DM',TRUE,'Migration Team'),
  (UUID(),'Dominican Republic','DO',TRUE,'Migration Team'),
  (UUID(),'Ecuador','EC',TRUE,'Migration Team'),
  (UUID(),'Egypt','EG',TRUE,'Migration Team'),
  (UUID(),'El Salvador','SV',TRUE,'Migration Team'),
  (UUID(),'Equatorial Guinea','GQ',TRUE,'Migration Team'),
  (UUID(),'Eritrea','ER',TRUE,'Migration Team'),
  (UUID(),'Estonia','EE',TRUE,'Migration Team'),
  (UUID(),'Ethiopia','ET',TRUE,'Migration Team'),
  (UUID(),'Falkland Islands (Malvinas)','FK',TRUE,'Migration Team'),
  (UUID(),'Faroe Islands','FO',TRUE,'Migration Team'),
  (UUID(),'Fiji','FJ',TRUE,'Migration Team'),
  (UUID(),'Finland','FI',TRUE,'Migration Team'),
  (UUID(),'France','FR',TRUE,'Migration Team'),
  (UUID(),'French Guiana','GF',TRUE,'Migration Team'),
  (UUID(),'French Polynesia','PF',TRUE,'Migration Team'),
  (UUID(),'French Southern Territories','TF',TRUE,'Migration Team'),
  (UUID(),'Gabon','GA',TRUE,'Migration Team'),
  (UUID(),'Gambia','GM',TRUE,'Migration Team'),
  (UUID(),'Georgia','GE',TRUE,'Migration Team'),
  (UUID(),'Germany','DE',TRUE,'Migration Team'),
  (UUID(),'Ghana','GH',TRUE,'Migration Team'),
  (UUID(),'Gibraltar','GI',TRUE,'Migration Team'),
  (UUID(),'Greece','GR',TRUE,'Migration Team'),
  (UUID(),'Greenland','GL',TRUE,'Migration Team'),
  (UUID(),'Grenada','GD',TRUE,'Migration Team'),
  (UUID(),'Guadeloupe','GP',TRUE,'Migration Team'),
  (UUID(),'Guam','GU',TRUE,'Migration Team'),
  (UUID(),'Guatemala','GT',TRUE,'Migration Team'),
  (UUID(),'Guernsey','GG',TRUE,'Migration Team'),
  (UUID(),'Guinea','GN',TRUE,'Migration Team'),
  (UUID(),'Guinea-Bissau','GW',TRUE,'Migration Team'),
  (UUID(),'Guyana','GY',TRUE,'Migration Team'),
  (UUID(),'Haiti','HT',TRUE,'Migration Team'),
  (UUID(),'Heard and Mcdonald Islands','HM',TRUE,'Migration Team'),
  (UUID(),'Holy See (Vatican City State)','VA',TRUE,'Migration Team'),
  (UUID(),'Honduras','HN',TRUE,'Migration Team'),
  (UUID(),'Hungary','HU',TRUE,'Migration Team'),
  (UUID(),'Iceland','IS',TRUE,'Migration Team'),
  (UUID(),'India','IN',TRUE,'Migration Team'),
  (UUID(),'Indonesia','ID',TRUE,'Migration Team'),
  (UUID(),'Iran, Islamic Republic of','IR',TRUE,'Migration Team'),
  (UUID(),'Iraq','IQ',TRUE,'Migration Team'),
  (UUID(),'Ireland','IE',TRUE,'Migration Team'),
  (UUID(),'Isle of Man','IM',TRUE,'Migration Team'),
  (UUID(),'Israel','IL',TRUE,'Migration Team'),
  (UUID(),'Italy','IT',TRUE,'Migration Team'),
  (UUID(),'Jamaica','JM',TRUE,'Migration Team'),
  (UUID(),'Japan','JP',TRUE,'Migration Team'),
  (UUID(),'Jersey','JE',TRUE,'Migration Team'),
  (UUID(),'Jordan','JO',TRUE,'Migration Team'),
  (UUID(),'Kazakhstan','KZ',TRUE,'Migration Team'),
  (UUID(),'Kenya','KE',TRUE,'Migration Team'),
  (UUID(),'Kiribati','KI',TRUE,'Migration Team'),
  (UUID(),'Korea (North)','KP',TRUE,'Migration Team'),
  (UUID(),'Korea (South)','KR',TRUE,'Migration Team'),
  (UUID(),'Kuwait','KW',TRUE,'Migration Team'),
  (UUID(),'Kyrgyzstan','KG',TRUE,'Migration Team'),
  (UUID(),'Lao PDR','LA',TRUE,'Migration Team'),
  (UUID(),'Latvia','LV',TRUE,'Migration Team'),
  (UUID(),'Lebanon','LB',TRUE,'Migration Team'),
  (UUID(),'Lesotho','LS',TRUE,'Migration Team'),
  (UUID(),'Liberia','LR',TRUE,'Migration Team'),
  (UUID(),'Libya','LY',TRUE,'Migration Team'),
  (UUID(),'Liechtenstein','LI',TRUE,'Migration Team'),
  (UUID(),'Lithuania','LT',TRUE,'Migration Team'),
  (UUID(),'Luxembourg','LU',TRUE,'Migration Team'),
  (UUID(),'Macedonia, Republic of','MK',TRUE,'Migration Team'),
  (UUID(),'Madagascar','MG',TRUE,'Migration Team'),
  (UUID(),'Malawi','MW',TRUE,'Migration Team'),
  (UUID(),'Malaysia','MY',TRUE,'Migration Team'),
  (UUID(),'Maldives','MV',TRUE,'Migration Team'),
  (UUID(),'Mali','ML',TRUE,'Migration Team'),
  (UUID(),'Malta','MT',TRUE,'Migration Team'),
  (UUID(),'Marshall Islands','MH',TRUE,'Migration Team'),
  (UUID(),'Martinique','MQ',TRUE,'Migration Team'),
  (UUID(),'Mauritania','MR',TRUE,'Migration Team'),
  (UUID(),'Mauritius','MU',TRUE,'Migration Team'),
  (UUID(),'Mayotte','YT',TRUE,'Migration Team'),
  (UUID(),'Mexico','MX',TRUE,'Migration Team'),
  (UUID(),'Micronesia, Federated States of','FM',TRUE,'Migration Team'),
  (UUID(),'Moldova','MD',TRUE,'Migration Team'),
  (UUID(),'Monaco','MC',TRUE,'Migration Team'),
  (UUID(),'Mongolia','MN',TRUE,'Migration Team'),
  (UUID(),'Montenegro','ME',TRUE,'Migration Team'),
  (UUID(),'Montserrat','MS',TRUE,'Migration Team'),
  (UUID(),'Morocco','MA',TRUE,'Migration Team'),
  (UUID(),'Mozambique','MZ',TRUE,'Migration Team'),
  (UUID(),'Myanmar','MM',TRUE,'Migration Team'),
  (UUID(),'Namibia','NA',TRUE,'Migration Team'),
  (UUID(),'Nauru','NR',TRUE,'Migration Team'),
  (UUID(),'Nepal','NP',TRUE,'Migration Team'),
  (UUID(),'Netherlands','NL',TRUE,'Migration Team'),
  (UUID(),'Netherlands Antilles','AN',TRUE,'Migration Team'),
  (UUID(),'New Caledonia','NC',TRUE,'Migration Team'),
  (UUID(),'New Zealand','NZ',TRUE,'Migration Team'),
  (UUID(),'Nicaragua','NI',TRUE,'Migration Team'),
  (UUID(),'Niger','NE',TRUE,'Migration Team'),
  (UUID(),'Nigeria','NG',TRUE,'Migration Team'),
  (UUID(),'Niue','NU',TRUE,'Migration Team'),
  (UUID(),'Norfolk Island','NF',TRUE,'Migration Team'),
  (UUID(),'Northern Mariana Islands','MP',TRUE,'Migration Team'),
  (UUID(),'Norway','NO',TRUE,'Migration Team'),
  (UUID(),'Oman','OM',TRUE,'Migration Team'),
  (UUID(),'Pakistan','PK',TRUE,'Migration Team'),
  (UUID(),'Palau','PW',TRUE,'Migration Team'),
  (UUID(),'Palestinian Territory','PS',TRUE,'Migration Team'),
  (UUID(),'Panama','PA',TRUE,'Migration Team'),
  (UUID(),'Papua New Guinea','PG',TRUE,'Migration Team'),
  (UUID(),'Paraguay','PY',TRUE,'Migration Team'),
  (UUID(),'Peru','PE',TRUE,'Migration Team'),
  (UUID(),'Philippines','PH',TRUE,'Migration Team'),
  (UUID(),'Pitcairn','PN',TRUE,'Migration Team'),
  (UUID(),'Poland','PL',TRUE,'Migration Team'),
  (UUID(),'Portugal','PT',TRUE,'Migration Team'),
  (UUID(),'Puerto Rico','PR',TRUE,'Migration Team'),
  (UUID(),'Qatar','QA',TRUE,'Migration Team'),
  (UUID(),'Réunion','RE',TRUE,'Migration Team'),
  (UUID(),'Romania','RO',TRUE,'Migration Team'),
  (UUID(),'Russian Federation','RU',TRUE,'Migration Team'),
  (UUID(),'Rwanda','RW',TRUE,'Migration Team'),
  (UUID(),'Saint-Barthélemy','BL',TRUE,'Migration Team'),
  (UUID(),'Saint Helena','SH',TRUE,'Migration Team'),
  (UUID(),'Saint Kitts and Nevis','KN',TRUE,'Migration Team'),
  (UUID(),'Saint Lucia','LC',TRUE,'Migration Team'),
  (UUID(),'Saint-Martin (French part)','MF',TRUE,'Migration Team'),
  (UUID(),'Saint Pierre and Miquelon','PM',TRUE,'Migration Team'),
  (UUID(),'Saint Vincent and Grenadines','VC',TRUE,'Migration Team'),
  (UUID(),'Samoa','WS',TRUE,'Migration Team'),
  (UUID(),'San Marino','SM',TRUE,'Migration Team'),
  (UUID(),'Sao Tome and Principe','ST',TRUE,'Migration Team'),
  (UUID(),'Saudi Arabia','SA',TRUE,'Migration Team'),
  (UUID(),'Senegal','SN',TRUE,'Migration Team'),
  (UUID(),'Serbia','RS',TRUE,'Migration Team'),
  (UUID(),'Seychelles','SC',TRUE,'Migration Team'),
  (UUID(),'Sierra Leone','SL',TRUE,'Migration Team'),
  (UUID(),'Singapore','SG',TRUE,'Migration Team'),
  (UUID(),'Slovakia','SK',TRUE,'Migration Team'),
  (UUID(),'Slovenia','SI',TRUE,'Migration Team'),
  (UUID(),'Solomon Islands','SB',TRUE,'Migration Team'),
  (UUID(),'Somalia','SO',TRUE,'Migration Team'),
  (UUID(),'South Africa','ZA',TRUE,'Migration Team'),
  (UUID(),'South Georgia and the South Sandwich Islands','GS',TRUE,'Migration Team'),
  (UUID(),'South Sudan','SS',TRUE,'Migration Team'),
  (UUID(),'Spain','ES',TRUE,'Migration Team'),
  (UUID(),'Sri Lanka','LK',TRUE,'Migration Team'),
  (UUID(),'Sudan','SD',TRUE,'Migration Team'),
  (UUID(),'Suriname','SR',TRUE,'Migration Team'),
  (UUID(),'Svalbard and Jan Mayen Islands','SJ',TRUE,'Migration Team'),
  (UUID(),'Swaziland','SZ',TRUE,'Migration Team'),
  (UUID(),'Sweden','SE',TRUE,'Migration Team'),
  (UUID(),'Switzerland','CH',TRUE,'Migration Team'),
  (UUID(),'Syrian Arab Republic (Syria)','SY',TRUE,'Migration Team'),
  (UUID(),'Taiwan, Republic of China','TW',TRUE,'Migration Team'),
  (UUID(),'Tajikistan','TJ',TRUE,'Migration Team'),
  (UUID(),'Tanzania, United Republic of','TZ',TRUE,'Migration Team'),
  (UUID(),'Thailand','TH',TRUE,'Migration Team'),
  (UUID(),'Timor-Leste','TL',TRUE,'Migration Team'),
  (UUID(),'Togo','TG',TRUE,'Migration Team'),
  (UUID(),'Tokelau','TK',TRUE,'Migration Team'),
  (UUID(),'Tonga','TO',TRUE,'Migration Team'),
  (UUID(),'Trinidad and Tobago','TT',TRUE,'Migration Team'),
  (UUID(),'Tunisia','TN',TRUE,'Migration Team'),
  (UUID(),'Turkey','TR',TRUE,'Migration Team'),
  (UUID(),'Turkmenistan','TM',TRUE,'Migration Team'),
  (UUID(),'Turks and Caicos Islands','TC',TRUE,'Migration Team'),
  (UUID(),'Tuvalu','TV',TRUE,'Migration Team'),
  (UUID(),'Uganda','UG',TRUE,'Migration Team'),
  (UUID(),'Ukraine','UA',TRUE,'Migration Team'),
  (UUID(),'United Arab Emirates','AE',TRUE,'Migration Team'),
  (UUID(),'United Kingdom','GB',TRUE,'Migration Team'),
  (UUID(),'United States of America','US',TRUE,'Migration Team'),
  (UUID(),'US Minor Outlying Islands','UM',TRUE,'Migration Team'),
  (UUID(),'Uruguay','UY',TRUE,'Migration Team'),
  (UUID(),'Uzbekistan','UZ',TRUE,'Migration Team'),
  (UUID(),'Vanuatu','VU',TRUE,'Migration Team'),
  (UUID(),'Venezuela (Bolivarian Republic)','VE',TRUE,'Migration Team'),
  (UUID(),'Viet Nam','VN',TRUE,'Migration Team'),
  (UUID(),'Virgin Islands, US','VI',TRUE,'Migration Team'),
  (UUID(),'Wallis and Futuna Islands','WF',TRUE,'Migration Team'),
  (UUID(),'Western Sahara','EH',TRUE,'Migration Team'),
  (UUID(),'Yemen','YE',TRUE,'Migration Team'),
  (UUID(),'Zambia','ZM',TRUE,'Migration Team'),
  (UUID(),'Zimbabwe','ZW',TRUE,'Migration Team');
  /*END - INSERT OF COUNTRIES*/
  

   /*START - INSERT OF CURRENCIES*/
    INSERT INTO squidpayv3.currencies
  (id,DESCRIPTION,CODE,STATUS,user_created)
VALUES
  (UUID(),'UAE Dirham','AED',TRUE,'Migration Team'),
  (UUID(),'Afghani','AFN',TRUE,'Migration Team'),
  (UUID(),'Lek','ALL',TRUE,'Migration Team'),
  (UUID(),'Armenian Dram','AMD',TRUE,'Migration Team'),
  (UUID(),'Netherlands Antillean Guilder','ANG',TRUE,'Migration Team'),
  (UUID(),'Kwanza','AOA',TRUE,'Migration Team'),
  (UUID(),'Argentine Peso','ARS',TRUE,'Migration Team'),
  (UUID(),'Australian Dollar','AUD',TRUE,'Migration Team'),
  (UUID(),'Aruban Florin','AWG',TRUE,'Migration Team'),
  (UUID(),'Azerbaijanian Manat','AZN',TRUE,'Migration Team'),
  (UUID(),'Convertible Mark','BAM',TRUE,'Migration Team'),
  (UUID(),'Barbados Dollar','BBD',TRUE,'Migration Team'),
  (UUID(),'Taka','BDT',TRUE,'Migration Team'),
  (UUID(),'Bulgarian Lev','BGN',TRUE,'Migration Team'),
  (UUID(),'Bahraini Dinar','BHD',TRUE,'Migration Team'),
  (UUID(),'Burundi Franc','BIF',TRUE,'Migration Team'),
  (UUID(),'Bermudian Dollar','BMD',TRUE,'Migration Team'),
  (UUID(),'Brunei Dollar','BND',TRUE,'Migration Team'),
  (UUID(),'Boliviano','BOB',TRUE,'Migration Team'),
  (UUID(),'Mvdol','BOV',TRUE,'Migration Team'),
  (UUID(),'Brazilian Real','BRL',TRUE,'Migration Team'),
  (UUID(),'Bahamian Dollar','BSD',TRUE,'Migration Team'),
  (UUID(),'Ngultrum','BTN',TRUE,'Migration Team'),
  (UUID(),'Pula','BWP',TRUE,'Migration Team'),
  (UUID(),'Belarussian Ruble','BYN',TRUE,'Migration Team'),
  (UUID(),'Belize Dollar','BZD',TRUE,'Migration Team'),
  (UUID(),'Canadian Dollar','CAD',TRUE,'Migration Team'),
  (UUID(),'Congolese Franc','CDF',TRUE,'Migration Team'),
  (UUID(),'WIR Euro','CHE',TRUE,'Migration Team'),
  (UUID(),'Swiss Franc','CHF',TRUE,'Migration Team'),
  (UUID(),'WIR Franc','CHW',TRUE,'Migration Team'),
  (UUID(),'Unidad de Fomento','CLF',TRUE,'Migration Team'),
  (UUID(),'Chilean Peso','CLP',TRUE,'Migration Team'),
  (UUID(),'Yuan Renminbi','CNY',TRUE,'Migration Team'),
  (UUID(),'Colombian Peso','COP',TRUE,'Migration Team'),
  (UUID(),'Unidad de Valor Real','COU',TRUE,'Migration Team'),
  (UUID(),'Costa Rican Colon','CRC',TRUE,'Migration Team'),
  (UUID(),'Peso Convertible','CUC',TRUE,'Migration Team'),
  (UUID(),'Cuban Peso','CUP',TRUE,'Migration Team'),
  (UUID(),'Cabo Verde Escudo','CVE',TRUE,'Migration Team'),
  (UUID(),'Czech Koruna','CZK',TRUE,'Migration Team'),
  (UUID(),'Djibouti Franc','DJF',TRUE,'Migration Team'),
  (UUID(),'Danish Krone','DKK',TRUE,'Migration Team'),
  (UUID(),'Dominican Peso','DOP',TRUE,'Migration Team'),
  (UUID(),'Algerian Dinar','DZD',TRUE,'Migration Team'),
  (UUID(),'Egyptian Pound','EGP',TRUE,'Migration Team'),
  (UUID(),'Nakfa','ERN',TRUE,'Migration Team'),
  (UUID(),'Ethiopian Birr','ETB',TRUE,'Migration Team'),
  (UUID(),'Euro','EUR',TRUE,'Migration Team'),
  (UUID(),'Fiji Dollar','FJD',TRUE,'Migration Team'),
  (UUID(),'Falkland Islands Pound','FKP',TRUE,'Migration Team'),
  (UUID(),'Pound Sterling','GBP',TRUE,'Migration Team'),
  (UUID(),'Lari','GEL',TRUE,'Migration Team'),
  (UUID(),'Ghana Cedi','GHS',TRUE,'Migration Team'),
  (UUID(),'Gibraltar Pound','GIP',TRUE,'Migration Team'),
  (UUID(),'Dalasi','GMD',TRUE,'Migration Team'),
  (UUID(),'Guinea Franc','GNF',TRUE,'Migration Team'),
  (UUID(),'Quetzal','GTQ',TRUE,'Migration Team'),
  (UUID(),'Guyana Dollar','GYD',TRUE,'Migration Team'),
  (UUID(),'Hong Kong Dollar','HKD',TRUE,'Migration Team'),
  (UUID(),'Lempira','HNL',TRUE,'Migration Team'),
  (UUID(),'Kuna','HRK',TRUE,'Migration Team'),
  (UUID(),'Gourde','HTG',TRUE,'Migration Team'),
  (UUID(),'Forint','HUF',TRUE,'Migration Team'),
  (UUID(),'Rupiah','IDR',TRUE,'Migration Team'),
  (UUID(),'New Israeli Sheqel','ILS',TRUE,'Migration Team'),
  (UUID(),'Indian Rupee','INR',TRUE,'Migration Team'),
  (UUID(),'Iraqi Dinar','IQD',TRUE,'Migration Team'),
  (UUID(),'Iranian Rial','IRR',TRUE,'Migration Team'),
  (UUID(),'Iceland Krona','ISK',TRUE,'Migration Team'),
  (UUID(),'Jamaican Dollar','JMD',TRUE,'Migration Team'),
  (UUID(),'Jordanian Dinar','JOD',TRUE,'Migration Team'),
  (UUID(),'Yen','JPY',TRUE,'Migration Team'),
  (UUID(),'Kenyan Shilling','KES',TRUE,'Migration Team'),
  (UUID(),'Som','KGS',TRUE,'Migration Team'),
  (UUID(),'Riel','KHR',TRUE,'Migration Team'),
  (UUID(),'Comoro Franc','KMF',TRUE,'Migration Team'),
  (UUID(),'North Korean Won','KPW',TRUE,'Migration Team'),
  (UUID(),'Won','KRW',TRUE,'Migration Team'),
  (UUID(),'Kuwaiti Dinar','KWD',TRUE,'Migration Team'),
  (UUID(),'Cayman Islands Dollar','KYD',TRUE,'Migration Team'),
  (UUID(),'Tenge','KZT',TRUE,'Migration Team'),
  (UUID(),'Kip','LAK',TRUE,'Migration Team'),
  (UUID(),'Lebanese Pound','LBP',TRUE,'Migration Team'),
  (UUID(),'Sri Lanka Rupee','LKR',TRUE,'Migration Team'),
  (UUID(),'Liberian Dollar','LRD',TRUE,'Migration Team'),
  (UUID(),'Loti','LSL',TRUE,'Migration Team'),
  (UUID(),'Libyan Dinar','LYD',TRUE,'Migration Team'),
  (UUID(),'Moroccan Dirham','MAD',TRUE,'Migration Team'),
  (UUID(),'Moldovan Leu','MDL',TRUE,'Migration Team'),
  (UUID(),'Malagasy Ariary','MGA',TRUE,'Migration Team'),
  (UUID(),'Denar','MKD',TRUE,'Migration Team'),
  (UUID(),'Kyat','MMK',TRUE,'Migration Team'),
  (UUID(),'Tugrik','MNT',TRUE,'Migration Team'),
  (UUID(),'Pataca','MOP',TRUE,'Migration Team'),
  (UUID(),'Ouguiya','MRU',TRUE,'Migration Team'),
  (UUID(),'Mauritius Rupee','MUR',TRUE,'Migration Team'),
  (UUID(),'Rufiyaa','MVR',TRUE,'Migration Team'),
  (UUID(),'Kwacha','MWK',TRUE,'Migration Team'),
  (UUID(),'Mexican Peso','MXN',TRUE,'Migration Team'),
  (UUID(),'Mexican Unidad de Inversion (UDI)','MXV',TRUE,'Migration Team'),
  (UUID(),'Malaysian Ringgit','MYR',TRUE,'Migration Team'),
  (UUID(),'Mozambique Metical','MZN',TRUE,'Migration Team'),
  (UUID(),'Namibia Dollar','NAD',TRUE,'Migration Team'),
  (UUID(),'Naira','NGN',TRUE,'Migration Team'),
  (UUID(),'Cordoba Oro','NIO',TRUE,'Migration Team'),
  (UUID(),'Norwegian Krone','NOK',TRUE,'Migration Team'),
  (UUID(),'Nepalese Rupee','NPR',TRUE,'Migration Team'),
  (UUID(),'New Zealand Dollar','NZD',TRUE,'Migration Team'),
  (UUID(),'Rial Omani','OMR',TRUE,'Migration Team'),
  (UUID(),'Balboa','PAB',TRUE,'Migration Team'),
  (UUID(),'Nuevo Sol','PEN',TRUE,'Migration Team'),
  (UUID(),'Kina','PGK',TRUE,'Migration Team'),
  (UUID(),'Philippine Peso','PHP',TRUE,'Migration Team'),
  (UUID(),'Pakistan Rupee','PKR',TRUE,'Migration Team'),
  (UUID(),'Zloty','PLN',TRUE,'Migration Team'),
  (UUID(),'Guarani','PYG',TRUE,'Migration Team'),
  (UUID(),'Qatari Rial','QAR',TRUE,'Migration Team'),
  (UUID(),'Romanian Leu','RON',TRUE,'Migration Team'),
  (UUID(),'Serbian Dinar','RSD',TRUE,'Migration Team'),
  (UUID(),'Russian Ruble','RUB',TRUE,'Migration Team'),
  (UUID(),'Rwanda Franc','RWF',TRUE,'Migration Team'),
  (UUID(),'Saudi Riyal','SAR',TRUE,'Migration Team'),
  (UUID(),'Solomon Islands Dollar','SBD',TRUE,'Migration Team'),
  (UUID(),'Seychelles Rupee','SCR',TRUE,'Migration Team'),
  (UUID(),'Sudanese Pound','SDG',TRUE,'Migration Team'),
  (UUID(),'Swedish Krona','SEK',TRUE,'Migration Team'),
  (UUID(),'Singapore Dollar','SGD',TRUE,'Migration Team'),
  (UUID(),'Saint Helena Pound','SHP',TRUE,'Migration Team'),
  (UUID(),'Leone','SLL',TRUE,'Migration Team'),
  (UUID(),'Somali Shilling','SOS',TRUE,'Migration Team'),
  (UUID(),'Surinam Dollar','SRD',TRUE,'Migration Team'),
  (UUID(),'South Sudanese Pound','SSP',TRUE,'Migration Team'),
  (UUID(),'Dobra','STN',TRUE,'Migration Team'),
  (UUID(),'El Salvador Colon','SVC',TRUE,'Migration Team'),
  (UUID(),'Syrian Pound','SYP',TRUE,'Migration Team'),
  (UUID(),'Lilangeni','SZL',TRUE,'Migration Team'),
  (UUID(),'Baht','THB',TRUE,'Migration Team'),
  (UUID(),'Somoni','TJS',TRUE,'Migration Team'),
  (UUID(),'Turkmenistan New Manat','TMT',TRUE,'Migration Team'),
  (UUID(),'Tunisian Dinar','TND',TRUE,'Migration Team'),
  (UUID(),'Pa’anga','TOP',TRUE,'Migration Team'),
  (UUID(),'Turkish Lira','TRY',TRUE,'Migration Team'),
  (UUID(),'Trinidad and Tobago Dollar','TTD',TRUE,'Migration Team'),
  (UUID(),'New Taiwan Dollar','TWD',TRUE,'Migration Team'),
  (UUID(),'Tanzanian Shilling','TZS',TRUE,'Migration Team'),
  (UUID(),'Hryvnia','UAH',TRUE,'Migration Team'),
  (UUID(),'Uganda Shilling','UGX',TRUE,'Migration Team'),
  (UUID(),'US Dollar','USD',TRUE,'Migration Team'),
  (UUID(),'US Dollar (Next day)','USN',TRUE,'Migration Team'),
  (UUID(),'Uruguay Peso en Unidades Indexadas (URUIURUI)','UYI',TRUE,'Migration Team'),
  (UUID(),'Peso Uruguayo','UYU',TRUE,'Migration Team'),
  (UUID(),'Uzbekistan Sum','UZS',TRUE,'Migration Team'),
  (UUID(),'Bolivar','VEF',TRUE,'Migration Team'),
  (UUID(),'Dong','VND',TRUE,'Migration Team'),
  (UUID(),'Vatu','VUV',TRUE,'Migration Team'),
  (UUID(),'Tala','WST',TRUE,'Migration Team'),
  (UUID(),'CFA Franc BEAC','XAF',TRUE,'Migration Team'),
  (UUID(),'East Caribbean Dollar','XCD',TRUE,'Migration Team'),
  (UUID(),'SDR (Special Drawing Right)','XDR',TRUE,'Migration Team'),
  (UUID(),'CFA Franc BCEAO','XOF',TRUE,'Migration Team'),
  (UUID(),'CFP Franc','XPF',TRUE,'Migration Team'),
  (UUID(),'Sucre','XSU',TRUE,'Migration Team'),
  (UUID(),'ADB Unit of Account','XUA',TRUE,'Migration Team'),
  (UUID(),'Yemeni Rial','YER',TRUE,'Migration Team'),
  (UUID(),'Rand','ZAR',TRUE,'Migration Team'),
  (UUID(),'Zambian Kwacha','ZMW',TRUE,'Migration Team'),
  (UUID(),'Zimbabwe Dollar','ZWL',TRUE,'Migration Team');
    /*END - INSERT OF CURRENCIES*/


  /*START - INSERT OF HOST*/
INSERT INTO squidpayv3.`signup_hosts`
  (id,DESCRIPTION,STATUS,user_created)
VALUES
(UUID(),'ACCOUNT.SQUID.PH',TRUE,'Migration Team'),
  (UUID(),'SQUIDPAY.PH',TRUE,'Migration Team');
    /*END - INSERT OF HOST*/
  
   /*START - INSERT OF SOURCE OF FUND*/
  INSERT INTO `squidpayv3`.`source_of_funds`
(id,DESCRIPTION,STATUS,user_created)
VALUES
  (UUID(),'Allowance',TRUE,'Migration Team'),
  (UUID(),'Business Proceeds',TRUE,'Migration Team'),
  (UUID(),'Pension',TRUE,'Migration Team'),
  (UUID(),'Remittance',TRUE,'Migration Team'),
  (UUID(),'Salary',TRUE,'Migration Team'),
  (UUID(),'Self-Employed',TRUE,'Migration Team'),
  (UUID(),'Commission',TRUE,'Migration Team'),
  (UUID(),'Other/s, please specify',TRUE,'Migration Team');
    /*END - INSERT OF SOURCE OF FUND*/
   
   
/*START - INSERT OF NATURE OF WORK*/
    INSERT INTO `squidpayv3`.`natures_of_work`
  (id,DESCRIPTION,STATUS,user_created)
VALUES
  (UUID(),'Accountant',TRUE,'Migration Team'),
  (UUID(),'BPO Companies',TRUE,'Migration Team'),
  (UUID(),'Banking',TRUE,'Migration Team'),
  (UUID(),'Brokerage/Securities Sector',TRUE,'Migration Team'),
  (UUID(),'Car Dealers',TRUE,'Migration Team'),
  (UUID(),'Casinos/Gaming Clubs/Lottery Outlet',TRUE,'Migration Team'),
  (UUID(),'Construction',TRUE,'Migration Team'),
  (UUID(),'Doctor/Dentist/other Medical Professionals',TRUE,'Migration Team'),
  (UUID(),'E-Money Issuers',TRUE,'Migration Team'),
  (UUID(),'Education (Teacher, Tutor, Professor, etc)',TRUE,'Migration Team'),
  (UUID(),'Embassies/Foreign Consulates',TRUE,'Migration Team'),
  (UUID(),'FX Dealer / Money Changer',TRUE,'Migration Team'),
  (UUID(),'Financial Services (Non Stock Savings and Loans Associations (NSSLs))',TRUE,'Migration Team'),
  (UUID(),'Freelance (e.g Writer, Buy and Sell)',TRUE,'Migration Team'),
  (UUID(),'Government Employees',TRUE,'Migration Team'),
  (UUID(),'Government Service (LGUs, etc)',TRUE,'Migration Team'),
  (UUID(),'Housewife/Househusband/Dependent',TRUE,'Migration Team'),
  (UUID(),'IT Companies',TRUE,'Migration Team'),
  (UUID(),'Insurance Sector',TRUE,'Migration Team'),
  (UUID(),'Jewelry Business',TRUE,'Migration Team'),
  (UUID(),'Lawyers and Notaries',TRUE,'Migration Team'),
  (UUID(),'Legal Practice (Firms)',TRUE,'Migration Team'),
  (UUID(),'Lending and Financing',TRUE,'Migration Team'),
  (UUID(),'Manning/Employment Agencies',TRUE,'Migration Team'),
  (UUID(),'Manufacturing',TRUE,'Migration Team'),
  (UUID(),'Military and Police',TRUE,'Migration Team'),
  (UUID(),'Multi Level Marketing',TRUE,'Migration Team'),
  (UUID(),'NGO/ Foundations/ Charities',TRUE,'Migration Team'),
  (UUID(),'OFW',TRUE,'Migration Team'),
  (UUID(),'Other/s, please specify',TRUE,'Migration Team'),
  (UUID(),'Pawnshop',TRUE,'Migration Team'),
  (UUID(),'Precious Metals and Stones Business',TRUE,'Migration Team'),
  (UUID(),'Real Estate',TRUE,'Migration Team'),
  (UUID(),'Religious Organizations',TRUE,'Migration Team'),
  (UUID(),'Remittance Agent',TRUE,'Migration Team'),
  (UUID(),'Retiree',TRUE,'Migration Team'),
  (UUID(),'Students',TRUE,'Migration Team'),
  (UUID(),'Transportation (Driver, Operator, etc.)',TRUE,'Migration Team'),
  (UUID(),'Virtual Currencies',TRUE,'Migration Team'),
  (UUID(),'e-commerce/online business',TRUE,'Migration Team');
  
  /*END - INSERT OF NATURE OF WORK*/  
    


/*START - INSERT OF ID TYPE*/
INSERT INTO `squidpayv3`.`id_types`
  (ID,TYPE,DESCRIPTION,STATUS,recommended,user_created)
VALUES
  (UUID(),'TIN ID','Tax Identification Card (TIN)',TRUE,NULL,'Migration Team'),
  (UUID(),'PASSPORT','Passport',TRUE,NULL,'Migration Team'),
  (UUID(),'BARANGAY CERTIFICATE','Barangay Certificate',TRUE,NULL,'Migration Team'),
  (UUID(),'SSS ID','Social Security System (SSS) Card ',TRUE,NULL,'Migration Team'),
  (UUID(),'UMID','Unified Multi-Purpose ID',TRUE,NULL,'Migration Team'),
  (UUID(),'COMPANY ID','Company ID(issued by private entities or institutions registered with or supervised or regulated either by the BSP, SEC or IC)',TRUE,NULL,'Migration Team'),
  (UUID(),'PHICB ','Philhealth Insurance Card ng Bayan (PHICB)',TRUE,NULL,'Migration Team'),
  (UUID(),'GOVERNMENT OFFICE AND GOCC ID','Government Office and Government Owned Corporation(GOCC) ID',TRUE,NULL,'Migration Team'),
  (UUID(),'VOTER''S ID','Voter''s ID',TRUE,NULL,'Migration Team'),
  (UUID(),'DRIVER''S LICENSE','Driver''s License',TRUE,NULL,'Migration Team'),
  (UUID(),'POLICE CLEARANCE','Police Clearance',TRUE,NULL,'Migration Team'),
  (UUID(),'NBI CLEARANCE','National Bureau of Investigation(NBI) Clearance',TRUE,NULL,'Migration Team'),
  (UUID(),'PRC ID','Professional Regulation Commission (PRC) ID',TRUE,NULL,'Migration Team'),
  (UUID(),'POSTAL ID','Postal ID',TRUE,NULL,'Migration Team'),
  (UUID(),'ACR/ICR','Alien Certification of Registration / Immigrant Certificate of Registration (ACR/ICR)',TRUE,NULL,'Migration Team'),
  (UUID(),'SENIOR CITIZEN CARD','Senior Citizen Card',TRUE,NULL,'Migration Team'),
  (UUID(),'SCHOOL ID','School ID',TRUE,NULL,'Migration Team'),
  (UUID(),'IBP ID','Integrated Bar of the Philippines ID',TRUE,NULL,'Migration Team'),
  (UUID(),'GSIS ID','Government Service Insurance System (GSIS) E-card',TRUE,NULL,'Migration Team'),
  (UUID(),'OWWA ID','Overseas Workers Welfare Administration (OWWA) ID',TRUE,NULL,'Migration Team'),
  (UUID(),'NCWDP CERTIFICATE','Certification from the National Council for the Welfare of Disabled Persons (NCWDP)',TRUE,NULL,'Migration Team'),
  (UUID(),'PWD ID','Person With Disabilities ID',TRUE,NULL,'Migration Team'),
  (UUID(),'DSWD CERTIFICATE','Department of Social Welfare and Development (DSWD) Certification ',TRUE,NULL,'Migration Team'),
  (UUID(),'SEAMAN''S BOOK','Seaman’s Book',TRUE,NULL,'Migration Team');
/*END - INSERT OF ID TYPE*/
    


/*START - INSERT NON-MERCHANT TO `user_accounts`*/
INSERT INTO `squidpayv3`.`user_accounts` (`id`, `entity_id`, `username`, `mobile_number`, `email`, `password`,
isadmin, STATUS, old_creation_date_time_from_v3_DB,  `pin_Code`, `user_created`,  `ismerchant`)
SELECT  UUID(),`MerchantID`, `username`, `mobile_number`,  `email`, `password` ,isadmin, TRUE AS STATUS,  
old_creation_date_time_from_v3_DB, pin_Code, 'Migration Team', FALSE AS `ismerchant`
FROM (
SELECT A.`MerchantID`, a.email AS `username`, C.mobilePhone AS mobile_number, C.emailAddress AS email,
`password`, FALSE AS isadmin,  A.`created_at` AS `old_creation_date_time_from_v3_DB`,
B. pin AS `pin_Code`
FROM `squidpay`.`users` A 
LEFT JOIN (SELECT DISTINCT EntityID, pin, fullname 
FROM `squidpay`.`otherinformation`) B ON B.`entityId` = A.`MerchantID` AND a.name = b.fullname 
LEFT JOIN (SELECT DISTINCT entityId, emailAddress, mobilePhone  FROM `squidpay`.`personalinformation`) C ON C.`entityId` = A.`MerchantID`
WHERE LEFT(A.`MerchantID`  , 1) <> 'M'
LIMIT 0,1000000
) A ;
/*END - INSERT NON-MERCHANT TO `user_accounts`*/



/*START - INSERT DETAILS OF NON-MERCHANT TO `user_details`*/
INSERT INTO `squidpayv3`.`user_details` 
(`ID`, `entity_id`, `user_account_id`, `title`, `lastName`, `firstname`, `middleName`, `birthdate`, `place_of_birth`, 
`maritialstatus_id`, `nationality_id`, `encoded_nationality`, `occupation`, `house_no_street`, `city`, `provice_state`, `municipality`, 
`country_id`, `encoded_country`, `postal_code`, `nature_of_work_id`, `encoded_nature_of_work`, `source_of_fund_id`, `encoded_source_of_fund`, 
`mother_maidenname`, `employer`, `currency_id`, `signup_host_id`, verification_Status,  emergency_lock_status, user_account_status,  `user_created`)
SELECT UUID(), `entity_id`, `user_account_id`, `title`, `lastName`, `firstname`, `middleName`, `birthdate`, `place_of_birth`, 
`maritialstatus_id`, `nationality_id`, `encoded_nationality`, `occupation`, `house_no_street`, `city`, `provice_state`, `municipality`, 
`country_id`, `encoded_country`, `postal_code`, `nature_of_work_id`, `encoded_nature_of_work`, `source_of_fund_id`, `encoded_source_of_fund`, 
`mother_maidenname`, `employer`, `currency_id`, `signup_host_id`, verification_Status,  emergency_lock_status, user_account_status, `user_created`
 FROM (
SELECT A.`MerchantID` AS `entity_id`, L.`id` AS `user_account_id`, B.Title, b.lastname, b.firstname, 
b.middlename, birthdate,
C.placeOfBirth AS place_of_birth, D.`id` AS  maritialstatus_id, j.id AS nationality_id, C.nationality AS `encoded_nationality`,  
c.occupation, E.`numberStreet` house_no_street , E.cityTown AS city, E.provState AS provice_state, '' AS municipality, 
f.id AS country_id, E.Country AS `encoded_country`, E.postalcode AS `postal_code`, 
IF(g.id IS NULL, (SELECT `id` FROM `squidpayv3`.`natures_of_work` WHERE `description` ='Other/s, please specify'), g.id) AS `nature_of_work_id` ,
c.natureOfWork AS `encoded_nature_of_work`, 
IF(K.id IS NULL, (SELECT `id` FROM `squidpayv3`.`source_of_funds` WHERE `description` ='Other/s, please specify'), K.id) AS `source_of_fund_id` 
, C.sourceOfFunds AS `encoded_source_of_fund`,
C.mothersMaidenName AS `mother_maidenname`, C.Employer AS `employer`,  
H.id AS `currency_id`, I.id AS `signup_host_id`,
C.`verificationStatus` AS `verification_Status`, 
IF(C.`emergencyLock`='LOCKED', TRUE, FALSE) AS `emergency_lock_status`, C.`status` AS `user_account_status` , 
'Migration Team' AS user_created
FROM `squidpay`.`users` A
LEFT JOIN (SELECT  DISTINCT entityId, Title, lastname, firstname, middlename, CONCAT(birthMm, "/", birthDd, "/", birthYear) AS birthdate
FROM `squidpay`.`personalinformation`) B ON A.`MerchantID` = B.`entityId`
LEFT JOIN (SELECT  DISTINCT entityid, fullname, placeOfBirth, nationality, Employer, maritalStatus, 
occupation, natureOfWork, sourceOfFunds, mothersMaidenName, baseCurrency, signupHost, 
emergencyLock, STATUS, verificationStatus  FROM 
 `squidpay`.`otherinformation` WHERE verificationStatus <> 'LOCKED' AND ( entityid <>'1619494535599' OR  occupation <> 'IT')) C ON C.`entityId` = A.`MerchantID` AND a.name = C.fullname 
LEFT JOIN `squidpayv3`.`maritial_status` D ON C.`maritalStatus`=D.`legend`
LEFT JOIN (SELECT DISTINCT numberStreet, cityTown, provState, Country, postalcode, entityId 
FROM `squidpay`.`presentaddress`) E ON A.`MerchantID`=E.`entityId`
LEFT JOIN `squidpayv3`.`countries` F ON F.code = E.country
LEFT JOIN `squidpayv3`.`natures_of_work` G ON g.`description` = c.natureOfWork
LEFT JOIN `squidpayv3`.`currencies` H ON H.`code` = c.baseCurrency
LEFT JOIN `squidpayv3`.`signup_hosts` I ON I.description = C.signupHost
LEFT JOIN `squidpayv3`.`nationalities` J ON C.nationality=j.code OR C.nationality=j.description
LEFT JOIN `squidpayv3`.`source_of_funds` K ON C.sourceOfFunds=K.`description`
LEFT JOIN `squidpayv3`.`user_accounts` L ON L.`entity_id`=A.`MerchantID`
WHERE A.`MerchantID` IN  (SELECT `entity_id` FROM `squidpayv3`.`user_accounts` WHERE LEFT(`entity_id`  , 1) <> 'M')) A;
/*END - INSERT DETAILS OF NON-MERCHANT TO `user_details`
DELETE FROM `squidpayv3`.`user_details`  where entity_id ='1619494535599' and occupation = 'IT';*/


/*START - INSERT DETAILS OF NON-MERCHANT TO `user_id_photos`*/
INSERT INTO `squidpayv3`.`user_id_photos` (`id`, `user_account_id`, `id_type_id`, `old_id_type`, 
`photo_location`, `approval_status`, `reviewed_by`, `user_created` )
SELECT UUID(), `user_account_id`, `id_type_id`, `old_id_type`, `photo_location`, `approval_status`, `reviewed_by`, `user_created` 
FROM (SELECT A.`id` AS `user_account_id`, C.id AS `id_type_id`, B.`poidType` AS `old_id_type`, '/asas/' AS `photo_location`, B.`approvalStatus`
AS `approval_status`, 
'Migration Team' AS`reviewed_by`, 'Migration Team' AS `user_created`
FROM `squidpayv3`.`user_accounts` A
LEFT JOIN `squidpay`.`govermentid` B ON B.`entityId`= A.`entity_id`
LEFT JOIN `squidpayv3`.`id_types` C ON B.`poidType`=C.`type`
WHERE B.`poidType` IS NOT NULL AND LENGTH(B.poidImage) > 1
) A;
/*END - INSERT DETAILS OF NON-MERCHANT TO `user_id_photos`*/



/*START - UPDATING COORECT ID TYPE*/
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='TIN ID'
WHERE old_id_type IN ('TIN', 'Tax Identification Card (TIN)', 'Tax Identification Card(TIN)');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='PASSPORT'
WHERE old_id_type IN ('PASSPORT');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='BARANGAY CERTIFICATE'
WHERE old_id_type IN ('Barangay Certification', 'BARANGAYCERT');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='SSS ID'
WHERE old_id_type IN ('SSSID');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='UMID'
WHERE old_id_type IN ('UMID');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='COMPANY ID'
WHERE old_id_type IN ('Company ID', 'COMPANYID');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='PHICB'
WHERE old_id_type IN ('PHICB', 'Philhealth Insurance Card ng Bayan (PHICB)');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='GOVERNMENT OFFICE AND GOCC ID'
WHERE old_id_type IN ('GOCCID', 'Government Office and GOCC ID');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='VOTER''S ID'
WHERE old_id_type IN ('VOTERID', 'Voters ID', 'Voter''s ID');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='DRIVER''S LICENSE'
WHERE old_id_type IN ('DRIVERLICENSE', 'Drivers License', 'Driver''s License', 'LICENSE');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='POLICE CLEARANCE'
WHERE old_id_type IN ('POLICECLEARANCE');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='NBI CLEARANCE'
WHERE old_id_type IN ('National Bureau of Investigation (NBI) Clearance');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='PRC ID'
WHERE old_id_type IN ('PRCID', 'Professional Regulation Commision (PRC) ID', 'Professional Regulation Commission');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='POSTAL ID'
WHERE old_id_type IN ('Postal ID', 'POSTALID');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='ACR/ICR'
WHERE old_id_type IN ('ACRICR', 'Alien Certification of Registration/Immigrant Certification of Registration');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='SENIOR CITIZEN CARD'
WHERE old_id_type IN ('Senior Citizen Card');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='SCHOOL ID'
WHERE old_id_type IN ('SCHOOL ID');
UPDATE `squidpayv3`.`user_id_photos` SET old_id_type ='OWWA ID'
WHERE old_id_type IN ('Overseas Filipino Workers (OFW) ID');
/*END - UPDATING COORECT ID TYPE*/


/*START - UPDATING RELATIONSHIP ID TYPE*/
UPDATE `squidpayv3`.`user_id_photos`
LEFT JOIN
`squidpayv3`.`id_types` 
ON 
`squidpayv3`.`id_types`.`type` = `squidpayv3`.`user_id_photos`.`old_id_type`
SET 
`squidpayv3`.`user_id_photos`.`id_type_id` = `squidpayv3`.`id_types`.`id`;
/*END - UPDATING RELATIONSHIP ID TYPE*/


/*START - UPDATE PHOTO LOCATION*/
UPDATE `squidpayv3`.`user_id_photos` SET `photo_location` = CONCAT("\\", id, ".", `id_type_id`, ".png");
/*END - UPDATE PHOTO LOCATION*/


/*START - INSERT MERCHANT TYPE*/
DELETE FROM `squidpayv3`.`merchat_types` ;
INSERT INTO `squidpayv3`.`merchat_types` (`id`, `description`, `status`, `user_created`)
SELECT UUID(), `description`, TRUE AS STATUS, 'Migration Team' AS `user_created` FROM  (
SELECT DISTINCT  TYPE AS `description` FROM `squidpay`.`otherusers`) A;
/*END - INSERT MERCHANT TYPE*/



/*START - INSERT MERCHANT*/
INSERT INTO `squidpayv3`.`user_accounts` (`id`, `merchant_id`, `username`, `mobile_number`, `email`, `password`,
isadmin, STATUS, old_creation_date_time_from_v3_DB,  `pin_Code`, `user_created`,`ismerchant`, merchant_type_id)
SELECT  UUID(),`MerchantID`, `username`, `mobile_number`,  `email`, `password` ,isadmin, TRUE AS STATUS,  
old_creation_date_time_from_v3_DB, pin_Code, 'Migration Team', TRUE AS `ismerchant`, merchant_type_id
FROM (
SELECT A.`MerchantID`, a.`Userid` AS `username`, C.phoneNumber AS mobile_number, C.emailAddress AS email,
`password`, FALSE AS isadmin,  A.`DateCreated` AS `old_creation_date_time_from_v3_DB`,NULL AS `pin_Code`, D.id AS merchant_type_id
FROM `squidpay`.`otherusers` A
LEFT JOIN (SELECT DISTINCT entityId, `emailAddress`, `phoneNumber` FROM `squidpay`.`merchantdetails`) C ON C.`entityId` = A.`MerchantID`
LEFT JOIN `squidpayv3`.`merchat_types` D ON D.`description`=A.`Type`
WHERE LEFT(A.`MerchantID`  , 1) = 'M'
LIMIT 0,1000000
) A;
/*END - INSERT MERCHANT*/


/*START - INSERT MERCHANT DETAILS*/
INSERT INTO `squidpayv3`.`merchant_details` (`id`, `user_account_id`,  `merchant_id`, `api_Key`, `name`, `address1`, 
`address2`, `city_town`, `province_state`, `country_id`, `postal_code`, `contact_info`, `contact_number`, `fax`, `email_address`, 
`tin`, `notes_comments`, `currency_id`, `credit_bank_percentage`, `swi_allow_negative_balance`, `payment_processing_fee`, 
`secondary_merchant`, `secondary_fee_merchant_id`, `integartions`, `authorizedIP`, `notification_nickname`, `notification_mobile_number1`, 
`notification_mobile_number2`, `notification_mobile_number3`, `settlements`, `bank_name`, `bank_code`, `account_name`, `account_number`, 
`billing_address`, `location`, `user_created`)
SELECT UUID(), `user_account_id`, `merchant_id`,`apiKey`, `entityName`, `address1`, `address2`, `cityTown`, provinceState ,
`country_id`, `postalZipCode`, `contactInfo`, phoneNumber, FaxNumber, emailAddress, tin, `creditBackRate`,
`swi_allow_negative_balance`, `procFeePct1`,`procFeePct2`,`procFee2EntityId`,
`redirectUrl`, `authzIpAddresses`, `nickname`, `mobileNumber1`, `mobileNumber2`, `mobileNumber3`, settlementEmail,
`BankName`, `bankCode`,`AccName`, `AccNumber`, billingAddr, location, comments, `currency_id`, `user_created`
FROM (
SELECT a.id AS `user_account_id`, A.`merchant_id`,  B.`apiKey`, B.`entityName`, B.`address1`, B.`address2`, B.`cityTown`, B.`provinceState` ,
IF(B.country IS NULL, NULL, (SELECT `id` FROM `squidpayv3`.`countries` WHERE CODE ='PH' )) AS `country_id`
, B.`postalZipCode`, B.`contactInfo`, B.phoneNumber, B.FaxNumber, B.emailAddress, B.tin, b.`creditBackRate`,
IF(B.`allowNegativeBal` = 1, TRUE, FALSE) AS `swi_allow_negative_balance`, B.`procFeePct1`, B.`procFeePct2`, B.`procFee2EntityId`,
b.`redirectUrl`, B.`authzIpAddresses`, B.`nickname`, B.`mobileNumber1`, B.`mobileNumber2`, B.`mobileNumber3`, B.settlementEmail,
B.`BankName`, B.`bankCode`, B.`AccName`, B.`AccNumber`, B.`billingAddr`, NULL AS location, 
B.comments, (SELECT `id` FROM `squidpayv3`.`currencies` WHERE CODE ='PHP') AS `currency_id`, 'Migration Team'  AS `user_created`
FROM `squidpayv3`.`user_accounts` A
LEFT JOIN `squidpay`.`merchantdetails` B ON A.`entity_id` = B.`entityId`
WHERE LEFT(A.`entity_id`  , 1) = 'M') A;
/*END - INSERT MERCHANT DETAILS*/



DELETE FROM `squidpayv3`.`merchant_categories`;
/*START - INSERT MERCHANT CATEGORY*/
INSERT INTO `squidpayv3`.`merchant_categories` 
(`id`, `merchant_id`,  `old_merchant_category_id`, `name`, `status`, `user_created`)
SELECT UUID(), merchant_id, `old_merchant_category_id`, CategoryName, STATUS, user_created FROM (
SELECT b.merchant_id, a.`Id` AS `old_merchant_category_id`, A.`CategoryName`, TRUE AS STATUS, 'Migration Team' AS user_created 
FROM `squidpay`.`merchantcategories` A
LEFT JOIN (SELECT DISTINCT `merchant_id` FROM `squidpayv3`.`user_accounts`) B ON a.merchantid=B.merchant_id) A
/*END - INSERT MERCHANT CATEGORY*/



INSERT INTO `squidpayv3`.`merchant_items` 
(`id`, `merchant_id`, `discount`, `item_code`, `name`, `stock`, `amount`, `status`, `user_created`, `user_updated`, 
`created_at`, `updated_at`, `expires_at`, `deleted_at`)
SELECT UUID(), `MerchantId`, `Discount`, `CategoryId`, `CategoryName`, `CategoryId` FROM `squidpay`.`merchantitems`





/*START - THIS IS CREATE QUERY FOR EXPORTING IMAGE*/
SELECT 
CONCAT("SELECT poidImage FROM squidpay.`govermentid` WHERE entityid = '", b.entity_id  ,"' "
, "INTO DUMPFILE 'C:\\Users\\RAN-PC\\Desktop\\BlobFile\\", a.id, ".png';" ) AS Coomand
FROM `squidpayv3`.`user_id_photos` A
LEFT JOIN `squidpayv3`.`user_accounts` B ON A.`user_account_id`=B.ID;
/*END - THIS IS CREATE QUERY FOR EXPORTING IMAGE*/

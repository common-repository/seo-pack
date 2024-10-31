<?php
/**
 *  GEO checker
 */

	$file_settings = dirname(__FILE__).'/settings.php';
	if (file_exists($file_settings)) include_once($file_settings);
	else return;

	$file_geo_class = dirname(__FILE__).'/geo.php';
	if (file_exists($file_geo_class)) include_once($file_geo_class);
	else return;

	$seo_sg_settings = (array)json_decode($seo_sg_settings, true);


	if (isset($seo_sg_settings['protection_frontend']) && $seo_sg_settings['protection_frontend'] == 1)
	{
		$myIP = RT_SEO_Geo_Check::GetMyIP();
		
		if (!RT_SEO_Geo_Check::Check_IP_in_list($myIP, $seo_sg_settings['frontend_ip_list_allow']))
		{

			$myCountryCode = RT_SEO_Geo_Check::GetCountryCode($myIP);
			$myCountryName = RT_SEO_Geo_Check::GetCountryName($myIP);
			
			if ( !RT_SEO_Geo_Check::Check_if_User_IP_allowed($myIP, $seo_sg_settings['frontend_ip_list']) )
			{
				// Log action
				$alert_data = array(
					'time' => time(),
					'ip' => $myIP,
					'country_code' => $myCountryCode,
					'url' => $_SERVER['REQUEST_URI']
				);
				RT_SEO_Geo_Check::Save_Block_alert($alert_data);
				RT_SEO_Geo_Check::BlockPage($myIP, $myCountryName);
			}

			if ( !RT_SEO_Geo_Check::Check_if_User_allowed($myCountryCode, json_decode($seo_sg_settings['frontend_country_list'], true)) )
			{

				// Log action
				$alert_data = array(
					'time' => time(),
					'ip' => $myIP,
					'country_code' => $myCountryCode,
					'url' => $_SERVER['REQUEST_URI']
				);
				RT_SEO_Geo_Check::Save_Block_alert($alert_data);
				RT_SEO_Geo_Check::BlockPage($myIP, $myCountryName);
			}
		}
	}



	class RT_SEO_Geo_Check
	{
		public static $country_list = array(
			"AF" => "Afghanistan",   // Afghanistan
			"AL" => "Albania",   // Albania
			"DZ" => "Algeria",   // Algeria
			"AS" => "American Samoa",   // American Samoa
			"AD" => "Andorra",   // Andorra 
			"AO" => "Angola",   // Angola
			"AI" => "Anguilla",   // Anguilla
			"AQ" => "Antarctica",   // Antarctica
			"AG" => "Antigua and Barbuda",   // Antigua and Barbuda
			"AR" => "Argentina",   // Argentina
			"AM" => "Armenia",   // Armenia
			"AW" => "Aruba",   // Aruba 
			"AU" => "Australia",   // Australia 
			"AT" => "Austria",   // Austria
			"AZ" => "Azerbaijan",   // Azerbaijan
			"BS" => "Bahamas",   // Bahamas
			"BH" => "Bahrain",   // Bahrain 
			"BD" => "Bangladesh",   // Bangladesh
			"BB" => "Barbados",   // Barbados 
			"BY" => "Belarus",   // Belarus 
			"BE" => "Belgium",   // Belgium
			"BZ" => "Belize",   // Belize
			"BJ" => "Benin",   // Benin
			"BM" => "Bermuda",   // Bermuda
			"BT" => "Bhutan",   // Bhutan
			"BO" => "Bolivia",   // Bolivia
			"BA" => "Bosnia and Herzegovina",   // Bosnia and Herzegovina
			"BW" => "Botswana",   // Botswana
			"BV" => "Bouvet Island",   // Bouvet Island
			"BR" => "Brazil",   // Brazil
			"IO" => "British Indian Ocean Territory",   // British Indian Ocean Territory
			"VG" => "British Virgin Islands",   // British Virgin Islands,
			"BN" => "Brunei Darussalam",   // Brunei Darussalam
			"BG" => "Bulgaria",   // Bulgaria
			"BF" => "Burkina Faso",   // Burkina Faso
			"BI" => "Burundi",   // Burundi
			"KH" => "Cambodia",   // Cambodia 
			"CM" => "Cameroon",   // Cameroon
			"CA" => "Canada",   // Canada 
			"CV" => "Cape Verde",   // Cape Verde
			"KY" => "Cayman Islands",   // Cayman Islands
			"CF" => "Central African Republic",   // Central African Republic
			"TD" => "Chad",   // Chad
			"CL" => "Chile",   // Chile
			"CN" => "China",   // China
			"CX" => "Christmas Island",   // Christmas Island
			"CC" => "Cocos (Keeling Islands)",   // Cocos (Keeling Islands)
			"CO" => "Colombia",   // Colombia
			"KM" => "Comoros",   // Comoros
			"CG" => "Congo",   // Congo 
			"CK" => "Cook Islands",   // Cook Islands
			"CR" => "Costa Rica",   // Costa Rica 
			"HR" => "Croatia (Hrvatska)",   // Croatia (Hrvatska
			"CY" => "Cyprus",   // Cyprus
			"CZ" => "Czech Republic",   // Czech Republic
			"CG" => "Democratic Republic of Congo",   // Democratic Republic of Congo,
			"DK" => "Denmark",   // Denmark
			"DJ" => "Djibouti",   // Djibouti
			"DM" => "Dominica",   // Dominica
			"DO" => "Dominican Republic",   // Dominican Republic
			"TP" => "East Timor",   // East Timor
			"EC" => "Ecuador",   // Ecuador
			"EG" => "Egypt",   // Egypt 
			"SV" => "El Salvador",   // El Salvador 
			"GQ" => "Equatorial Guinea",   // Equatorial Guinea
			"ER" => "Eritrea",   // Eritrea 
			"EE" => "Estonia",   // Estonia 
			"ET" => "Ethiopia",   // Ethiopia
			"FK" => "Falkland Islands (Malvinas)",   // Falkland Islands (Malvinas)
			"FO" => "Faroe Islands",   // Faroe Islands 
			"FM" => "Federated States of Micronesia",   // Federated States of Micronesia,
			"FJ" => "Fiji",   // Fiji
			"FI" => "Finland",   // Finland
			"FR" => "France",   // France
			"GF" => "French Guiana",   // French Guiana
			"PF" => "French Polynesia",   // French Polynesia
			"TF" => "French Southern Territories",   // French Southern Territories
			"GA" => "Gabon",   // Gabon
			"GM" => "Gambia",   // Gambia
			"GE" => "Georgia",   // Georgia
			"DE" => "Germany",   // Germany
			"GH" => "Ghana",   // Ghana
			"GI" => "Gibraltar",   // Gibraltar
			"GR" => "Greece",   // Greece
			"GL" => "Greenland",   // Greenland
			"GD" => "Grenada",   // Grenada 
			"GP" => "Guadeloupe",   // Guadeloupe
			"GU" => "Guam",   // Guam 
			"GT" => "Guatemala",   // Guatemala
			"GN" => "Guinea",   // Guinea
			"GW" => "Guinea-Bissau",   // Guinea-Bissau
			"GY" => "Guyana",   // Guyana
			"HT" => "Haiti",   // Haiti
			"HM" => "Heard and McDonald Islands",   // Heard and McDonald Islands
			"HN" => "Honduras",   // Honduras
			"HK" => "Hong Kong",   // Hong Kong
			"HU" => "Hungary",   // Hungary
			"IS" => "Iceland",   // Iceland
			"IN" => "India",   // India
			"ID" => "Indonesia",   // Indonesia
			"IR" => "Iran",   // Iran
			"IQ" => "Iraq",   // Iraq
			"IE" => "Ireland",   // Ireland
			"IL" => "Israel",   // Israel
			"IT" => "Italy",   // Italy
			"CI" => "Ivory Coast",   // Ivory Coast,
			"JM" => "Jamaica",   // Jamaica
			"JP" => "Japan",   // Japan 
			"JO" => "Jordan",   // Jordan 
			"KZ" => "Kazakhstan",   // Kazakhstan
			"KE" => "Kenya",   // Kenya 
			"KI" => "Kiribati",   // Kiribati 
			"KW" => "Kuwait",   // Kuwait
			"KG" => "Kuwait",   // Kyrgyzstan
			"LA" => "Laos",   // Laos
			"LV" => "Latvia",   // Latvia
			"LB" => "Lebanon",   // Lebanon
			"LS" => "Lesotho",   // Lesotho
			"LR" => "Liberia",   // Liberia 
			"LY" => "Libya",   // Libya
			"LI" => "Liechtenstein",   // Liechtenstein
			"LT" => "Lithuania",   // Lithuania
			"LU" => "Luxembourg",   // Luxembourg 
			"MO" => "Macau",   // Macau
			"MK" => "Macedonia",   // Macedonia
			"MG" => "Madagascar",   // Madagascar
			"MW" => "Malawi",   // Malawi
			"MY" => "Malaysia",   // Malaysia
			"MV" => "Maldives",   // Maldives
			"ML" => "Mali",   // Mali
			"MT" => "Malta",   // Malta
			"MH" => "Marshall Islands",   // Marshall Islands
			"MQ" => "Martinique",   // Martinique
			"MR" => "Mauritania",   // Mauritania
			"MU" => "Mauritius",   // Mauritius
			"YT" => "Mayotte",   // Mayotte
			"MX" => "Mexico",   // Mexico
			"MD" => "Moldova",   // Moldova
			"MC" => "Monaco",   // Monaco
			"MN" => "Mongolia",   // Mongolia
			"MS" => "Montserrat",   // Montserrat
			"MA" => "Morocco",   // Morocco
			"MZ" => "Mozambique",   // Mozambique
			"MM" => "Myanmar",   // Myanmar
			"NA" => "Namibia",   // Namibia
			"NR" => "Nauru",   // Nauru
			"NP" => "Nepal",   // Nepal
			"NL" => "Netherlands",   // Netherlands
			"AN" => "Netherlands Antilles",   // Netherlands Antilles
			"NC" => "New Caledonia",   // New Caledonia
			"NZ" => "New Zealand",   // New Zealand
			"NI" => "Nicaragua",   // Nicaragua
			"NE" => "Nicaragua",   // Niger
			"NG" => "Nigeria",   // Nigeria
			"NU" => "Niue",   // Niue
			"NF" => "Norfolk Island",   // Norfolk Island
			"KP" => "Korea (North)",   // Korea (North)
			"MP" => "Northern Mariana Islands",   // Northern Mariana Islands
			"NO" => "Norway",   // Norway
			"OM" => "Oman",   // Oman
			"PK" => "Pakistan",   // Pakistan
			"PW" => "Palau",   // Palau
			"PA" => "Panama",   // Panama
			"PG" => "Papua New Guinea",   // Papua New Guinea
			"PY" => "Paraguay",   // Paraguay
			"PE" => "Peru",   // Peru
			"PH" => "Philippines",   // Philippines
			"PN" => "Pitcairn",   // Pitcairn
			"PL" => "Poland",   // Poland
			"PT" => "Portugal",   // Portugal
			"PR" => "Puerto Rico",   // Puerto Rico
			"QA" => "Qatar",   // Qatar
			"RE" => "Reunion",   // Reunion
			"RO" => "Romania",   // Romania
			"RU" => "Russian Federation",   // Russian Federation
			"RW" => "Rwanda",   // Rwanda
			"SH" => "Saint Helena and Dependencies",   // Saint Helena and Dependencies,
			"KN" => "Saint Kitts and Nevis",   // Saint Kitts and Nevis
			"LC" => "Saint Lucia",   // Saint Lucia
			"VC" => "Saint Vincent and The Grenadines",   // Saint Vincent and The Grenadines
			"VC" => "Saint Vincent and the Grenadines",   // Saint Vincent and the Grenadines,
			"WS" => "Samoa",   // Samoa
			"SM" => "San Marino",   // San Marino
			"ST" => "Sao Tome and Principe",   // Sao Tome and Principe 
			"SA" => "Saudi Arabia",   // Saudi Arabia
			"SN" => "Senegal",   // Senegal
			"SC" => "Seychelles",   // Seychelles
			"SL" => "Sierra Leone",   // Sierra Leone
			"SG" => "Singapore",   // Singapore
			"SK" => "Slovak Republic",   // Slovak Republic
			"SI" => "Slovenia",   // Slovenia
			"SB" => "Solomon Islands",   // Solomon Islands
			"SO" => "Somalia",   // Somalia
			"ZA" => "South Africa",   // South Africa
			"GS" => "S. Georgia and S. Sandwich Isls.",   // S. Georgia and S. Sandwich Isls.
			"KR" => "South Korea",   // South Korea,
			"ES" => "Spain",   // Spain
			"LK" => "Sri Lanka",   // Sri Lanka
			"SR" => "Suriname",   // Suriname
			"SJ" => "Svalbard and Jan Mayen Islands",   // Svalbard and Jan Mayen Islands
			"SZ" => "Swaziland",   // Swaziland
			"SE" => "Sweden",   // Sweden
			"CH" => "Switzerland",   // Switzerland
			"SY" => "Syria",   // Syria
			"TW" => "Taiwan",   // Taiwan
			"TJ" => "Tajikistan",   // Tajikistan
			"TZ" => "Tanzania",   // Tanzania
			"TH" => "Thailand",   // Thailand
			"TG" => "Togo",   // Togo
			"TK" => "Tokelau",   // Tokelau
			"TO" => "Tonga",   // Tonga
			"TT" => "Trinidad and Tobago",   // Trinidad and Tobago
			"TN" => "Tunisia",   // Tunisia
			"TR" => "Turkey",   // Turkey
			"TM" => "Turkmenistan",   // Turkmenistan
			"TC" => "Turks and Caicos Islands",   // Turks and Caicos Islands
			"TV" => "Tuvalu",   // Tuvalu
			"UG" => "Uganda",   // Uganda
			"UA" => "Ukraine",   // Ukraine
			"AE" => "United Arab Emirates",   // United Arab Emirates
			"UK" => "United Kingdom",   // United Kingdom
			"US" => "United States",   // United States
			"UM" => "US Minor Outlying Islands",   // US Minor Outlying Islands
			"UY" => "Uruguay",   // Uruguay
			"VI" => "US Virgin Islands",   // US Virgin Islands,
			"UZ" => "Uzbekistan",   // Uzbekistan
			"VU" => "Vanuatu",   // Vanuatu
			"VA" => "Vatican City State (Holy See)",   // Vatican City State (Holy See)
			"VE" => "Venezuela",   // Venezuela
			"VN" => "Viet Nam",   // Viet Nam
			"WF" => "Wallis and Futuna Islands",   // Wallis and Futuna Islands
			"EH" => "Western Sahara",   // Western Sahara
			"YE" => "Yemen",   // Yemen
			"ZM" => "Zambia",   // Zambia
			"ZW" => "Zimbabwe",   // Zimbabwe
			"CU" => "Cuba",   // Cuba,
			"IR" => "Iran",   // Iran,
		);

		
		public static function GetMyIP()
		{
			return $_SERVER["REMOTE_ADDR"];
		}

		public static function Check_IP_in_list($ip, $ip_list = '')
		{
			if ($ip_list == '') return false;   // IP is not in the list
			
			$ip_list = str_replace(array(".*.*.*", ".*.*", ".*"), ".", trim($ip_list));
			$ip_list = explode("\n", $ip_list);
			if (count($ip_list))
			{
				foreach ($ip_list as $rule_ip)
				{
					if (strpos($ip, $rule_ip) === 0) 
					{
						// match
						return true;    // IP is in the list
					}
				}
			}
			
			return  false;   // IP is not in the list
		}
		
		public static function Check_if_User_IP_allowed($ip, $ip_list = '')
		{
			if ($ip_list == '') return true;
			
			$ip_list = str_replace(array(".*.*.*", ".*.*", ".*"), ".", trim($ip_list));
			$ip_list = explode("\n", $ip_list);
			if (count($ip_list))
			{
				foreach ($ip_list as $rule_ip)
				{
					if (strpos($ip, $rule_ip) === 0) 
					{
						// match
						return false;
					}
				}
			}
			
			return true;
		}

		public static function GetCountryCode($ip)
		{
			if (isset($_COOKIE["GEO_country_code"]) && isset($_COOKIE["GEO_country_code_hash"]))
			{
				$cookie_GEO_country_code = trim($_COOKIE["GEO_country_code"]);
				$cookie_GEO_country_code_hash = trim($_COOKIE["GEO_country_code_hash"]);
				
				$hash = md5($ip.'-'.$cookie_GEO_country_code);
				if ($cookie_GEO_country_code_hash == $hash) return $cookie_GEO_country_code;
			}
			
			if (!class_exists('RT_SEO_Geo_IP2Country'))
			{
				include_once(dirname(__FILE__).DIRSEP.'geo.php');
			}
			
			$geo = new RT_SEO_Geo_IP2Country;
			$country_code = $geo->getCountryByIP($ip); 
 

			
			if ($country_code != '')
			{
				// Set cookie
				$hash = md5($ip.'-'.$country_code);
				@setcookie("GEO_country_code", $country_code, time()+3600*24);
				@setcookie("GEO_country_code_hash", $hash, time()+3600*24);
			}
			
			return $country_code;
		}

		public static function GetCountryName($ip)
		{
			if (!class_exists('RT_SEO_Geo_IP2Country'))
			{
				include_once(dirname(__FILE__).DIRSEP.'geo.php');
			}
			
			$geo = new RT_SEO_Geo_IP2Country;
			$country_code = $geo->getCountryByIP($ip); 
			$country_name = $geo->getNameByCountryCode($country_code); 
 
			
			return $country_name;
		}
		
		public static function Check_if_User_allowed($myCountryCode, $blocked_country_list = array())
		{
			if (count($blocked_country_list) && in_array($myCountryCode, $blocked_country_list)) return false;
			return true;
		}
		
		
		public static function Save_Block_alert($alert_data)
		{
			$sql_array = array(
				'time' => intval($alert_data['time']),
				'ip' => $alert_data['ip'],
				'country_code' => $alert_data['country_code'],
				'url' => addslashes($alert_data['url']),
			);
			
			$file_tmp_block_log = dirname(__FILE__).'/block.log';
			$fp = fopen($file_tmp_block_log, 'a');
			fwrite($fp, json_encode($sql_array)."\n");
			fclose($fp);
		}
		
		

		public static function BlockPage($myIP, $country_name = '')
		{
			?><html><head>
			</head>
			<body>
			<div style="margin:100px auto; max-width: 500px;text-align: center;">
				<p><img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9Ijc0IDE2NSAxOTk2IDQ3MiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyA3NCAxNjUgMTk5NiA0NzI7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtzdHJva2U6IzAwMDAwMDtzdHJva2Utd2lkdGg6Mjt9DQoJLnN0MXtmaWxsOiNGRkZGRkY7c3Ryb2tlOiMwMDAwMDA7c3Ryb2tlLXdpZHRoOjI7fQ0KCS5zdDJ7ZmlsbDojRUUzMTI0O30NCgkuc3Qze2ZvbnQtZmFtaWx5OidOYXNhbGl6YXRpb24nO30NCgkuc3Q0e2ZvbnQtc2l6ZToyOTYuMjVweDt9DQo8L3N0eWxlPg0KPHRpdGxlPnNoaWVsZC1ncmVlbjwvdGl0bGU+DQo8Zz4NCgk8dGl0bGU+TGF5ZXIgMTwvdGl0bGU+DQoJPHBhdGggaWQ9InBhdGgyIiBjbGFzcz0ic3QwIiBkPSJNMjkxLjEsMTc3aDEuNmw3LjEsMy42YzQuNywyLjQsNy44LDQsOS40LDQuN2MxLjUsMC43LDYuNywzLjIsMTUuNSw3LjJjOC44LDQuMSwxMy40LDYuMSwxMy43LDYuMg0KCQljMC4zLDAsMi42LDEsNywyLjljNC40LDEuOSw3LjgsMy4zLDEwLjEsNC4yYzIuNCwwLjksNS44LDIuMSwxMC4yLDMuN2M0LjUsMS42LDcuMywyLjYsOC41LDNjMS4yLDAuNCwyLjcsMC45LDQuNCwxLjUNCgkJYzEuOCwwLjYsNCwxLjMsNi44LDIuMWMyLjgsMC44LDYuMywxLjgsMTAuNiwzYzQuMywxLjIsOC41LDIuMiwxMi41LDMuMWM0LDAuOSw2LjcsMS41LDcuOCwxLjdzNC45LDAuOSwxMS4zLDIuMQ0KCQljNi4zLDEuMiwxMC4zLDEuOSwxMS45LDIuMXM1LjUsMC45LDExLjgsMS45YzYuMiwxLDkuNCwxLjUsOS41LDEuNWMwLjEsMCwzLjEsMC42LDkuMSwxLjdjNiwxLjIsOSwxLjgsOSwxLjhsMC4xLDAuMWwtMC40LDE4LjQNCgkJYy0wLjMsMTIuMy0wLjksMjguMi0xLjksNDcuNmMtMSwxOS41LTEuOCwzMy4xLTIuNCw0MC44Yy0wLjYsNy44LTEuMywxNC40LTIsMTkuOWMtMC44LDUuNS0xLjYsMTAuOC0yLjcsMTUuNw0KCQljLTEsNC45LTIuMSw5LjktMy4zLDE1Yy0xLjIsNS0yLjIsOS40LTMuMiwxMy4yYy0wLjksMy44LTIsNy45LTMuMywxMi4yYy0xLjMsNC40LTIsNy0yLjMsNy45Yy0wLjMsMC45LTAuNiwyLjEtMS4xLDMuNQ0KCQljLTAuNSwxLjQtMS4xLDMuMS0xLjgsNS4yYy0wLjcsMi4xLTEuNyw1LTMuMiw4LjljLTEuNCwzLjktMi42LDctMy41LDkuM3MtMi4xLDUuMS0zLjQsOC4zYy0xLjMsMy4yLTMuMSw3LjEtNS4yLDExLjYNCgkJcy0zLjQsNy4yLTMuOCw3LjljLTAuNCwwLjctMiwzLjQtNC42LDguMWMtMi43LDQuNy00LjMsNy40LTQuOCw4LjNjLTAuNSwwLjktMS45LDMuMS0zLjksNi42Yy0yLjEsMy41LTMuNCw1LjctNCw2LjcNCgkJYy0wLjYsMC45LTEuNiwyLjQtMi45LDQuNWMtMS4zLDItMi40LDMuNi0zLjIsNC43cy0xLjcsMi40LTIuOCw0Yy0xLjEsMS41LTIuMSwyLjktMyw0LjJzLTEuNywyLjQtMi4zLDMuMmMtMC41LDAuOS0xLjMsMi0yLjQsMy4zDQoJCWMtMS4xLDEuNC0yLjEsMi43LTMsNC4xYy0xLDEuNC0yLDIuOC0zLjIsNC4yYy0xLjEsMS40LTIuMSwyLjctMi45LDMuN2MtMC44LDEtMi41LDMtNS4xLDUuOWMtMi42LDIuOS01LjcsNi4yLTkuMiw5LjkNCgkJYy0zLjUsMy43LTYuNyw2LjktOS40LDkuNWMtMi43LDIuNi00LjUsNC4zLTUuNCw1Yy0wLjksMC43LTIuOSwyLjUtNi4yLDUuM3MtNS4yLDQuNi01LjksNS4yYy0wLjcsMC42LTIuNSwyLjEtNS4zLDQuNg0KCQljLTIuOCwyLjQtNC43LDQtNS42LDQuN2MtMC45LDAuNy0xLjksMS41LTMsMi41Yy0xLjEsMC45LTIuNywyLjItNC43LDMuOXMtMy42LDIuOS00LjYsMy42Yy0xLjEsMC44LTIuMSwxLjUtMywyLjENCgkJcy0yLjcsMS43LTUuMywzLjJjLTIuNiwxLjUtNC4zLDIuNi01LjEsMy4xcy0xLjcsMS4xLTIuNywxLjdzLTIuMSwxLjItMy4yLDJjLTEuMSwwLjctMS45LDEuMy0yLjQsMS42Yy0wLjUsMC4zLTEuNiwwLjktMy4zLDEuNw0KCQljLTEuNywwLjgtMy43LDEuOC02LjEsMi44Yy0yLjQsMS4xLTQsMS44LTQuOSwyLjFsLTEuNCwwLjVsLTEuNS0wLjRjLTEtMC4yLTIuMS0wLjctMy4zLTEuMmMtMS4yLTAuNi0zLjItMS42LTUuOS0zDQoJCWMtMi44LTEuNC00LjQtMi4zLTQuOC0yLjZjLTAuNC0wLjMtMS41LTEuMS0zLjItMi4yYy0xLjctMS4xLTMtMS45LTMuOS0yLjVjLTAuOS0wLjUtMi42LTEuNi01LjItMy4xYy0yLjYtMS41LTQuMy0yLjYtNS4zLTMuMg0KCQljLTEtMC42LTItMS4zLTMtMi4xYy0xLjEtMC44LTIuMy0xLjctMy42LTIuOHMtMy4xLTIuNS01LjQtNC40Yy0yLjItMS45LTQuMi0zLjUtNS44LTQuOGMtMS42LTEuNC00LTMuNC03LjItNi4yDQoJCWMtMy4yLTIuOC01LjItNC41LTYuMS01LjJjLTAuOS0wLjctMi44LTIuNC01LjgtNC45cy00LjctNC4xLTUtNC40cy0xLjgtMS44LTQuNS00LjRjLTIuNy0yLjYtNC41LTQuNS01LjYtNS42DQoJCWMtMS4xLTEuMS0zLjQtMy42LTctNy40cy01LjctNi4yLTYuNC03LjFzLTEuOC0yLjEtMy4yLTMuOGMtMS40LTEuNy0yLjUtMy4xLTMuMy00LjFzLTEuNi0yLjEtMi40LTMuMmMtMC44LTEuMS0xLjYtMi4zLTIuNS0zLjUNCgkJYy0wLjktMS4yLTEuNy0yLjMtMi40LTMuMmMtMC43LTAuOS0xLjMtMS44LTEuOC0yLjVjLTAuNS0wLjctMS4yLTEuNy0yLjEtMi45Yy0wLjktMS4yLTEuNi0yLjMtMi4zLTMuMmMtMC42LTAuOS0xLjUtMi4yLTIuNS0zLjcNCgkJcy0xLjktMi43LTIuNC0zLjZjLTAuNS0wLjktMS40LTIuMi0yLjctNC4xYy0xLjItMS45LTItMy4xLTIuNC0zLjhjLTAuNC0wLjctMS40LTIuNS0zLjItNS4zYy0xLjctMi44LTIuOS00LjctMy40LTUuNg0KCQljLTAuNS0wLjktMS45LTMuMi00LjEtNy4xYy0yLjItMy45LTMuOC02LjgtNC45LTguOGMtMS4xLTItMi4xLTMuOC0yLjktNS42Yy0wLjgtMS43LTItNC4zLTMuNS03LjhjLTEuNS0zLjUtMi4zLTUuMi0yLjMtNS4zDQoJCWMwLTAuMS0wLjgtMi0yLjMtNS43Yy0xLjUtMy43LTIuNy02LjYtMy41LTguOGMtMC44LTIuMS0yLTUuNC0zLjUtOS44Yy0xLjUtNC40LTMtOS00LjYtMTRjLTEuNS00LjktMy4yLTExLTUuMS0xOC4xDQoJCWMtMS45LTcuMS0zLjItMTIuNC0zLjktMTUuOGMtMC44LTMuNS0xLjgtOC41LTMuMi0xNS4xcy0yLjEtMTAuOS0yLjQtMTNjLTAuMy0yLjEtMC44LTguNC0xLjYtMTguOWMtMC44LTEwLjYtMS40LTE4LjUtMS42LTIzLjkNCgkJYy0wLjMtNS40LTAuNS0xMS4xLTAuOS0xNy4xYy0wLjMtNi0wLjgtMTUuOS0xLjQtMjkuNmMtMC42LTEzLjctMC45LTIzLjQtMC45LTI5LjF2LTguNWwwLjgtMC40YzAuNS0wLjMsMi0wLjYsNC41LTEuMQ0KCQljMi40LTAuNCw1LjgtMC45LDEwLjEtMS42YzQuMy0wLjcsMTAuOC0xLjcsMTkuNi0zLjJjOC44LTEuNSwxNS0yLjYsMTguOC0zLjJzOC44LTEuNywxNS0zLjFjNi4yLTEuNCwxMS42LTIuNywxNi4yLTQNCgkJYzQuNi0xLjIsOC41LTIuNCwxMS44LTMuNWMzLjMtMS4xLDUuOS0xLjksNy43LTIuNWMxLjktMC42LDMuNi0xLjIsNS4zLTEuN2MxLjctMC42LDUuMS0xLjgsMTAuMS0zLjdjNS4xLTEuOSw5LjgtMy44LDE0LjItNS44DQoJCWM0LjQtMS45LDYuOC0yLjksNy4xLTNjMC4zLDAsMi40LTEsNi40LTIuOGM0LTEuOCw3LjUtMy41LDEwLjctNC45YzMuMi0xLjUsNi40LTMsOS41LTQuNWMzLjEtMS41LDctMy40LDExLjYtNS44bDctMy42SDI5MS4xeiIvPg0KCTxwYXRoIGlkPSJzdmdfMSIgY2xhc3M9InN0MSIgZD0iTTI5MS4xLDE4Ni44aDEuNmw2LjgsMy40YzQuNSwyLjMsNy41LDMuOCw5LDQuNWMxLjUsMC43LDYuNCwzLDE0LjgsNi45YzguNCwzLjksMTIuOCw1LjgsMTMuMSw1LjkNCgkJYzAuMywwLDIuNSwxLDYuNywyLjhjNC4yLDEuOCw3LjQsMy4xLDkuNyw0YzIuMywwLjksNS41LDIsOS44LDMuNWM0LjMsMS41LDcsMi40LDguMSwyLjhjMS4xLDAuNCwyLjUsMC45LDQuMiwxLjQNCgkJYzEuNywwLjUsMy45LDEuMiw2LjUsMmMyLjcsMC44LDYsMS43LDEwLjIsMi44YzQuMSwxLjEsOC4xLDIuMSwxMiwyLjljMy45LDAuOSw2LjQsMS40LDcuNSwxLjZjMS4xLDAuMiw0LjcsMC45LDEwLjgsMg0KCQljNiwxLjEsOS44LDEuOCwxMS40LDJjMS41LDAuMiw1LjMsMC44LDExLjIsMS44YzYsMC45LDksMS40LDkuMSwxLjRjMC4xLDAsMywwLjUsOC43LDEuNmM1LjcsMS4xLDguNiwxLjcsOC42LDEuN2wwLjEsMC4xDQoJCWwtMC40LDE3LjVjLTAuMiwxMS43LTAuOCwyNi43LTEuOCw0NS4yYy0xLDE4LjUtMS43LDMxLjQtMi4zLDM4LjdjLTAuNiw3LjQtMS4yLDEzLjctMS45LDE4LjljLTAuNyw1LjItMS42LDEwLjItMi41LDE0LjkNCgkJYy0xLDQuNy0yLDkuNC0zLjEsMTQuMnMtMi4xLDktMywxMi42Yy0wLjksMy42LTEuOSw3LjUtMy4xLDExLjZjLTEuMiw0LjEtMS45LDYuNy0yLjIsNy41Yy0wLjIsMC45LTAuNiwyLTEuMSwzLjMNCgkJYy0wLjUsMS4zLTEsMy0xLjcsNC45Yy0wLjYsMi0xLjcsNC44LTMsOC41Yy0xLjQsMy43LTIuNSw2LjYtMy40LDguOGMtMC45LDIuMi0yLDQuOC0zLjMsNy45Yy0xLjMsMy4xLTIuOSw2LjctNSwxMQ0KCQljLTIsNC4zLTMuMiw2LjgtMy42LDcuNWMtMC40LDAuNy0xLjksMy4zLTQuNCw3LjdjLTIuNSw0LjQtNC4xLDctNC42LDcuOWMtMC41LDAuOC0xLjgsMi45LTMuNyw2LjJjLTIsMy4zLTMuMyw1LjQtMy45LDYuMw0KCQljLTAuNiwwLjktMS41LDIuMy0yLjgsNC4yYy0xLjIsMS45LTIuMywzLjQtMyw0LjVjLTAuOCwxLjEtMS43LDIuMy0yLjcsMy44Yy0xLDEuNC0yLDIuOC0yLjksNGMtMC45LDEuMi0xLjcsMi4yLTIuMiwzLjENCgkJYy0wLjUsMC44LTEuMywxLjktMi4zLDMuMmMtMSwxLjMtMiwyLjYtMi45LDMuOWMtMC45LDEuMy0xLjksMi42LTMsNHMtMiwyLjUtMi44LDMuNXMtMi40LDIuOC00LjksNS42Yy0yLjUsMi43LTUuNCw1LjktOC44LDkuNA0KCQljLTMuNCwzLjUtNi40LDYuNS05LDljLTIuNiwyLjUtNC4zLDQuMS01LjEsNC44Yy0wLjgsMC43LTIuOCwyLjMtNS45LDVjLTMuMSwyLjctNSw0LjMtNS43LDQuOXMtMi40LDItNS4xLDQuMw0KCQljLTIuNywyLjMtNC41LDMuOC01LjMsNC41Yy0wLjgsMC43LTEuOCwxLjQtMi45LDIuM2MtMS4xLDAuOS0yLjYsMi4xLTQuNSwzLjdjLTEuOSwxLjYtMy40LDIuNy00LjQsMy41Yy0xLDAuNy0yLDEuNC0yLjksMg0KCQljLTAuOSwwLjYtMi42LDEuNi01LjEsMy4xcy00LjEsMi40LTQuOCwyLjljLTAuOCwwLjUtMS42LDEtMi42LDEuNmMtMSwwLjUtMiwxLjItMywxLjljLTEsMC43LTEuOCwxLjItMi4zLDEuNQ0KCQljLTAuNSwwLjMtMS41LDAuOS0zLjEsMS42Yy0xLjYsMC44LTMuNSwxLjctNS44LDIuN2MtMi4zLDEtMy44LDEuNy00LjcsMmwtMS4zLDAuNWwtMS41LTAuNGMtMS0wLjItMi0wLjYtMy4xLTEuMg0KCQljLTEuMS0wLjUtMy0xLjUtNS43LTIuOGMtMi43LTEuMy00LjItMi4yLTQuNi0yLjVjLTAuNC0wLjMtMS40LTEtMy4xLTIuMWMtMS43LTEuMS0yLjktMS44LTMuOC0yLjNjLTAuOC0wLjUtMi41LTEuNS01LTIuOQ0KCQljLTIuNS0xLjQtNC4yLTIuNS01LjEtMy4xYy0wLjktMC42LTEuOS0xLjMtMi45LTJjLTEtMC43LTIuMi0xLjYtMy40LTIuNmMtMS4zLTEtMy0yLjQtNS4xLTQuMmMtMi4xLTEuOC00LTMuMy01LjYtNC42DQoJCWMtMS42LTEuMy0zLjktMy4yLTYuOS01LjljLTMtMi42LTUtNC4zLTUuOC00LjlzLTIuNy0yLjItNS42LTQuN2MtMi45LTIuNS00LjUtMy45LTQuOC00LjJjLTAuMy0wLjMtMS44LTEuNy00LjMtNC4yDQoJCWMtMi41LTIuNS00LjMtNC4yLTUuMy01LjNjLTEtMS4xLTMuMi0zLjQtNi43LTdjLTMuNC0zLjYtNS41LTUuOS02LjItNi43Yy0wLjctMC44LTEuNy0yLTMtMy42Yy0xLjMtMS42LTIuNC0yLjktMy4xLTMuOQ0KCQlzLTEuNS0yLTIuMy0zLjFjLTAuOC0xLjEtMS42LTIuMi0yLjQtMy4zYy0wLjgtMS4xLTEuNi0yLjItMi4zLTMuMWMtMC43LTAuOS0xLjMtMS43LTEuOC0yLjRjLTAuNS0wLjctMS4xLTEuNi0yLTIuOA0KCQljLTAuOC0xLjEtMS42LTIuMi0yLjItMy4xYy0wLjYtMC45LTEuNC0yLjEtMi40LTMuNWMtMS0xLjQtMS44LTIuNi0yLjMtMy40cy0xLjQtMi4xLTIuNS0zLjljLTEuMi0xLjgtMS45LTMtMi4zLTMuNg0KCQljLTAuNC0wLjctMS40LTIuMy0zLTVjLTEuNy0yLjctMi43LTQuNS0zLjMtNS4zYy0wLjUtMC44LTEuOC0zLjEtMy45LTYuN2MtMi4xLTMuNy0zLjctNi41LTQuNy04LjNjLTEtMS45LTItMy42LTIuOC01LjMNCgkJYy0wLjgtMS42LTEuOS00LjEtMy40LTcuNGMtMS41LTMuMy0yLjItNS0yLjItNWMwLTAuMS0wLjctMS45LTIuMi01LjRjLTEuNS0zLjUtMi42LTYuMy0zLjQtOC4zYy0wLjgtMi0xLjktNS4xLTMuNC05LjMNCgkJYy0xLjUtNC4xLTIuOS04LjYtNC40LTEzLjNjLTEuNS00LjctMy4xLTEwLjQtNC44LTE3LjFjLTEuOC02LjctMy0xMS43LTMuNy0xNWMtMC43LTMuMy0xLjctOC4xLTMtMTQuMw0KCQljLTEuMy02LjMtMi4xLTEwLjQtMi4zLTEyLjNjLTAuMi0yLTAuOC03LjktMS42LTE4Yy0wLjgtMTAtMS4zLTE3LjYtMS42LTIyLjdjLTAuMi01LjEtMC41LTEwLjUtMC44LTE2LjINCgkJYy0wLjMtNS43LTAuOC0xNS4xLTEuMy0yOC4xYy0wLjYtMTMtMC44LTIyLjItMC44LTI3LjZ2LTguMWwwLjgtMC40YzAuNS0wLjMsMi0wLjYsNC4zLTFjMi4zLTAuNCw1LjYtMC45LDkuNy0xLjUNCgkJYzQuMS0wLjYsMTAuNC0xLjYsMTguNy0zLjFjOC40LTEuNCwxNC40LTIuNCwxOC0zLjFjMy42LTAuNiw4LjQtMS42LDE0LjQtMi45YzYtMS4zLDExLjEtMi42LDE1LjUtMy44YzQuNC0xLjIsOC4xLTIuMywxMS4yLTMuMw0KCQljMy4xLTEsNS42LTEuOCw3LjQtMi4zYzEuOC0wLjUsMy41LTEuMSw1LjEtMS42YzEuNi0wLjUsNC44LTEuNyw5LjctMy41YzQuOC0xLjgsOS40LTMuNiwxMy42LTUuNWM0LjItMS44LDYuNS0yLjgsNi44LTIuOA0KCQljMC4zLDAsMi4zLTAuOSw2LjEtMi42YzMuOC0xLjcsNy4yLTMuMywxMC4zLTQuN2MzLjEtMS40LDYuMS0yLjgsOS4xLTQuMmMzLTEuNCw2LjctMy4yLDExLjEtNS41bDYuNy0zLjRIMjkxLjF6Ii8+DQo8L2c+DQo8dGV4dCB0cmFuc2Zvcm09Im1hdHJpeCgxIDAgMCAxIDE5NS45OTk5IDQ4OS44OTI5KSIgY2xhc3M9InN0MiBzdDMgc3Q0Ij5TPC90ZXh0Pg0KPGc+DQoJPHBhdGggY2xhc3M9InN0MiIgZD0iTTU3OS41LDQzMi44YzguOCw1LjQsMjEuNiw5LjksMzUuMSw5LjljMjAsMCwzMS43LTEwLjYsMzEuNy0yNS45YzAtMTQuMi04LjEtMjIuMy0yOC42LTMwLjENCgkJYy0yNC43LTguOC00MC0yMS42LTQwLTQzYzAtMjMuNiwxOS42LTQxLjIsNDktNDEuMmMxNS41LDAsMjYuOCwzLjYsMzMuNSw3LjRsLTUuNCwxNmMtNC45LTIuNy0xNS4xLTcuMi0yOC44LTcuMg0KCQljLTIwLjcsMC0yOC42LDEyLjQtMjguNiwyMi43YzAsMTQuMiw5LjIsMjEuMSwzMC4xLDI5LjJjMjUuNiw5LjksMzguNywyMi4zLDM4LjcsNDQuNWMwLDIzLjQtMTcuMyw0My42LTUzLjEsNDMuNg0KCQljLTE0LjYsMC0zMC42LTQuMy0zOC43LTkuN0w1NzkuNSw0MzIuOHoiLz4NCgk8cGF0aCBjbGFzcz0ic3QyIiBkPSJNNzcxLjUsMzg1LjZoLTU4Ljl2NTQuNmg2NS43djE2LjRINjkzVjMwNS4xaDgxLjl2MTYuNGgtNjIuM3Y0Ny45aDU4LjlWMzg1LjZ6Ii8+DQoJPHBhdGggY2xhc3M9InN0MiIgZD0iTTkzMy4yLDM3OS4zYzAsNTIuMi0zMS43LDc5LjgtNzAuNCw3OS44Yy00MCwwLTY4LjEtMzEtNjguMS03Ni45YzAtNDguMSwyOS45LTc5LjYsNzAuNC03OS42DQoJCUM5MDYuNSwzMDIuNiw5MzMuMiwzMzQuMyw5MzMuMiwzNzkuM3ogTTgxNS42LDM4MS43YzAsMzIuNCwxNy41LDYxLjQsNDguMyw2MS40YzMxLDAsNDguNi0yOC42LDQ4LjYtNjMNCgkJYzAtMzAuMS0xNS43LTYxLjYtNDguMy02MS42QzgzMS44LDMxOC42LDgxNS42LDM0OC41LDgxNS42LDM4MS43eiIvPg0KCTxwYXRoIGQ9Ik0xMDc0LDQ0OS45Yy04LjgsMy4xLTI2LjEsOC4zLTQ2LjUsOC4zYy0yMi45LDAtNDEuOC01LjgtNTYuNy0yMGMtMTMtMTIuNi0yMS4xLTMyLjgtMjEuMS01Ni40DQoJCWMwLjItNDUuMiwzMS4zLTc4LjMsODIuMS03OC4zYzE3LjUsMCwzMS4zLDMuOCwzNy44LDdsLTQuNywxNmMtOC4xLTMuNi0xOC4yLTYuNS0zMy41LTYuNWMtMzYuOSwwLTYwLjksMjIuOS02MC45LDYwLjkNCgkJYzAsMzguNSwyMy4yLDYxLjIsNTguNSw2MS4yYzEyLjgsMCwyMS42LTEuOCwyNi4xLTR2LTQ1LjJoLTMwLjhWMzc3aDQ5LjlWNDQ5Ljl6Ii8+DQoJPHBhdGggZD0iTTExOTQuMyw0MjYuOWMwLDExLjIsMC4yLDIxLjEsMC45LDI5LjdoLTE3LjVsLTEuMS0xNy44aC0wLjRjLTUuMiw4LjgtMTYuNiwyMC4yLTM2LDIwLjJjLTE3LjEsMC0zNy42LTkuNC0zNy42LTQ3LjcNCgkJdi02My42aDE5Ljh2NjAuM2MwLDIwLjcsNi4zLDM0LjYsMjQuMywzNC42YzEzLjMsMCwyMi41LTkuMiwyNi4xLTE4YzEuMS0yLjksMS44LTYuNSwxLjgtMTAuMXYtNjYuOGgxOS44VjQyNi45eiIvPg0KCTxwYXRoIGQ9Ik0xMjg3LjQsNDU2LjZsLTEuNi0xMy43aC0wLjdjLTYuMSw4LjUtMTcuOCwxNi4yLTMzLjMsMTYuMmMtMjIsMC0zMy4zLTE1LjUtMzMuMy0zMS4zYzAtMjYuMywyMy40LTQwLjcsNjUuNC00MC41di0yLjINCgkJYzAtOS0yLjUtMjUuMi0yNC43LTI1LjJjLTEwLjEsMC0yMC43LDMuMS0yOC4zLDguMWwtNC41LTEzYzktNS44LDIyLTkuNywzNS44LTkuN2MzMy4zLDAsNDEuNCwyMi43LDQxLjQsNDQuNXY0MC43DQoJCWMwLDkuNCwwLjQsMTguNywxLjgsMjYuMUgxMjg3LjR6IE0xMjg0LjUsNDAxLjFjLTIxLjYtMC40LTQ2LjEsMy40LTQ2LjEsMjQuNWMwLDEyLjgsOC41LDE4LjksMTguNywxOC45YzE0LjIsMCwyMy4yLTksMjYuMy0xOC4yDQoJCWMwLjctMiwxLjEtNC4zLDEuMS02LjNWNDAxLjF6Ii8+DQoJPHBhdGggZD0iTTEzMzUuNSwzODEuN2MwLTEyLjgtMC4yLTIzLjgtMC45LTM0aDE3LjNsMC43LDIxLjRoMC45YzQuOS0xNC42LDE2LjktMjMuOCwzMC4xLTIzLjhjMi4yLDAsMy44LDAuMiw1LjYsMC43djE4LjcNCgkJYy0yLTAuNC00LTAuNy02LjctMC43Yy0xMy45LDAtMjMuOCwxMC42LTI2LjUsMjUuNGMtMC40LDIuNy0wLjksNS44LTAuOSw5LjJ2NThoLTE5LjZWMzgxLjd6Ii8+DQoJPHBhdGggZD0iTTE1MDAuOCwyOTd2MTMxLjZjMCw5LjcsMC4yLDIwLjcsMC45LDI4LjFIMTQ4NGwtMC45LTE4LjloLTAuNGMtNi4xLDEyLjEtMTkuMywyMS40LTM3LjEsMjEuNA0KCQljLTI2LjMsMC00Ni41LTIyLjMtNDYuNS01NS4zYy0wLjItMzYuMiwyMi4zLTU4LjUsNDguOC01OC41YzE2LjYsMCwyNy45LDcuOSwzMi44LDE2LjZoMC40di02NUgxNTAwLjh6IE0xNDgxLDM5Mi4xDQoJCWMwLTIuNS0wLjItNS44LTAuOS04LjNjLTIuOS0xMi42LTEzLjctMjIuOS0yOC42LTIyLjljLTIwLjUsMC0zMi42LDE4LTMyLjYsNDIuMWMwLDIyLDEwLjgsNDAuMywzMi4yLDQwLjMNCgkJYzEzLjMsMCwyNS40LTguOCwyOS0yMy42YzAuNy0yLjcsMC45LTUuNCwwLjktOC41VjM5Mi4xeiIvPg0KCTxwYXRoIGQ9Ik0xNTU1LjcsMzE3LjJjMC4yLDYuNy00LjcsMTIuMS0xMi42LDEyLjFjLTcsMC0xMS45LTUuNC0xMS45LTEyLjFjMC03LDUuMi0xMi40LDEyLjQtMTIuNA0KCQlDMTU1MSwzMDQuOCwxNTU1LjcsMzEwLjIsMTU1NS43LDMxNy4yeiBNMTUzMy43LDQ1Ni42VjM0Ny44aDE5Ljh2MTA4LjhIMTUzMy43eiIvPg0KCTxwYXRoIGQ9Ik0xNTg2LjMsMzc3LjJjMC0xMS4yLTAuMi0yMC41LTAuOS0yOS41aDE3LjVsMS4xLDE4aDAuNGM1LjQtMTAuMywxOC0yMC41LDM2LTIwLjVjMTUuMSwwLDM4LjUsOSwzOC41LDQ2LjN2NjVoLTE5Ljh2LTYyLjcNCgkJYzAtMTcuNS02LjUtMzIuMi0yNS4yLTMyLjJjLTEzLDAtMjMuMiw5LjItMjYuNSwyMC4yYy0wLjksMi41LTEuMyw1LjgtMS4zLDkuMnY2NS40aC0xOS44VjM3Ny4yeiIvPg0KCTxwYXRoIGQ9Ik0xODA0LjksMzQ3LjhjLTAuNCw3LjktMC45LDE2LjYtMC45LDI5Ljl2NjMuMmMwLDI1LTQuOSw0MC4zLTE1LjUsNDkuN2MtMTAuNiw5LjktMjUuOSwxMy0zOS42LDEzYy0xMywwLTI3LjQtMy4xLTM2LjItOQ0KCQlsNC45LTE1LjFjNy4yLDQuNSwxOC40LDguNSwzMS45LDguNWMyMC4yLDAsMzUuMS0xMC42LDM1LjEtMzhWNDM4aC0wLjRjLTYuMSwxMC4xLTE3LjgsMTguMi0zNC42LDE4LjJjLTI3LDAtNDYuMy0yMi45LTQ2LjMtNTMuMQ0KCQljMC0zNi45LDI0LjEtNTcuOCw0OS01Ny44YzE4LjksMCwyOS4yLDkuOSwzNCwxOC45aDAuNGwwLjktMTYuNEgxODA0Ljl6IE0xNzg0LjQsMzkwLjdjMC0zLjQtMC4yLTYuMy0xLjEtOQ0KCQljLTMuNi0xMS41LTEzLjMtMjAuOS0yNy43LTIwLjljLTE4LjksMC0zMi40LDE2LTMyLjQsNDEuMmMwLDIxLjQsMTAuOCwzOS4xLDMyLjIsMzkuMWMxMi4xLDAsMjMuMi03LjYsMjcuNC0yMC4yDQoJCWMxLjEtMy40LDEuNi03LjIsMS42LTEwLjZWMzkwLjd6Ii8+DQoJPHBhdGggZD0iTTE4MjYuMiw0NTAuOGMwLTQuMiwyLjgtNy4xLDYuNy03LjFzNi42LDIuOSw2LjYsNy4xYzAsNC0yLjYsNy4xLTYuNyw3LjFDMTgyOC45LDQ1Ny45LDE4MjYuMiw0NTQuOCwxODI2LjIsNDUwLjh6Ii8+DQoJPHBhdGggZD0iTTE4OTAuNyw0NTQuNmMtMi42LDEuNC04LjMsMy4xLTE1LjYsMy4xYy0xNi40LDAtMjcuMS0xMS4xLTI3LjEtMjcuOGMwLTE2LjgsMTEuNS0yOC45LDI5LjItMjguOWM1LjgsMCwxMSwxLjUsMTMuNywyLjgNCgkJbC0yLjIsNy42Yy0yLjQtMS40LTYuMS0yLjYtMTEuNS0yLjZjLTEyLjUsMC0xOS4yLDkuMi0xOS4yLDIwLjZjMCwxMi42LDguMSwyMC40LDE4LjksMjAuNGM1LjYsMCw5LjMtMS41LDEyLjEtMi43TDE4OTAuNyw0NTQuNnoiDQoJCS8+DQoJPHBhdGggZD0iTTE5NTAuOCw0MjljMCwyMC4xLTEzLjksMjguOS0yNy4xLDI4LjljLTE0LjcsMC0yNi4xLTEwLjgtMjYuMS0yOGMwLTE4LjIsMTEuOS0yOC45LDI3LTI4LjkNCgkJQzE5NDAuMiw0MDEsMTk1MC44LDQxMi4zLDE5NTAuOCw0Mjl6IE0xOTA3LjYsNDI5LjVjMCwxMS45LDYuOSwyMC45LDE2LjUsMjAuOWM5LjQsMCwxNi41LTguOSwxNi41LTIxLjFjMC05LjItNC42LTIwLjktMTYuMy0yMC45DQoJCUMxOTEyLjcsNDA4LjQsMTkwNy42LDQxOS4yLDE5MDcuNiw0MjkuNXoiLz4NCgk8cGF0aCBkPSJNMTk2My4zLDQxNi45YzAtNS42LTAuMS0xMC4yLTAuNC0xNC43aDguN2wwLjQsOC44aDAuM2MzLTUuMiw4LjEtMTAsMTcuMS0xMGM3LjQsMCwxMyw0LjUsMTUuNCwxMC45aDAuMg0KCQljMS43LTMsMy44LTUuNCw2LjEtNy4xYzMuMy0yLjUsNi45LTMuOCwxMi0zLjhjNy4yLDAsMTcuOSw0LjcsMTcuOSwyMy42djMyaC05Ljd2LTMwLjhjMC0xMC41LTMuOC0xNi44LTExLjgtMTYuOA0KCQljLTUuNiwwLTEwLDQuMi0xMS43LDljLTAuNCwxLjMtMC44LDMuMS0wLjgsNC45djMzLjZoLTkuN1Y0MjRjMC04LjctMy44LTE1LTExLjQtMTVjLTYuMiwwLTEwLjcsNC45LTEyLjMsOS45DQoJCWMtMC42LDEuNS0wLjgsMy4xLTAuOCw0Ljh2MzIuOGgtOS43VjQxNi45eiIvPg0KPC9nPg0KPC9zdmc+DQo="/></p>
				<p>&nbsp;</p>
				<h3 style="color: #de0027; text-align: center;">Access is not allowed from your IP or your country.</h3>
				<p>If you think it's a mistake, please contact with the websmater of the website.</p>
				<p>If you are the owner of the website, please contact with <a target="_blank" href="https://seoguarding.com/contact-us/">SEOGuarding.com support</a></p>
				<h4>Session details:</h4>
				<p>IP: <?php echo $myIP; ?></p>
				<?php
				if ($country_name != '') echo '<p>Country: '.$country_name.'</p>';
				?>
				<p>&nbsp;</p>
				<p>&nbsp;</p>

				<p style="font-size: 70%;">Powered by <a target="_blank" href="https://seoguarding.com/">SEOGuarding.com</a></p>
			</div>
			</body></html>
			<?php
			
			die();
		}

	}


?>
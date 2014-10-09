<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Framework_Form {
	/**
	 * @param string $action
	 * @param string $method
	 * @param array  $htmlOptions
	 *
	 * @return string
	 */
	public static function createForm( $action = '', $method = '', $htmlOptions = array() ) {

		$form = sprintf( '<form action="%s" method="%s"', $action, $method );
		foreach ( $htmlOptions as $key => $option ) {
			$form = $form . sprintf( ' %s="%s"', $key, $option );
		}
		$form .= '>';

		return $form;
	}

	/**
	 * @return string
	 */
	public static function endForm() {
		return '</form>';
	}

	/**
	 * @param       $name
	 * @param       $value
	 * @param array $htmlOptions
	 *
	 * @return string
	 */
	public static function textField( $name, $value, $htmlOptions = array() ) {
		$field = sprintf( '<input type="text" name="%s" value="%s"', $name, $value );
		foreach ( $htmlOptions as $key => $option ) {
			$field = $field . sprintf( ' %s="%s"', $key, $option );
		}
		$field .= '/>';

		return $field;
	}

	/**
	 * @param       $name
	 * @param       $value
	 * @param array $htmlOptions
	 *
	 * @return string
	 */
	public static function passWordField( $name, $value, $htmlOptions = array() ) {
		$field = sprintf( '<input type="password" name="%s" value="%s"', $name, $value );
		foreach ( $htmlOptions as $key => $option ) {
			$field = $field . sprintf( ' %s="%s"', $key, $option );
		}
		$field .= '/>';

		return $field;
	}

	/**
	 * @param       $name
	 * @param       $value
	 * @param array $htmlOptions
	 *
	 * @return string
	 */
	public static function hiddenField( $name, $value, $htmlOptions = array() ) {
		$field = sprintf( '<input type="hidden" name="%s" value="%s"', $name, $value );
		foreach ( $htmlOptions as $key => $option ) {
			$field = $field . sprintf( ' %s="%s"', $key, $option );
		}
		$field .= '/>';

		return $field;
	}

	/**
	 * @param       $name
	 * @param array $selected
	 * @param array $data
	 * @param array $htmlOptions
	 *
	 * @return string
	 */
	public static function dropDownList( $name, $selected = array(), $data = array(), $htmlOptions = array() ) {
		if ( $selected == null ) {
			$selected = array();
		}
		//filter the selected
		$selected = array_filter( $selected, 'strlen' );
		$select   = '<select name="' . $name . '"';
		foreach ( $htmlOptions as $key => $option ) {
			$select .= sprintf( ' %s="%s"', $key, $option );
		}
		$select .= '>';
		//option go here
		$options = '';
		if ( isset( $htmlOptions['prompt'] ) ) {
			$options .= '<option value="">' . $htmlOptions['prompt'] . '</option>';
		}
		foreach ( (array) $data as $key => $value ) {
			$optionSelect = in_array( $key, $selected ) ? 'selected="selected"' : null;
			$options .= sprintf( '<option %s value="%s">%s</option>', $optionSelect, $key, $value );
		}

		return $select . $options . '</select>';

	}

	/**
	 * @param       $name
	 * @param       $value
	 * @param array $htmlOptions
	 *
	 * @return string
	 */
	public static function textArea( $name, $value, $htmlOptions = array() ) {
		$field = sprintf( '<textarea name="%s"', $name );
		foreach ( $htmlOptions as $key => $option ) {
			$field = $field . sprintf( ' %s="%s"', $key, $option );
		}
		$field .= '>' . $value . '</textarea>';

		return $field;
	}

	/**
	 * @param       $name
	 * @param       $isCheck
	 * @param array $htmlOptions
	 *
	 * @return string
	 */
	public static function checkBox( $name, $isCheck, $htmlOptions = array() ) {
		$field = sprintf( '<input type="checkbox" name="%s"', $name );
		foreach ( $htmlOptions as $key => $option ) {
			$field = $field . sprintf( ' %s="%s"', $key, $option );
		}
		if ( $isCheck == true ) {
			$field .= 'checked="checked"';
		}
		$field .= '/>';

		return $field;
	}

	public static function radioButton( $name, $isCheck, $htmlOptions = array() ) {
		$field = sprintf( '<input type="radio" name="%s"', $name );
		foreach ( $htmlOptions as $key => $option ) {
			$field = $field . sprintf( ' %s="%s"', $key, $option );
		}
		if ( $isCheck == true ) {
			$field .= 'checked="checked"';
		}
		$field .= '/>';

		return $field;
	}

	/**
	 * @param       $name
	 * @param array $selected
	 * @param array $htmlOptions
	 */
	public static function countryDropdown( $name, $selected = array(), $htmlOptions = array() ) {
		$countries             = array(
			"AF" => "Afghanistan",
			"AL" => "Albania",
			"DZ" => "Algeria",
			"AS" => "American Samoa",
			"AD" => "Andorra",
			"AO" => "Angola",
			"AI" => "Anguilla",
			"AQ" => "Antarctica",
			"AG" => "Antigua and Barbuda",
			"AR" => "Argentina",
			"AM" => "Armenia",
			"AW" => "Aruba",
			"AU" => "Australia",
			"AT" => "Austria",
			"AZ" => "Azerbaijan",
			"BS" => "Bahamas",
			"BH" => "Bahrain",
			"BD" => "Bangladesh",
			"BB" => "Barbados",
			"BY" => "Belarus",
			"BE" => "Belgium",
			"BZ" => "Belize",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BT" => "Bhutan",
			"BO" => "Bolivia",
			"BA" => "Bosnia and Herzegovina",
			"BW" => "Botswana",
			"BV" => "Bouvet Island",
			"BR" => "Brazil",
			"BQ" => "British Antarctic Territory",
			"IO" => "British Indian Ocean Territory",
			"VG" => "British Virgin Islands",
			"BN" => "Brunei",
			"BG" => "Bulgaria",
			"BF" => "Burkina Faso",
			"BI" => "Burundi",
			"KH" => "Cambodia",
			"CM" => "Cameroon",
			"CA" => "Canada",
			"CT" => "Canton and Enderbury Islands",
			"CV" => "Cape Verde",
			"KY" => "Cayman Islands",
			"CF" => "Central African Republic",
			"TD" => "Chad",
			"CL" => "Chile",
			"CN" => "China",
			"CX" => "Christmas Island",
			"CC" => "Cocos [Keeling] Islands",
			"CO" => "Colombia",
			"KM" => "Comoros",
			"CG" => "Congo - Brazzaville",
			"CD" => "Congo - Kinshasa",
			"CK" => "Cook Islands",
			"CR" => "Costa Rica",
			"HR" => "Croatia",
			"CU" => "Cuba",
			"CY" => "Cyprus",
			"CZ" => "Czech Republic",
			"CI" => "Côte d’Ivoire",
			"DK" => "Denmark",
			"DJ" => "Djibouti",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"NQ" => "Dronning Maud Land",
			"DD" => "East Germany",
			"EC" => "Ecuador",
			"EG" => "Egypt",
			"SV" => "El Salvador",
			"GQ" => "Equatorial Guinea",
			"ER" => "Eritrea",
			"EE" => "Estonia",
			"ET" => "Ethiopia",
			"FK" => "Falkland Islands",
			"FO" => "Faroe Islands",
			"FJ" => "Fiji",
			"FI" => "Finland",
			"FR" => "France",
			"GF" => "French Guiana",
			"PF" => "French Polynesia",
			"TF" => "French Southern Territories",
			"FQ" => "French Southern and Antarctic Territories",
			"GA" => "Gabon",
			"GM" => "Gambia",
			"GE" => "Georgia",
			"DE" => "Germany",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GR" => "Greece",
			"GL" => "Greenland",
			"GD" => "Grenada",
			"GP" => "Guadeloupe",
			"GU" => "Guam",
			"GT" => "Guatemala",
			"GG" => "Guernsey",
			"GN" => "Guinea",
			"GW" => "Guinea-Bissau",
			"GY" => "Guyana",
			"HT" => "Haiti",
			"HM" => "Heard Island and McDonald Islands",
			"HN" => "Honduras",
			"HK" => "Hong Kong SAR China",
			"HU" => "Hungary",
			"IS" => "Iceland",
			"IN" => "India",
			"ID" => "Indonesia",
			"IR" => "Iran",
			"IQ" => "Iraq",
			"IE" => "Ireland",
			"IM" => "Isle of Man",
			"IL" => "Israel",
			"IT" => "Italy",
			"JM" => "Jamaica",
			"JP" => "Japan",
			"JE" => "Jersey",
			"JT" => "Johnston Island",
			"JO" => "Jordan",
			"KZ" => "Kazakhstan",
			"KE" => "Kenya",
			"KI" => "Kiribati",
			"KW" => "Kuwait",
			"KG" => "Kyrgyzstan",
			"LA" => "Laos",
			"LV" => "Latvia",
			"LB" => "Lebanon",
			"LS" => "Lesotho",
			"LR" => "Liberia",
			"LY" => "Libya",
			"LI" => "Liechtenstein",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"MO" => "Macau SAR China",
			"MK" => "Macedonia",
			"MG" => "Madagascar",
			"MW" => "Malawi",
			"MY" => "Malaysia",
			"MV" => "Maldives",
			"ML" => "Mali",
			"MT" => "Malta",
			"MH" => "Marshall Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MU" => "Mauritius",
			"YT" => "Mayotte",
			"FX" => "Metropolitan France",
			"MX" => "Mexico",
			"FM" => "Micronesia",
			"MI" => "Midway Islands",
			"MD" => "Moldova",
			"MC" => "Monaco",
			"MN" => "Mongolia",
			"ME" => "Montenegro",
			"MS" => "Montserrat",
			"MA" => "Morocco",
			"MZ" => "Mozambique",
			"MM" => "Myanmar [Burma]",
			"NA" => "Namibia",
			"NR" => "Nauru",
			"NP" => "Nepal",
			"NL" => "Netherlands",
			"AN" => "Netherlands Antilles",
			"NT" => "Neutral Zone",
			"NC" => "New Caledonia",
			"NZ" => "New Zealand",
			"NI" => "Nicaragua",
			"NE" => "Niger",
			"NG" => "Nigeria",
			"NU" => "Niue",
			"NF" => "Norfolk Island",
			"KP" => "North Korea",
			"VD" => "North Vietnam",
			"MP" => "Northern Mariana Islands",
			"NO" => "Norway",
			"OM" => "Oman",
			"PC" => "Pacific Islands Trust Territory",
			"PK" => "Pakistan",
			"PW" => "Palau",
			"PS" => "Palestinian Territories",
			"PA" => "Panama",
			"PZ" => "Panama Canal Zone",
			"PG" => "Papua New Guinea",
			"PY" => "Paraguay",
			"YD" => "Peoples Democratic Republic of Yemen",
			"PE" => "Peru",
			"PH" => "Philippines",
			"PN" => "Pitcairn Islands",
			"PL" => "Poland",
			"PT" => "Portugal",
			"PR" => "Puerto Rico",
			"QA" => "Qatar",
			"RO" => "Romania",
			"RU" => "Russia",
			"RW" => "Rwanda",
			"RE" => "Réunion",
			"BL" => "Saint Barthélemy",
			"SH" => "Saint Helena",
			"KN" => "Saint Kitts and Nevis",
			"LC" => "Saint Lucia",
			"MF" => "Saint Martin",
			"PM" => "Saint Pierre and Miquelon",
			"VC" => "Saint Vincent and the Grenadines",
			"WS" => "Samoa",
			"SM" => "San Marino",
			"SA" => "Saudi Arabia",
			"SN" => "Senegal",
			"RS" => "Serbia",
			"CS" => "Serbia and Montenegro",
			"SC" => "Seychelles",
			"SL" => "Sierra Leone",
			"SG" => "Singapore",
			"SK" => "Slovakia",
			"SI" => "Slovenia",
			"SB" => "Solomon Islands",
			"SO" => "Somalia",
			"ZA" => "South Africa",
			"GS" => "South Georgia and the South Sandwich Islands",
			"KR" => "South Korea",
			"ES" => "Spain",
			"LK" => "Sri Lanka",
			"SD" => "Sudan",
			"SR" => "Suriname",
			"SJ" => "Svalbard and Jan Mayen",
			"SZ" => "Swaziland",
			"SE" => "Sweden",
			"CH" => "Switzerland",
			"SY" => "Syria",
			"ST" => "São Tomé and Príncipe",
			"TW" => "Taiwan",
			"TJ" => "Tajikistan",
			"TZ" => "Tanzania",
			"TH" => "Thailand",
			"TL" => "Timor-Leste",
			"TG" => "Togo",
			"TK" => "Tokelau",
			"TO" => "Tonga",
			"TT" => "Trinidad and Tobago",
			"TN" => "Tunisia",
			"TR" => "Turkey",
			"TM" => "Turkmenistan",
			"TC" => "Turks and Caicos Islands",
			"TV" => "Tuvalu",
			"UM" => "U.S. Minor Outlying Islands",
			"PU" => "U.S. Miscellaneous Pacific Islands",
			"VI" => "U.S. Virgin Islands",
			"UG" => "Uganda",
			"UA" => "Ukraine",
			"SU" => "Union of Soviet Socialist Republics",
			"AE" => "United Arab Emirates",
			"GB" => "United Kingdom",
			"US" => "United States",
			"ZZ" => "Unknown or Invalid Region",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VU" => "Vanuatu",
			"VA" => "Vatican City",
			"VE" => "Venezuela",
			"VN" => "Vietnam",
			"WK" => "Wake Island",
			"WF" => "Wallis and Futuna",
			"EH" => "Western Sahara",
			"YE" => "Yemen",
			"ZM" => "Zambia",
			"ZW" => "Zimbabwe",
			"AX" => "Åland Islands",
		);
	//	$htmlOptions['prompt'] = 'Please select a country';

		return self::dropDownList( $name, $selected, array_combine( array_values( $countries ), array_values( $countries ) ), $htmlOptions );
	}
}
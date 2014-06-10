<?php 
class RiotWebsite {
	//setup variables
	public 	$facebook,
			$twitter,
			$environment = 'local',
			$page_keywords,
			$page_description = '';
	private $colors = '0123456789abcdef';
	public function randomColor () {
		$i = 6;
		$color = '#';
		while ($i > 0) {
			$color .= $this->colors[rand(0, strlen($this->colors) - 1)];
			$i -= 1;
		}
		return $color;
	}
	public function getJobApplication ($email = false, $sent_secret = false, $readonly = false) {
		return $this->RiotAPI->getJobApplication($email, $sent_secret, $readonly);
	}
	public function getJobApplicationByID ($id) {
		return $this->RiotAPI->getJobApplicationByID($id);
	}
	public function isAjax () {
		return !empty($_REQUEST['ajax']);
	}
	public function do_three_lines () {
		?>
<span class="three-lines"><span></span><span></span><span></span></span>
		<?php
	}
	public function getTwitter () {
		if (!empty($twitter)) {
			return $twitter;
		}
		$twitter = new Twitter();
		return $twitter;
	}
	public function doStateOptions ($selected=false) {
		$states = array('AL'=>"Alabama",
			'AK'=>"Alaska",  
			'AZ'=>"Arizona",  
			'AR'=>"Arkansas",  
			'CA'=>"California",  
			'CO'=>"Colorado",  
			'CT'=>"Connecticut",  
			'DE'=>"Delaware",  
			'DC'=>"District Of Columbia",  
			'FL'=>"Florida",  
			'GA'=>"Georgia",  
			'HI'=>"Hawaii",  
			'ID'=>"Idaho",  
			'IL'=>"Illinois",  
			'IN'=>"Indiana",  
			'IA'=>"Iowa",  
			'KS'=>"Kansas",  
			'KY'=>"Kentucky",  
			'LA'=>"Louisiana",  
			'ME'=>"Maine",  
			'MD'=>"Maryland",  
			'MA'=>"Massachusetts",  
			'MI'=>"Michigan",  
			'MN'=>"Minnesota",  
			'MS'=>"Mississippi",  
			'MO'=>"Missouri",  
			'MT'=>"Montana",
			'NE'=>"Nebraska",
			'NV'=>"Nevada",
			'NH'=>"New Hampshire",
			'NJ'=>"New Jersey",
			'NM'=>"New Mexico",
			'NY'=>"New York",
			'NC'=>"North Carolina",
			'ND'=>"North Dakota",
			'OH'=>"Ohio",  
			'OK'=>"Oklahoma",  
			'OR'=>"Oregon",  
			'PA'=>"Pennsylvania",  
			'RI'=>"Rhode Island",  
			'SC'=>"South Carolina",  
			'SD'=>"South Dakota",
			'TN'=>"Tennessee",  
			'TX'=>"Texas",  
			'UT'=>"Utah",  
			'VT'=>"Vermont",  
			'VA'=>"Virginia",  
			'WA'=>"Washington",  
			'WV'=>"West Virginia",  
			'WI'=>"Wisconsin",  
			'WY'=>"Wyoming");
		foreach ($states as $k=>$v) {
			?>
			<?php
			echo '<option';
			if ($selected === $k || $selected === $v) {
				echo ' selected';
			}
			echo ' value="' . $k . '">';
			_e($k);
			echo '</option>'; ?>
		<?php
		}
	}
	public function doYearOptions () {
		$year = (int) date('Y');
		$year_end = $year + 10;
		while ($year < $year_end + 1) {
		?>
			<option value="<?php echo $year ?>"><?php _e($year); ?></option>
		<?php
			$year++;
		}
	}
	public function doCountryOptions () {
		$countries = array(
			"US" => "United States",
		  "AF" => "Afghanistan",
		  "AL" => "Albania",
		  "DZ" => "Algeria",
		  "AS" => "American Samoa",
		  "AD" => "Andorra",
		  "AO" => "Angola",
		  "AI" => "Anguilla",
		  "AQ" => "Antarctica",
		  "AG" => "Antigua And Barbuda",
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
		  "BA" => "Bosnia And Herzegowina",
		  "BW" => "Botswana",
		  "BV" => "Bouvet Island",
		  "BR" => "Brazil",
		  "IO" => "British Indian Ocean Territory",
		  "BN" => "Brunei Darussalam",
		  "BG" => "Bulgaria",
		  "BF" => "Burkina Faso",
		  "BI" => "Burundi",
		  "KH" => "Cambodia",
		  "CM" => "Cameroon",
		  "CA" => "Canada",
		  "CV" => "Cape Verde",
		  "KY" => "Cayman Islands",
		  "CF" => "Central African Republic",
		  "TD" => "Chad",
		  "CL" => "Chile",
		  "CN" => "China",
		  "CX" => "Christmas Island",
		  "CC" => "Cocos (Keeling) Islands",
		  "CO" => "Colombia",
		  "KM" => "Comoros",
		  "CG" => "Congo",
		  "CD" => "Congo, The Democratic Republic Of The",
		  "CK" => "Cook Islands",
		  "CR" => "Costa Rica",
		  "CI" => "Cote D'Ivoire",
		  "HR" => "Croatia (Local Name: Hrvatska)",
		  "CU" => "Cuba",
		  "CY" => "Cyprus",
		  "CZ" => "Czech Republic",
		  "DK" => "Denmark",
		  "DJ" => "Djibouti",
		  "DM" => "Dominica",
		  "DO" => "Dominican Republic",
		  "TP" => "East Timor",
		  "EC" => "Ecuador",
		  "EG" => "Egypt",
		  "SV" => "El Salvador",
		  "GQ" => "Equatorial Guinea",
		  "ER" => "Eritrea",
		  "EE" => "Estonia",
		  "ET" => "Ethiopia",
		  "FK" => "Falkland Islands (Malvinas)",
		  "FO" => "Faroe Islands",
		  "FJ" => "Fiji",
		  "FI" => "Finland",
		  "FR" => "France",
		  "FX" => "France, Metropolitan",
		  "GF" => "French Guiana",
		  "PF" => "French Polynesia",
		  "TF" => "French Southern Territories",
		  "GA" => "Gabon",
		"GB" => "United Kingdom",
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
		  "GN" => "Guinea",
		  "GW" => "Guinea-Bissau",
		  "GY" => "Guyana",
		  "HT" => "Haiti",
		  "HM" => "Heard And Mc Donald Islands",
		  "VA" => "Holy See (Vatican City State)",
		  "HN" => "Honduras",
		  "HK" => "Hong Kong",
		  "HU" => "Hungary",
		  "IS" => "Iceland",
		  "IN" => "India",
		  "ID" => "Indonesia",
		  "IR" => "Iran (Islamic Republic Of)",
		  "IQ" => "Iraq",
		  "IE" => "Ireland",
		  "IL" => "Israel",
		  "IT" => "Italy",
		  "JM" => "Jamaica",
		  "JP" => "Japan",
		  "JO" => "Jordan",
		  "KZ" => "Kazakhstan",
		  "KE" => "Kenya",
		  "KI" => "Kiribati",
		  "KP" => "Korea, Democratic People's Republic Of",
		  "KR" => "Korea, Republic Of",
		  "KW" => "Kuwait",
		  "KG" => "Kyrgyzstan",
		  "LA" => "Lao People's Democratic Republic",
		  "LV" => "Latvia",
		  "LB" => "Lebanon",
		  "LS" => "Lesotho",
		  "LR" => "Liberia",
		  "LY" => "Libyan Arab Jamahiriya",
		  "LI" => "Liechtenstein",
		  "LT" => "Lithuania",
		  "LU" => "Luxembourg",
		  "MO" => "Macau",
		  "MK" => "Macedonia, Former Yugoslav Republic Of",
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
		  "MX" => "Mexico",
		  "FM" => "Micronesia, Federated States Of",
		  "MD" => "Moldova, Republic Of",
		  "MC" => "Monaco",
		  "MN" => "Mongolia",
		  "MS" => "Montserrat",
		  "MA" => "Morocco",
		  "MZ" => "Mozambique",
		  "MM" => "Myanmar",
		  "NA" => "Namibia",
		  "NR" => "Nauru",
		  "NP" => "Nepal",
		  "NL" => "Netherlands",
		  "AN" => "Netherlands Antilles",
		  "NC" => "New Caledonia",
		  "NZ" => "New Zealand",
		  "NI" => "Nicaragua",
		  "NE" => "Niger",
		  "NG" => "Nigeria",
		  "NU" => "Niue",
		  "NF" => "Norfolk Island",
		  "MP" => "Northern Mariana Islands",
		  "NO" => "Norway",
		  "OM" => "Oman",
		  "PK" => "Pakistan",
		  "PW" => "Palau",
		  "PA" => "Panama",
		  "PG" => "Papua New Guinea",
		  "PY" => "Paraguay",
		  "PE" => "Peru",
		  "PH" => "Philippines",
		  "PN" => "Pitcairn",
		  "PL" => "Poland",
		  "PT" => "Portugal",
		  "PR" => "Puerto Rico",
		  "QA" => "Qatar",
		  "RE" => "Reunion",
		  "RO" => "Romania",
		  "RU" => "Russian Federation",
		  "RW" => "Rwanda",
		  "KN" => "Saint Kitts And Nevis",
		  "LC" => "Saint Lucia",
		  "VC" => "Saint Vincent And The Grenadines",
		  "WS" => "Samoa",
		  "SM" => "San Marino",
		  "ST" => "Sao Tome And Principe",
		  "SA" => "Saudi Arabia",
		  "SN" => "Senegal",
		  "SC" => "Seychelles",
		  "SL" => "Sierra Leone",
		  "SG" => "Singapore",
		  "SK" => "Slovakia (Slovak Republic)",
		  "SI" => "Slovenia",
		  "SB" => "Solomon Islands",
		  "SO" => "Somalia",
		  "ZA" => "South Africa",
		  "GS" => "South Georgia, South Sandwich Islands",
		  "ES" => "Spain",
		  "LK" => "Sri Lanka",
		  "SH" => "St. Helena",
		  "PM" => "St. Pierre And Miquelon",
		  "SD" => "Sudan",
		  "SR" => "Suriname",
		  "SJ" => "Svalbard And Jan Mayen Islands",
		  "SZ" => "Swaziland",
		  "SE" => "Sweden",
		  "CH" => "Switzerland",
		  "SY" => "Syrian Arab Republic",
		  "TW" => "Taiwan",
		  "TJ" => "Tajikistan",
		  "TZ" => "Tanzania, United Republic Of",
		  "TH" => "Thailand",
		  "TG" => "Togo",
		  "TK" => "Tokelau",
		  "TO" => "Tonga",
		  "TT" => "Trinidad And Tobago",
		  "TN" => "Tunisia",
		  "TR" => "Turkey",
		  "TM" => "Turkmenistan",
		  "TC" => "Turks And Caicos Islands",
		  "TV" => "Tuvalu",
		  "UG" => "Uganda",
		  "UA" => "Ukraine",
		  "AE" => "United Arab Emirates",
		  "UM" => "United States Minor Outlying Islands",
		  "UY" => "Uruguay",
		  "UZ" => "Uzbekistan",
		  "VU" => "Vanuatu",
		  "VE" => "Venezuela",
		  "VN" => "Viet Nam",
		  "VG" => "Virgin Islands (British)",
		  "VI" => "Virgin Islands (U.S.)",
		  "WF" => "Wallis And Futuna Islands",
		  "EH" => "Western Sahara",
		  "YE" => "Yemen",
		  "YU" => "Yugoslavia",
		  "ZM" => "Zambia",
		  "ZW" => "Zimbabwe"
		);
		foreach ($countries as $k=>$v) {
			?>
			<?php echo '<option value="' . $k . '">'; _e($v); echo '</option>'; ?>
		<?php
		}
	}
	public function getFacebook () {
		if (!empty($facebook)) {
			return $facebook;
		}
		$facebook = new Facebook();
		return $facebook;
	}
	public function makePostMeta ($post=false) {
		$pt = get_post_type();
		$img_data = array();
		$meta = array(
			'title'=>get_the_title(),
			'post_title'=>get_the_title(),
			'author'=>$this->makeAuthorHTML(),
			'content'=>''
		);
		switch ($pt) {
			case "article":
				break;
			case "video":
				$meta['vidid'] = get_post_meta(get_the_ID(), '_vidid', true);
				break;
			case "image":
				//need to update attachments deal
				//$img_data = attachments_get_attachments();
				$img_data = array();
				foreach ($img_data as $k=>$v) {
					$small = wp_get_attachment_image_src($v['id'], 'admin-list-thumb');
					$med = wp_get_attachment_image_src($v['id'], 'medium');
					$lar = wp_get_attachment_image_src($v['id'], 'large');
					$img_data[$k]['small_src'] = $small[0];
					$img_data[$k]['medium_src'] = $med[0];
					$img_data[$k]['large_src'] = $lar[0];
					unset($img_data[$k]['mime']);
					unset($img_data[$k]['filesize']);
					unset($img_data[$k]['order']);
				}
				$meta['images'] = $img_data;
				break;
			case "product":
				//$img_data = attachments_get_attachments();
				$img_data = array();
				foreach ($img_data as $k=>$v) {
					$lar = wp_get_attachment_image_src($v['id'], 'large');
					$img_data[$k]['src'] = $lar[0];
					unset($img_data[$k]['title']);
					unset($img_data[$k]['mime']);
					unset($img_data[$k]['filesize']);
					unset($img_data[$k]['order']);
				}
				$thumb_id = get_post_thumbnail_id(get_the_ID());
				$meta['thumb'] = wp_get_attachment_image_src($thumb_id, 'admin-list-thumb');
				$meta['thumb'] = $meta['thumb'][0];
				$meta['main_image'] = wp_get_attachment_url($thumb_id);
				$meta['images'] = $img_data;
				$meta['id'] = get_the_ID();
				$meta['slug'] = $post->post_name;
				$meta['price'] = get_post_meta(get_the_ID(), '_price', true);
				$meta['nyop'] = get_post_meta(get_the_ID(), '_nyop', true);
				$meta['shipping'] = get_post_meta(get_the_ID(), '_ship_price', true);
				unset($meta['author']);
				break;
		}
		return json_encode($meta);
	}
	public function do_title () {
		global $custom_page_title;
		if (!empty($custom_page_title)) {
			_e($custom_page_title);
		} else if (is_front_page() || get_the_title() == "") {
			bloginfo('title');
		} else {
			printf(__('%1$s // %2$s'), get_the_title(), get_bloginfo('title'));
		}
	}
	public function makeAuthorHTML () {
		$html = '<a data-ajax_load href="' . get_bloginfo('url') . '/about#team">';
		$html .= '<img src="' . get_the_author_meta('twitter_prof_pic') . '" class="author-thumbnail" />';
		$html .= '<h2>by ' . get_the_author() . '</h2>';
		$html .= '</a>';
		return $html;
	}
	public function contentWithFormatting ($more_link_text = 'Read More...', $stripteaser = 0, $more_file = '') {
		$content = get_the_content($more_link_text, $stripteaser, $more_file);
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}
	public function getStripeCustomer ($id) {
		require_once('Stripe/Stripe.php');
		if (home_url() === 'http://riotstudios.com' || home_url() === 'https://riotstudios.com') {
			Stripe::setApiKey("sk_live_et97Q4DOC0sB3n7MUa2JSTca");
		} else {
			Stripe::setApiKey("sk_test_PtucQ9cVM9KM15lNU2RmZsbb");
		}
		$customer = Stripe_Customer::retrieve($id);	
		return $customer;	
	}
	public function getStripeOrder ($id) {
		require_once('Stripe/Stripe.php');
		if (home_url() === 'http://riotstudios.com' || home_url() === 'https://riotstudios.com') {
			Stripe::setApiKey("sk_live_et97Q4DOC0sB3n7MUa2JSTca");
		} else {
			Stripe::setApiKey("sk_test_PtucQ9cVM9KM15lNU2RmZsbb");
		}
		$charge = Stripe_Charge::retrieve($id);	
		return $charge;	
	}
	public function currentURL () {
		$current = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
		$current .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		return $current;
	}
	public function testURLMatch ($url1, $url2, $loose = true) {
		if (rtrim($url1, '/') === rtrim($url2, '/')) {
			return true;
		}
		if ($loose && strpos(rtrim($url2, '/'), rtrim($url1, '/')) !== false) {
			return true;
		}
		return false;
	}
	public function doOpenGraphTags ($page_title = false) {
	?>
		<meta property="og:image" content="<?php bloginfo('template_directory'); ?>/img/touch-icon-ipad-retina.png"/>
		<meta property="og:description" content="<?php bloginfo('description'); ?>"/>
		<meta property="og:title" content="<?php $this->do_title(); ?>"/>
		<meta property="og:url" content="<?php echo $this->currentURL(); ?>"/>
		<meta property="og:site_name" content="Riot Studios"/>
	<?php
	}
	public function doAppleIconTags () {
	?>
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black">	
		<!-- iPhone SPLASHSCREEN-->
		<link href="<?php bloginfo('template_url'); ?>/img/startup-screen-320x460.png" media="(device-width: 320px)" rel="apple-touch-startup-image">
		<!-- iPhone (Retina) SPLASHSCREEN-->
		<link href="<?php bloginfo('template_url'); ?>/img/startup-screen-640x920.png" media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
		<?php /*
		make these eventually...
		<!-- iPad (portrait) SPLASHSCREEN-->
		<link href="apple-touch-startup-image-768x1004.png" media="(device-width: 768px) and (orientation: portrait)" rel="apple-touch-startup-image">
		<!-- iPad (landscape) SPLASHSCREEN-->
		<link href="apple-touch-startup-image-748x1024.png" media="(device-width: 768px) and (orientation: landscape)" rel="apple-touch-startup-image">
		<!-- iPad (Retina, portrait) SPLASHSCREEN-->
		<link href="apple-touch-startup-image-1536x2008.png" media="(device-width: 1536px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
		<!-- iPad (Retina, landscape) SPLASHSCREEN-->
		<link href="apple-touch-startup-image-1496x2048.png" media="(device-width: 1536px)  and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">

		*/ ?>

		<link rel="apple-touch-icon" href="<?php bloginfo('template_url'); ?>/img/touch-icon-iphone.png" />
		<link rel="apple-touch-icon" href="<?php bloginfo('template_url'); ?>/img/touch-icon-iphone.png" />
		<link rel="apple-touch-icon" sizes="72x72" href="<?php bloginfo('template_url'); ?>/img/touch-icon-ipad.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="<?php bloginfo('template_url'); ?>/img/touch-icon-iphone-retina.png" />
		<link rel="apple-touch-icon" sizes="144x144" href="<?php bloginfo('template_url'); ?>/img/touch-icon-ipad-retina.png" />
	<?php
	}
	public function makeMetaObject ($post_id) {
		$obj = new stdClass;
		$post_meta = get_post_meta($post_id);
		foreach ($post_meta as $k=>$v) {
			$obj->$k = $v[0];
		}
		return $obj;
	}
	function __construct () {
		$this->facebook = $this->getFacebook();
		$this->twitter = $this->getTwitter();
		$this->RiotAPI = new RiotAPI();
		$this->page_description = get_bloginfo('description');
		$this->page_keywords = get_option('riot_keywords');
		$this->_getEnvironment();
	}
	private function _getEnvironment () {
		switch (home_url()) {
			case 'http://riotstudios.com':
				$this->environment = 'live';
				break;
			case 'http://test.riotstudios.com':
				$this->environment = 'test';
				break;
			default:
				$this->environment = 'local';
				break;
		}
		return $this->environment;
	}
	private function _generateRandomString ($length = 10) {
		$original_string = array_merge(range(0,9), range('a','z'), range('A', 'Z'));
		$original_string = implode("", $original_string);
		return substr(str_shuffle($original_string), 0, $length);
	}
}
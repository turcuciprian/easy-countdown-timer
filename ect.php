<?php
/*
   Plugin Name: Easy Contdown Timer
   Plugin URI: https://wordpress.org/plugins/easy-countdown-timer
   Description: A very easy to use and intuitive countdown timer for wordpress.
	 At the moment it shows the number of days left to a given date As a shortcode
	 You can add infinite shortcodes.
   Author: Ciprian Turcu
   Version: 2.0.0
   Author URI: http://www.ciprianturcu.com
   tags: timer, countdown, days, shorcode, simple, easy, until, left, datepicker, interface, time-zone, bold, color, name
   License: GPL2
 */
 // error_reporting(E_ALL); ini_set('display_errors', '1');
function sanitize_option_ectDatePickerA($value){
	return esc_html($value);
}
function ect_plugin_menu() {
	add_menu_page( 'Easy Countdown Timer', 'Easy Countdown Timer', 'manage_options', 'ect-menu','ect_menu_options_page' );
}
function ect_menu_options_page(){
	//if form was sumbitted
	$datePickerA = get_option('ect-datePickerA');
	if($_POST){
		$getNounce = $_REQUEST['_wpnonce'];
		$nonce = wp_verify_nonce( $getNounce, 'post-ect-data' );
		if(!$nonce){
			$datePickerA = $_POST['datePickerA'];
			$datePickerA = wp_kses( $datePickerA );
			update_option( 'ect-datePickerA', $datePickerA );
		}
	}
?>
	<h2>Easy Countdown Timer Settings</h2>
  	<div id="ectPopupContent">
  	</div>
		<script type="text/javascript">
	    var isOnlyPreview = false;
	    var ectProperties = [{
	        'ectPopupContent': {
	          timeout: [],
	          pDate: '2018/2/12',
	          pHour: '00',
	          pMinutes: '00',
	          pFormat: 'D then H:M:S',
	          fontSize: '32px',
	          color: '#F00',
	          fontWeight: 'bold'
	        }
	      }
	    ];
	  </script>
	<?php
}

add_action( 'admin_menu', 'ect_plugin_menu' );
// Register style sheet.
if($_GET['page']=='ect-menu'){
	add_action( 'admin_enqueue_scripts', 'ect_admin_register_plugin_styles' );
}
add_action( 'wp_enqueue_scripts', 'ect_register_plugin_styles' );

//register/enqueue style callback function
function ect_admin_register_plugin_styles() {
	//script
	wp_register_script( 'ectCommonsReact', plugins_url( 'src/js/commons.js', __FILE__ ),[],null,true  );
	wp_enqueue_script( 'ectCommonsReact' );
	wp_register_script( 'ectBundleReact', plugins_url( 'src/js/bundle.js', __FILE__ ),['ectCommonsReact'],null, true  );
	wp_enqueue_script( 'ectBundleReact' );
}
//register/enqueue style callback function
function ect_register_plugin_styles(){
	?>
	<script type="text/javascript">
	var isOnlyPreview = true;
		var ectProperties = [];
	</script>
	<?php
	//script
	wp_register_script( 'ectCommonsReact', plugins_url( 'src/js/commons.js', __FILE__ ),[],null,true  );
	wp_enqueue_script( 'ectCommonsReact' );
	wp_register_script( 'ectBundleReact', plugins_url( 'src/js/bundle.js', __FILE__ ),['ectCommonsReact'],null, true  );
	wp_enqueue_script( 'ectBundleReact' );
}
// shortcode:
function ectShortcodeDate1( $atts ){
	$datePickerA = get_option('ect-datePickerA');
	$result = ect_daysUntil($datePickerA);
	return $result;
}
	add_shortcode( 'ectShortcode', 'ectShortAll');
function ectShortAll($atts){
	$tName = $atts['name'];
	$tHours = $atts['hour'];
	$tMinutes = $atts['minutes'];
	$tDate = $atts['date'];
	$tColor = $atts['color'];
	$tFontSize = $atts['fontsize'];
	$tBold = $atts['bold'];
	$tTimezone = $atts['timezone'];
	$tTimeFormat = $atts['timeformat'];
	$ectIDValue = 'ect_shortcode_'.substr(md5($tName.$tHours.$tMinutes.$tDate.$tColor.$tFontSize.$tBold.$tTimezone.$tTimeFormat.get_the_ID().rand(0, 100)),0,10);


	$boldText = ($tBold=='true'?'bold':'normal');
$exactDate = $tDate.' '.$tHours.':'.$tMinutes.':00';
	$daysLeft = ect_daysUntil($exactDate,$tTimezone);
	$result = "<div id=\"$ectIDValue\">
	</div>
	<script type=\"text/javascript\">
		ectProperties.push({
				'$ectIDValue': {
					timeout: [],
					pDate: '$tDate',
					pTimezoneOffset: '$tTimezone',
					pHour: '$tHours',
					pMinutes: '$tMinutes',
					pFormat: '$tTimeFormat',
					fontSize: '$tFontSize',
					color: '$tColor',
					fontWeight: '$tBold'
				}
			});
	</script>";
	if($result>=0){
		return $result;
	}
	return 0;
}
function ect_daysUntil($date,$timezone){
	date_default_timezone_set($timezone);
	return (isset($date)) ? floor((strtotime($date) - time())/60/60/24) : FALSE;
}

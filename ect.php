<?php
/*
   Plugin Name: Easy Contdown Timer
   Plugin URI: https://wordpress.org/plugins/easy-countdown-timer
   Description: A very easy to use and intuitive countdown timer for wordpress.
	 At the moment it shows the number of days left to a given date As a shortcode
	 You can add infinite shortcodes.
   Author: Ciprian Turcu
   Version: 2.1
   Author URI: http://www.ciprianturcu.com
   tags: timer, countdown,timezone, live 
   License: GPL2
 */
 error_reporting(E_ALL); ini_set('display_errors', '1');
// Register style sheet.
	add_action( 'admin_enqueue_scripts', 'ect_admin_register_plugin_styles' );
	add_action( 'wp_enqueue_scripts', 'ect_register_plugin_styles' );

//register/enqueue style callback function
function ect_admin_register_plugin_styles() {
	//script
	wp_register_script( 'ectCustomjQuery', plugins_url( 'src/js/script.js', __FILE__ ),['jquery-core'],null, true );
	wp_enqueue_script( 'ectCustomjQuery' );

	wp_register_script( 'ectCommonsReact', plugins_url( 'src/js/commons.js', __FILE__ ),['ectCustomjQuery'],null,true  );
	wp_enqueue_script( 'ectCommonsReact' );
	wp_register_script( 'ectBundleReact', plugins_url( 'src/js/bundle.js', __FILE__ ),['ectCommonsReact'],null,true  );
	wp_enqueue_script( 'ectBundleReact' );
	wp_register_style( 'ectAdminStyle', plugins_url( 'src/css/adminStyle.css', __FILE__ ) );
	wp_enqueue_style( 'ectAdminStyle' );


}
//register/enqueue style callback function
function ect_register_plugin_styles(){
	?>
	<script type="text/javascript">
	var devMode = true;
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
	add_shortcode( 'ectSc', 'ectShortAll');
function ectShortAll($atts){
	global $wpdb;
	if(!isset($atts['id'])){
		return;
	}
	$timerID = $atts['id'];
	//
	$getData = $wpdb->get_row( "SELECT * FROM `".$wpdb->prefix."ect_timers`  WHERE ID = ".$timerID.";" );
	$finalArr = unserialize($getData->allData);
	
	$tName = 'Timer '.$timerID;
	$endDate = $finalArr['enddate'];
	$numberColor = $finalArr['numbercolor'];
	$ColorTxt = $finalArr['colortxt'];
	$numberFontSize = $finalArr['numberfontsize'];
	$FontSizeTxt = $finalArr['fontsizetxt'];
	$numberBold = $finalArr['numberbold'];
	$numberBoldTxt = $finalArr['numberboldtxt'];
	$tTimezone = $finalArr['enddatetimezone'];
	$endHours = $finalArr['endhour'];
	$endMinutes = $finalArr['endminute'];
	$tTimeFormat = $finalArr['timeformat'];
	$cTxtYears = $finalArr['customtxtyears'];
	$cTxtMonths = $finalArr['customtxtmonths'];
	$cTxtWeeks = $finalArr['customtxtweeks'];
	$cTxtDays = $finalArr['customtxtdays'];
	$cTxtHours = $finalArr['customtxthours'];
	$cTxtMinutes= $finalArr['customtxtminutes'];
	$cTxtSeconds = $finalArr['customtxtseconds'];
	$customTimerEndedTxt = $finalArr['customTimerEndedTxt'];
	$ectIDValue = 'ect_shortcode_'.substr(md5($tName.$endHours.$endMinutes.$endDate.$ColorTxt.$numberFontSize.$numberBold.$tTimezone.$tTimeFormat.get_the_ID().rand(0, 100)),0,10);


	$boldText = ($numberBold=='true'?'bold':'normal');
$exactDate = $tDate.' '.$tHours.':'.$tMinutes.':00';
	$daysLeft = ect_daysUntil($exactDate,$tTimezone);
	$result = "<div class=\"ectPopupContent\" id=\"$ectIDValue\">
	</div>
	<script type=\"text/javascript\">
		ectProperties.push(
			{
				'$ectIDValue': {
					timeout: [],
					endDate: '$endDate',
					pTimezoneOffset: '$tTimezone',
					endHour: '$endHours',
					endMinute: '$endMinutes',
					pFormat: '$tTimeFormat',
					fontSize: '$numberFontSize',
					fontSizeTxt: '$FontSizeTxt',
					color: '$numberColor',
					colorTxt: '$ColorTxt',
					isBold: $numberBold,
					isBoldTxt: $numberBoldTxt,
					customTxtYears: '$cTxtYears',
					customTxtMonths: '$cTxtMonths',
					customTxtWeeks: '$cTxtWeeks',
					customTxtDays: '$cTxtDays',
					customTxtHours: '$cTxtHours',
					customTxtMinutes: '$cTxtMinutes',
					customTxtSeconds: '$cTxtSeconds',
					customTimerEndedTxt: '$customTimerEndedTxt'
				}
			}
		);
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

//TinyMCE Above button
add_action( 'media_buttons', function($editor_id){
    echo '<span id="ectPopupButton" class="button button-primary button-large">Insert Timer</span>';
} );
//admin body

function ect_admin_footer() {
    ?>

		<div class="ectMainPopupContainer hidden">
			<div class="ectPopupContent" id="ectPopupContent"></div>
		</div>
		<script type="text/javascript">
			var devMode = false;
    		var isOnlyPreview = false;
    		var ectProperties = [
			{
				'ectPopupContent': {
					timeout: [],
					endDate: '2029/2/16',
					pTimezoneOffset: '+7200000',
					endHour: '00',
					endMinute: '00',
					pFormat: 'D then H:M:S',
					fontSize: 172,
					fontSizeTxt: 32,
					color: 'green',
					colorTxt: '#F00',
					isBold: false,
					isBoldTxt: false,
					customTxtYears: 'Years',
					customTxtMonths: 'Months',
					customTxtWeeks: 'Weeks',
					customTxtDays: 'Days',
					customTxtHours: 'Hours',
					customTxtMinutes: 'Minutes',
					customTxtSeconds: 'Seconds',
					customTimerEndedTxt: 'Timer Ended'
				}
			}
		];
  </script>

    <?php
}
add_action('admin_footer', 'ect_admin_footer',1000);

//
// rest
//

add_action( 'rest_api_init', function () {
	//get timers
	register_rest_route( 'ect/v2', '/getTimers', array(
		'methods' => 'GET',
		'callback' => 'ect_rest_get_timers_callback',
	) );
	//add timer
	register_rest_route( 'ect/v2', '/addTimer', array(
		'methods' => 'PUT',
		'callback' => 'ect_rest_add_timers_callback',
	) );
} );
function ect_rest_get_timers_callback( ) {
	global $wpdb;
	$all=$wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."ect_timers;");

	// $allDataJson = unserialize($all[0]->allData);
	foreach($all as $key =>$value){
		$allDataJson[]=unserialize($value->allData);
	}
	if ($all) 
	return $allDataJson;
};
// add timer CALLBACK
function ect_rest_add_timers_callback($data){
	global $wpdb;
	
	$newArr = [];
	
		$newArr['userID'] = $data['userID'];
		$newArr['fontSize'] = $data['fontSize'];
		$newArr['fontSizeTxt'] = $data['fontSizeTxt'];
		$newArr['color'] = $data['color'];
		$newArr['colorTxt'] = $data['colorTxt'];
		$newArr['isBold'] = $data['isBold'];
		$newArr['isBoldTxt'] = $data['isBoldTxt'];
		$newArr['timezoneOffset'] = $data['timezoneOffset'];
		$newArr['endMinute'] = $data['endMinute'];
		$newArr['utcTz'] = $data['utcTz'];
		$newArr['yearsTxt'] = $data['yearsTxt'];
		$newArr['monthsTxt'] = $data['monthsTxt'];
		$newArr['weeksTxt'] = $data['weeksTxt'];
		$newArr['daysTxt'] = $data['daysTxt'];
		$newArr['hoursTxt'] = $data['hoursTxt'];
		$newArr['minutesTxt'] = $data['minutesTxt'];
		$newArr['secondsTxt'] = $data['secondsTxt'];
		$newArr['customEndedTxt'] = $data['customEndedTxt'];
		$newArr['layoutType'] = $data['layoutType'];

		$wpdb->insert( 
			$wpdb->prefix.'ect_timers', 
			array( 
				'allData' => serialize($newArr)
			)
		);
	return ["Added Timer",["returnID"=>$wpdb->insert_id]];

}
// custom database table
function ect_db_install() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'ect_timers';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE `".$wpdb->prefix."ect_timers` 
	( `ID` INT(9) NOT NULL AUTO_INCREMENT , 
	`allData` TEXT NOT NULL,
	PRIMARY KEY (`ID`)) 
	ENGINE = InnoDB;
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}
register_activation_hook( __FILE__, 'ect_db_install' );
<?php
/*
   Plugin Name: Easy Contdown Timer
   Plugin URI: https://wordpress.org/plugins/easy-countdown-timer
   Description: A very easy to use and intuitive countdown timer for wordpress.
	 At the moment it shows the number of days left to a given date As a shortcode
	 You can add infinite shortcodes.
   Author: Ciprian Turcu
   Version: 2.0.4
   Author URI: http://www.ciprianturcu.com
   tags: timer, countdown,timezone, live 
   License: GPL2
 */
 // error_reporting(E_ALL); ini_set('display_errors', '1');
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
	add_shortcode( 'ectShortcode', 'ectShortAll');
function ectShortAll($atts){
	$tName = $atts['timername'];
	$endDate = $atts['enddate'];
	$numberColor = $atts['numbercolor'];
	$ColorTxt = $atts['colortxt'];
	$numberFontSize = $atts['numberfontsize'];
	$FontSizeTxt = $atts['fontsizetxt'];
	$numberBold = $atts['numberbold'];
	$numberBoldTxt = $atts['numberboldtxt'];
	$tTimezone = $atts['enddatetimezone'];
	$endHours = $atts['endhour'];
	$endMinutes = $atts['endminute'];
	$tTimeFormat = $atts['timeformat'];
	$cTxtYears = $atts['customtxtyears'];
	$cTxtMonths = $atts['customtxtmonths'];
	$cTxtWeeks = $atts['customtxtweeks'];
	$cTxtDays = $atts['customtxtdays'];
	$cTxtHours = $atts['customtxthours'];
	$cTxtMinutes= $atts['customtxtminutes'];
	$cTxtSeconds = $atts['customtxtseconds'];
	$customTimerEndedTxt = $atts['customTimerEndedTxt'];
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

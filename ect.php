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

	$ectIDValue = 'ectScID_'.substr(md5(rand(0, 10000)),0,10);


// 	$boldText = ($numberBold=='true'?'bold':'normal');
// $exactDate = $tDate.' '.$tHours.':'.$tMinutes.':00';
// 	$daysLeft = ect_daysUntil($exactDate,$tTimezone);
	$result = "<div class=\"ectPopupContent\" id=\"$ectIDValue\">
	</div>
	<script type=\"text/javascript\">
		ectProperties.push(
			{
				'$ectIDValue': {
					timeout: [],\n";
	foreach($finalArr as $key=>$value){
		$result .="                    $key : '$value', \n";
	}
	$result .="
				}
			}
		);
	</script>";
	if($result>=0){
		return $result;
	}
	return 0;
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
		$finalArr = $value->allData;
		$newArr =unserialize($finalArr);
		$newArr['timerID']=$value->ID;
		$allDataJson[]=$newArr;
	}
	if ($all) 
	return $allDataJson;
};
// add timer CALLBACK
function ect_rest_add_timers_callback($data){
	global $wpdb;
	
	$newArr = [];
	foreach($data->get_params() as $key=> $value){
		$newArr[$key] = $data[$key];
	}
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
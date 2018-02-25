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
 $ectKs = md5(NONCE_KEY.'ect');
 $ectTableName = $wpdb->prefix . 'ect_timers';
 
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
		var ectWPPath ="<?php echo site_url();?>";
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
	global  $ectTableName;
	global $wpdb;
	if(!isset($atts['id'])){
		return;
	}
	$timerID = $atts['id'];
	//
	$getData = $wpdb->get_row( "SELECT * FROM  $ectTableName  WHERE ID = ".$timerID.";" );
	$finalArr = unserialize($getData->allData);

	$ectIDValue = 'ectScID_'.substr(md5(rand(0, 10000)),0,10);
	$result = "<div class=\"ectPopupContent\" id=\"$ectIDValue\">
	</div>
	<script type=\"text/javascript\">
		ectProperties.push(
			{
				'$ectIDValue': {
					timeout: [],\n";
	foreach($finalArr as $key=>$val){
		if(is_array($key) || is_array($val)){
			continue;
		}
		$result .="                    ".$key." : '".$val."', \n";
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
	global $ectKs;
    ?>

		<div class="ectMainPopupContainer hidden">
			<div class="ectPopupContent" id="ectPopupContent"></div>
		</div>
		<script type="text/javascript">
			var devMode = false;
    		var isOnlyPreview = false;
			var ectWPPath ="<?php echo site_url();?>";
			var ectKs = "<?php echo $ectKs;?>";
			
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
					yearsTxt: 'Years',
					monthsTxt: 'Months',
					weeksTxt: 'Weeks',
					daysTxt: 'Days',
					hoursTxt: 'Hours',
					minutesTxt: 'Minutes',
					secondsTxt: 'Seconds',
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
	$namespace = 'ect/v2';
	//get timers
	register_rest_route( $namespace, '/getTimers/(?P<ectKs>\w+)', array(
		'methods' => 'GET',
		'callback' => 'ect_rest_get_timers_callback',
	) );
	//add timer
	register_rest_route( $namespace, '/addTimer', array(
		'methods' => 'PUT',
		'callback' => 'ect_rest_add_timers_callback',
	) );
	//remove timer
	register_rest_route( $namespace, '/removeTimer/(?P<timerID>\w+)/(?P<ectKs>\w+)', array(
		'methods' => 'DELETE',
		'callback' => 'ect_rest_remove_timers_callback',
	) );
} );
function ect_rest_get_timers_callback($data) {
	global $ectKs;
	if($data['ectKs']!=$ectKs){
		return ['status'=>'Key invalid'];

	}
	global $wpdb;
	global $ectTableName;
	$all=$wpdb->get_results( "SELECT * FROM ".$ectTableName.";");

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
	global $ectTableName;
	global $ectKs;
	if($data['ectKs']!=$ectKs){
		return ['status'=>'Key invalid'];
	}
	global $wpdb;
	
	$newArr = [];
	$newObjData= $data->get_params()['data'];
	foreach($newObjData as $key=> $value){
		$newArr[$key] = $newObjData[$key];
	}
	$wpdb->insert( 
		$ectTableName, 
		array( 
			'allData' => serialize($newArr)
		)
	);
	return ["Added Timer",["returnID"=>$wpdb->insert_id]];

};
// Delete timer callback
function ect_rest_remove_timers_callback($data){
	global $wpdb;
	global $ectKs;
	global $ectTableName;
	
	$timerID = $data['timerID'];
	if($data['ectKs']!=$ectKs){
		return ['status'=>'Key invalid'];

	}
	$wpdb->delete( $ectTableName, ["ID"=>$timerID] );
	return ['status'=>'timer deleted'];

}
// custom database table
function ect_db_install() {
	global $wpdb;
	global $ectTableName;

	;
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $ectTableName 
	( `ID` INT(9) NOT NULL AUTO_INCREMENT , 
	`allData` TEXT NOT NULL,
	PRIMARY KEY (`ID`)) 
	ENGINE = InnoDB;
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}
register_activation_hook( __FILE__, 'ect_db_install' );







// menu
function ect_plugin_menu() {
	add_menu_page( 'Timers', 'Easy Countdown Timer - Manage Timers', 'manage_options', 'ect-menu','ect_menu_options_page' );
}
function ect_menu_options_page(){
	//if form was sumbitted
?>
	<div id="allTimers"></div>
<?php	
}
add_action( 'admin_menu', 'ect_plugin_menu' );

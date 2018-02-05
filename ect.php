<?php
/*
   Plugin Name: Easy Contdown Timer
   Plugin URI: https://wordpress.org/plugins/easy-countdown-timer
   Description: A very easy to use and intuitive countdown timer for wordpress.
	 At the moment it shows the number of days left to a given date As a shortcode
	 You can add infinite shortcodes.
   Author: Ciprian Turcu
   Version: 1.9.7
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
	<?php
}

add_action( 'admin_menu', 'ect_plugin_menu' );
// Register style sheet.
if($_GET['page']=='ect-menu'){
	add_action( 'admin_enqueue_scripts', 'ect_register_plugin_styles' );
}
//register/enqueue style callback function
function ect_register_plugin_styles() {
	?>
	<script type="text/javascript">
		window.ectPath = '<?php echo esc_url( site_url( '/' ).'wp-json' ); ?>';
	</script>
	<?php
	//style
	wp_register_style( 'ectJqueryUi', plugins_url( 'jquery-ui.min.css', __FILE__ )  );
	wp_enqueue_style( 'ectJqueryUi' );
	wp_register_style( 'ectStyles', plugins_url( 'style.css', __FILE__ )  );
	wp_enqueue_style( 'ectStyles' );
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
	$tDate = $atts['date'];
	$tColor = $atts['color'];
	$tFontSize = $atts['fontsize'];
	$tBold = $atts['bold'];

	$boldText = ($tBold=='true'?'bold':'normal');

	$daysLeft = ect_daysUntil($tDate);
	$result = '<span style="font-size: '.$tFontSize.';color:'.$tColor.';font-weight:'.$boldText.'">'.$daysLeft.'</span>';
	if($result>=0){
		return $result;
	}
	return 0;
}
function ect_daysUntil($date){
	return (isset($date)) ? floor((strtotime($date) - time())/60/60/24)+1 : FALSE;
}


//widget

   if(!function_exists('ect_Exists')){
     function ect_Exists($name, $path, $type,$dependencies = array(),$exportType)
     {
       $fileExists = false;

       if($exportType==='theme'){
         $file = get_template_direectry_uri().'/'.$path;
       }else{
         $file = plugin_dir_url(__FILE__).$path;
       }
         $plugin_file_headers = @get_headers($file);
         if (!$plugin_file_headers || strpos($plugin_file_headers[0], '404') > 0) {
             //file does not exist
           $fileExists = false;
         } else {
             //file exists if a plugin path
           $fileExists = true;
         }
       //inside theme path file existance ?
       // Custom Script
       if ($fileExists) {
           if ($type === 'style') {
               wp_register_style($name, $file);
               wp_enqueue_style($name);
           } else {
               wp_register_script($name, $file, $dependencies);
               wp_enqueue_script($name);
           }
       }
     }
   }



function ectRemoveCustomizer( $components ) {
    $i = array_search( 'widgets', $components );
    if ( false !== $i ) {
        unset( $components[ $i ] );
    }
    return $components;
}
add_filter( 'customize_loaded_components', 'ectRemoveCustomizer' );

//routes
//
//

add_action( 'rest_api_init', function () {
	register_rest_route( 'ect', 'getTimers', array(
    'methods' => 'GET',
    'callback' => 'ectRC_getTimers',
  ) );
	register_rest_route( 'ect', '/setTimers', array(
		'methods' => 'PUT',
		'callback' => 'ectRC_setTimers',
	) );
} );


function ectRC_getTimers( WP_REST_Request $request ) {
	$timersArr = get_option('ect-TimersArr');

	return $timersArr;
}
function ectRC_setTimers(WP_REST_Request $request ){
	$newData = $request['data'];
	$timersArr = update_option('ect-TimersArr',$newData);
	return $newData;
}

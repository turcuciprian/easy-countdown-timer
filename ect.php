<?php
/*
   Plugin Name: Easy Contdown Timer
   Plugin URI: https://wordpress.org/plugins/easy-countdown-timer
   Description: A very easy to use and intuitive countdown timer for wordpress. At the moment it shows the number of days left to a given date As a shortcode
   Author: Ciprian Turcu
   Version: 1.1.1
   Author URI: http://www.ciprianturcu.com
   tags: timer, countdown, days, shorcode, simple, easy, until, left, datepicker
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
  	<div id="ectPageContainer" ng-app="ectWP">
			<div ng-controller="mainController as mc" class="ectPanels">
		    <ang-accordion one-at-a-time="true">
		      <collapsible-item item-title="{{item.title}}" ng-repeat="item in mc.timers track by $index">
		        <div>

		          Timer Title: <input class="titleInp" type="text" ng-model="item.title" name="" value=""> <br/> Timer End Date:
		          <md-datepicker class="datePicker" type="text" md-hide-icons="all" md-open-on-focus ng-model="item.date" md-placeholder="Enter date"></md-datepicker>
		          <br/> Shortcode: <input class="readonly" type="text" name="" value="[ect-short nr=&quot;{{item.id}}&quot;]" readonly>

		          <md-button class="md-raised md-warn btnRemove" name="button" type="button" ng-click="mc.removeTimer($index)">Remove Timer</md-button>
		        </div>
		      </collapsible-item>
		      <!-- More collapsible items -->
		    </ang-accordion>
		    <p>
		      <md-button class="md-raised md-primary" name="button" ng-click="mc.AddTimer()">Add</md-button>
		    </p>
		    <p>
		      <md-button class="md-raised md-default" type="button" name="button" ng-click="mc.saveTimers()">Save Timers</md-button>

		    </p>
		    <p class="ectMessage">
		      {{mc.ectMessage}}
		    </p>
		  </div>
  	</div>
	<?php
}

add_action( 'admin_menu', 'ect_plugin_menu' );
// Register style sheet.
add_action( 'admin_enqueue_scripts', 'ect_register_plugin_styles' );
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
	wp_enqueue_script( 'jquery-ui-core' );

  wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-accordion' );
	wp_register_script( 'ectCommons', plugins_url( 'src/js/commons.js', __FILE__ )  );
	wp_enqueue_script( 'ectCommons' );
	wp_register_script( 'ectBundle', plugins_url( 'src/js/bundle.js', __FILE__ )  );
	wp_enqueue_script( 'ectBundle' );
}
// shortcode:
function ectShortcodeDate1( $atts ){
	$datePickerA = get_option('ect-datePickerA');
	$result = ect_daysUntil($datePickerA);
	return $result;
}
	add_shortcode( 'ect-short', 'ectShortAll' );
function ectShortAll($atts){
	$tNr = $atts['nr'];
	$tArrTemp = get_option('ect-TimersArr');
	foreach ($tArrTemp as $key => $value) {
		if($value['id']==$tNr){

			$result = ect_daysUntil($value['date']);
			if($result>=0){
				return $result;
			}
			return 0;
		}
	}
}
function ect_daysUntil($date){
	return (isset($date)) ? floor((strtotime($date) - time())/60/60/24)+1 : FALSE;
}


//widget


 if(!function_exists('ect_AdminEnqueueAll')){
   //Admin scripts and styles

   add_action('admin_enqueue_scripts', 'ect_AdminEnqueueAll');
   add_action('wp_enqueue_scripts', 'ect_EnqueueAll');
   function ect_EnqueueAll(){
     ect_Exists('ect_customStyle', 'src/css/style.css', 'style',null,'ectPlugin');
   }
   //Admin scripts and styles callback
   function ect_AdminEnqueueAll()
   {
     //*
     // CSS
     //*
     ect_Exists('jQueryUiCore', 'src/css/jquery-ui.css', 'style',array(),'ectPlugin');
     ect_Exists('ect_Timepicker', 'src/css/jquery.timepicker.css', 'style',array(),'ectPlugin');
     ect_Exists('ect_iris', 'src/css/iris.min.css', 'style',array(),'ectPlugin');
     ect_Exists('ect_customStyle', 'src/css/style.css', 'style',null,'ectPlugin');
       //*
       //  Custom JS
       //*
       wp_enqueue_script('jquery-ui-core');
       wp_enqueue_script('jquery-ui-draggable');
       wp_enqueue_script('jquery-ui-slider');
       wp_enqueue_script('jquery-ui-widget', false, array('jquery-ui-core'));
       wp_enqueue_script('jquery-ui-mouse', false, array('jquery-ui-core'));
       wp_enqueue_script('jquery-ui-datepicker', false, array('jquery-ui-core'));
       wp_enqueue_script('jquery-ui-draggable', false, array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse'));
       wp_enqueue_script('jquery-ui-slider', false, array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse'));
       //*
       //  Custom JS
       //*
       ect_Exists('ect_color', 'src/js/color.js', 'script',array('jquery'),'ectPlugin');
       ect_Exists('ect_iris', 'src/js/iris.js', 'script',array('jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-slider'),'ectPlugin');
       ect_Exists('ect_Timepicker', 'src/js/jquery.timepicker.min.js', 'script',array('jquery-ui-core'),'ectPlugin');
       ect_Exists('ect_CustomScript', 'src/js/script.js', 'script',array(),'ectPlugin');
     }
     add_action('customize_controls_enqueue_scripts', 'ect_AdminEnqueueAll');

   }




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



require_once 'widget.php';



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

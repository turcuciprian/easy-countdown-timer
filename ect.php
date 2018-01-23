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
	<form action="" method="POST">
<?php
	$newNounce = wp_create_nonce( 'post-ect-data');
?>
		<input type="hidden" name="_wpnounce" value="<?php echo $newNounce;?>"/>
		<label for="datePickerA">Select End Date(Date 1):</label>
		<input type="text" name="datePickerA" id="datePickerA" value="<?php echo $datePickerA ?>" placeholder="Date"/><br/>
		<label for="ect-shortcode">Copy Shortcode (Date1):
		<input type="text" name=""ect-shortcode" id="ect-shortcode" value="[ect-date1]" placeholder="Date"/>
		<p>
			<button type="submit" class="button button-primary">Save Settings</button>
		</p>
	</form>
	<?php
}

add_action( 'admin_menu', 'ect_plugin_menu' );
// Register style sheet.
add_action( 'admin_enqueue_scripts', 'ect_register_plugin_styles' );
//register/enqueue style callback function
function ect_register_plugin_styles() {
	//style
	wp_register_style( 'ectJqueryUi', plugins_url( 'jquery-ui.min.css', __FILE__ )  );
	wp_enqueue_style( 'ectJqueryUi' );
	wp_register_style( 'ectStyles', plugins_url( 'style.css', __FILE__ )  );
	wp_enqueue_style( 'ectStyles' );
	//script
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_register_script( 'ectScript', plugins_url( 'scripts.js', __FILE__ )  );
	wp_enqueue_script( 'ectScript' );
}

// shortcode:
function ectShortcodeDate1( $atts ){
	$datePickerA = get_option('ect-datePickerA');
	$result = ect_daysUntil($datePickerA);
	return $result+1;
}
add_shortcode( 'ect-date1', 'ectShortcodeDate1' );
function ect_daysUntil($date){
	return (isset($date)) ? floor((strtotime($date) - time())/60/60/24) : FALSE;
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

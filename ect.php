<?php 
/*
   Plugin Name: Easy Contdown Timer
   Plugin URI: https://wordpress.org/plugins/easy-countdown-timer
   Description: A very easy to use and intuitive countdown timer for wordpress. At the moment it shows the number of days left to a given date As a shortcode
   Author: Ciprian Turcu
   Version: 1.0.1
   Author URI: http://www.ciprianturcu.com
   tags: timer, countdown, days, shorcode, simple, easy, until, left, datepicker
 */
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
			$datePickerA = sanitize_option('ect-datePickerA',$datePickerA);
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
	$result = days_until($datePickerA);
	return $result+1;
}
add_shortcode( 'ect-date1', 'ectShortcodeDate1' );
function days_until($date){
	return (isset($date)) ? floor((strtotime($date) - time())/60/60/24) : FALSE;
}

<?php 
/*
   Plugin Name: Easy Contdown Timer
   Plugin URI: http://www.ciprianturcu.com
   Description: A very easy to use and intuitive countdown timer for wordpress
   Author: Ciprian Turcu
   Version: 1.0.0
   Author URI: http://www.ciprianturcu.com
 */
function ect_plugin_menu() {
    add_menu_page( 'Easy Countdown Timer', 'Easy Countdown Timer', 'manage_options', 'ect-menu','ect_menu_options_page' );
}
function ect_menu_options_page(){
    ?>
        <h2>Easy Countdown Timer Settings</h2>
        <label for="dateTime">Select End Date and Time:</label>
        <input type="text" name="dateTime" id="dateTime" value="" placeholder="Date"/>
	<p>
		<button type="button" class="button button-primary">Save Settings</button>
	</p>        
	<?php	
}

add_action( 'admin_menu', 'ect_plugin_menu' );

// Register style sheet.
add_action( 'admin_enqueue_scripts', 'ect_register_plugin_styles' );

//register/enqueue style callback function
function ect_register_plugin_styles() {
	//style
	wp_register_style( 'ectJqueryUi', plugins_url( 'css/jquery-ui.min.css', __FILE__ )  );
	wp_enqueue_style( 'ectJqueryUi' );


	wp_register_style( 'ectStyles', plugins_url( 'style.css', __FILE__ )  );
	wp_enqueue_style( 'ectStyles' );
	//script
	// wp_enqueue_script( 'jquery-ui-core' );
	wp_register_script( 'ectjQueryUi', plugins_url( 'js/jquery-ui.min.js', __FILE__ )  );
	wp_enqueue_script( 'ectjQueryUi' );

	wp_register_script( 'ectScript', plugins_url( 'js/script.js', __FILE__ )  );
	wp_enqueue_script( 'ectScript' );

}


<?php
/**
 * Plugin Name: Search Engine Visibility Updater
 * Plugin URI: https://github.com/ConnectThink/wp-sevu
 * Description: Switches the "Discourage search engines from indexing this site" option on or off based on current domain.  This is useful for making sure that your development site is hidden from search engines, while your live site is not.
 * Version: 1.0.1
 * Author: Connect Think
 * Author URI: http://connectthink.com
 * License: GPLv3
 */

// ------------------------------------------------------------------
// Add sections, fields and settings during admin_init
// ------------------------------------------------------------------
//
 
function sevu_settings_api_init() {
    // Add the section to reading settings so we can add our
    // fields to it
    add_settings_section(
        'sevu_setting_section',
        'Search Engine Visibility Updater',
        'sevu_setting_section_callback_function',
        'reading'
    );
    
    // Add the field with the names and function to use for our new
    // settings, put it in our new section
    add_settings_field(
        'sevu_setting_domain',
        'Live Domain',
        'sevu_setting_callback_function',
        'reading',
        'sevu_setting_section'
    );
    
    // Register our setting so that $_POST handling is done for us and
    // our callback function just has to echo the <input>
    register_setting( 'reading', 'sevu_setting_domain' );
} // sevu_settings_api_init()

add_action( 'admin_init', 'sevu_settings_api_init' );

 
// ------------------------------------------------------------------
// Settings section callback function
// ------------------------------------------------------------------
//
// This function is needed if we added a new section. This function 
// will be run at the start of our section
//

function sevu_setting_section_callback_function() {
    echo '<p>Set your live domain below. The plugin will compare this to WordPress\' current domain. If it matches, the "Discourage search engines from indexing this site" setting will be disabled. Otherwise, it will be enabled.</p>';
}

// ------------------------------------------------------------------
// Callback function for our setting
// ------------------------------------------------------------------
//
// Creates an input box for our domain setting
//

function sevu_setting_callback_function() {
    echo '<input name="sevu_setting_domain" id="sevu_setting_domain" type="text" value="'.get_option( 'sevu_setting_domain' ).'" placeholder="www.domain.com" />';
}

// ------------------------------------------------------------------
// Enable or Disable privacy mode based on domain setting
// ------------------------------------------------------------------
//

function sevu_privacy() {
    $live_url = get_option( 'sevu_setting_domain' );
    $current_url = parse_url(get_site_url());
    if( $live_url == $current_url['host'] ){
        update_option( 'blog_public', '1' );
    }else{
        update_option( 'blog_public', '0' );
    }
} 
add_action( 'init', 'sevu_privacy' );

// ------------------------------------------------------------------
// Attach our JS to the Reading Settings admin panel
// ------------------------------------------------------------------
//

function sevu_enqueue($hook) {
    if( 'options-reading.php' != $hook )
        return;
    wp_enqueue_script( 'wp-sevu-js', plugin_dir_url( __FILE__ ) . 'wp-sevu.js' );
}
add_action( 'admin_enqueue_scripts', 'sevu_enqueue' );

// ------------------------------------------------------------------
// Add settings link on plugin page
// ------------------------------------------------------------------
//

function sevu_settings_link($links) { 
  $settings_link = '<a href="options-reading.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'sevu_settings_link' );

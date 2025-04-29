<?php
/**
 * Plugin Name: August referral history by mayur
 * Description: This plugin use to manage referral history table
 * Author: Mayur Uttekar
 * Author URI: https://www.linkedin.com/in/mayur-uttekar-b96a4773/
 * Version: 1.0
 */

 // This define the plugin path
 define('AUG_PLUGIN_DIR', plugin_dir_path(__FILE__)); 

 // This file include plugin main class file where we define all files and there objects
 require_once AUG_PLUGIN_DIR . 'includes/class-aug-referral-plugin.php';

 // When plugin is load we create object of main class file and init it
 add_action('plugins_loaded', 'au_init_plugin');
 function au_init_plugin() {
     $plugin = new AU_Referral_Plugin();
     $plugin->init();
 }

 // When plugin activated we create out custom table
register_activation_hook(__FILE__, 'create_aug_referral_history_table');
function create_aug_referral_history_table() {
    global $wpdb;

    $aug_referral_history_table = $wpdb->prefix . 'aug_referral_history';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $aug_referral_history_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        username VARCHAR(50) NOT NULL,
        referral_user_name VARCHAR(50),
        join_commission FLOAT,
        unique_referral_code VARCHAR(50)
    ) $charset_collate;";

    $wpdb->query($sql);
}

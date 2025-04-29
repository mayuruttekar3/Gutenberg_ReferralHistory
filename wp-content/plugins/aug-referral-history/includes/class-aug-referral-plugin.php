<?php
// This class is used to register other class files and creating class objects of other classea
class AU_Referral_Plugin {
    public function init() {
        require_once AUG_PLUGIN_DIR . 'includes/class-aug-referral-shortcode.php';
        require_once AUG_PLUGIN_DIR . 'includes/class-aug-theme-settings.php';
        require_once AUG_PLUGIN_DIR . 'includes/class-aug-admin-referral-history-list.php';

        new Referral_Shortcode();
        new Aug_Theme_Settings();

        // We use admin_init hook here to create object of admin referral history list class because we use wp_list_table which require to load admin page first
        add_action('admin_init', function() {
            new Aug_Admin_Referral_History();
        });
    }

}
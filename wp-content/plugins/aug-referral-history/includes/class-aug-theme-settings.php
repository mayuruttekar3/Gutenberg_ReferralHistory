<?php
class Aug_Theme_Settings {

    public function __construct() {
        add_action("admin_init", [$this, 'display_theme_panel_fields']);
        add_action('admin_menu', [$this, 'add_theme_menu_item']);
    }


    /* Custom theme option start */
    function theme_settings_page() {
        ?>
        <div class="wrap">
            <h1>Custom Theme Options</h1>
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php
                        settings_fields("section");
                        do_settings_sections("theme-options");      
                        submit_button(); 
                    ?>
            </form>
        </div>
        <?php
    }

    function join_commission() { ?>
        <input type="text" name="join_commission" style="width:70%;height:40px" id="join_commission" value="<?php echo get_option('join_commission'); ?>">
    <?php
    }
    
    function display_theme_panel_fields() {
        add_settings_section("section", "Custom Theme Option", '', "theme-options");
    
        add_settings_field("join_commission", "Join Commission", [$this, "join_commission"], "theme-options", "section");
    
        register_setting("section", "join_commission");
    }    
    
    function add_theme_menu_item() {
        add_menu_page('Custom Theme Options', 'Custom Theme Options', 'manage_options', 'theme-panel', [$this, 'theme_settings_page'], 'dashicons-admin-generic', 99);
    }
    /* Custom theme option end */

}
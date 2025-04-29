<?php
class Referral_Shortcode {
    public function __construct() {
        
        // This action use to enqueue script
        add_action('wp_enqueue_scripts', [$this,'fn_enqueue_custom_scripts'], 99);

        // This frontend register form shortcode
        add_shortcode('display_user_register_form', [$this,'fn_display_user_register_form']);

        // This is register user ajax handler
        add_action('wp_ajax_fn_register_user', [$this, 'fn_register_user']);
        add_action('wp_ajax_nopriv_fn_register_user', [$this, 'fn_register_user']);

        // This is validate referral code ajax handler
        add_action('wp_ajax_fn_validate_referral', [$this, 'fn_validate_referral']);
        add_action('wp_ajax_nopriv_fn_validate_referral', [$this, 'fn_validate_referral']);
    }

    function fn_enqueue_custom_scripts() {
        wp_enqueue_script('aug-custom-script', plugin_dir_url(dirname(__FILE__)) . '/assets/js/custom_script.js', array( 'jquery' ), '', true);    
        wp_localize_script('aug-custom-script', 'my_ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php') ));
    }

    function fn_display_user_register_form() {
        ob_start();
?>
        <form method="post" class="register-form" id="referral-form">
            <h2>Register</h2>
            
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="reg_firstName" name="firstName" required>
            </div>

            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="reg_lastName" name="lastName" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="reg_email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="reg_password" name="password" required>
            </div>

            <div class="form-group">
                <label for="referral">Referral Code</label>
                <input type="text" id="referral" name="referral">
                <span id="ref-status"></span>
            </div>

            <div class="form-group">
                <button type="submit" id="reg_user" class="register-button">Register</button>
            </div>
            <div class="reg_form_error_message"></div>

        </form>
<?php
        return ob_get_clean();
    }

    function fn_register_user() {

        global $wpdb;

        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email_address = trim($_POST['email']);
        $password = trim($_POST['password']);
        $referral_code = $_POST['referral_code'];

        $username = explode('@', $email_address);

        if (!is_email($email_address)) {
            $errors->add('email', $this->get_error_message('email'));
            return $errors;
        }

        if (username_exists($first_name) || email_exists($email_address)) {
            echo json_encode(array('loggedin' => false, 'message' => 'Email Already Exist'));
            wp_die();
        }

        if (username_exists($username[0])) {
            echo json_encode(array('loggedin' => false, 'message' => 'Username Already Exist'));
            wp_die();
        }

        $user_data = array(
            'user_login'    => $username[0],
            'first_name'    => $first_name,
            'last_name'   	=> $last_name,
            'nickname'      => $username[0],
            'display_name'  => $username[0],
            'user_email'    => $email_address,
            'user_pass'     => $password,
        );
        $user_id = wp_insert_user($user_data);
        
        if ($user_id) {

            $user = new WP_User($user_id);
            $referral_history_list_table = $wpdb->prefix . 'aug_referral_history';
            $unique_referral_code = bin2hex(random_bytes(8));
            update_user_meta($user_id, 'user_unique_referral_code', sanitize_text_field($unique_referral_code));
            
            $referral_user_name = '-';
            
            if( $referral_code != '' ) {
                $referral_user_query = new WP_User_Query([
                    'meta_key'   => 'user_unique_referral_code',
                    'meta_value' => $referral_code,
                    'number'     => 1,
                    'fields'     => 'ID',
                ]);
                $referral_user_data = $referral_user_query->get_results();
                if (!empty($referral_user_data)) {
                    $referral_user_info = get_userdata($referral_user_data[0]);                    
                    $referral_user_name = $referral_user_info->user_login;
                }
            }

            $data = array(
                'user_id' => $user_id,
                'username' => $username[0],
                'referral_user_name' => $referral_user_name,
                'join_commission' => get_option('join_commission'),
                'unique_referral_code' => $unique_referral_code
            );

            $format = array('%d', '%s', '%s', '%f', '%s');
            $insert_count = $wpdb->insert($referral_history_list_table, $data, $format);

            $info = array();
            $info['user_login'] = $email_address;
            $info['user_password'] = $password;
            $user_signon = wp_signon($info, false);
            echo json_encode(array('loggedin' => true, 'message' => 'Register Done'));
        }
        exit(0);
    }

    public function fn_validate_referral() {
        
        $referral_code = isset($_POST['referral_code']) ? sanitize_text_field($_POST['referral_code']) : '';
    
        if (empty($referral_code)) {
            wp_send_json_error(['message' => 'Referral code is required.']);
        }

        $user_query = new WP_User_Query([
            'meta_key'   => 'user_unique_referral_code',
            'meta_value' => $referral_code,
            'number'     => 1,
            'fields'     => 'ID',
        ]);
    
        if (!empty($user_query->get_results())) {
            echo json_encode(array('counter' => true, 'message' => 'Valid referral code.'));
        } else {
            echo json_encode(array('counter' => false, 'message' => 'Invalid referral code.'));
        }
    
        wp_die();
    }

}
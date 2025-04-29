<?php
// Hook to create the admin menu
add_action('admin_menu', function() {
    add_menu_page('Referral History List', 'Referral History List', 'edit_posts', 'referral-list', 'render_referral_list_page', 'dashicons-list-view', 100);
});

// Load WP_List_Table if not loaded
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

// Define the custom list table class
class Aug_Admin_Referral_History extends WP_List_Table {

    function __construct() {
        parent::__construct([
            'singular' => 'Referral',
            'plural'   => 'Referrals',
            'ajax'     => false,
        ]);
    }

    // This is wp list table default function which use to display columns of table
    function get_columns() {
        return [
            'cb'                    => '<input type="checkbox" />',
            'id'                    => 'ID',
            'username'              => 'Username',
            'referral_user_name'    => 'Referral User Name',
            'join_commission'       => 'Join Commission',
            'unique_referral_code'  => 'Unique Referral Code',
        ];
    }

    // This is also wp list table default function which use to process the columns which we define in previous function
    function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aug_referral_history';

        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = [];

        $this->_column_headers = [$columns, $hidden, $sortable];

        // Handle bulk/single actions
        $this->process_bulk_action();

        $data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        $this->items = $data;
    }

    // This is default of checkbox row of wp list table
    function column_cb($item) {
        return sprintf('<input type="checkbox" name="id[]" value="%s" />', $item['id']);
    }

    // This function use to display values into table and define row actions
    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
                return $item['id'];
            case 'username':
                return $item['username'] . $this->row_actions([
                    'edit'   => sprintf('<a href="?page=%s&action=edit&id=%s">Edit</a>', $_REQUEST['page'], $item['id']),
                    'delete' => sprintf('<a href="?page=%s&action=delete&id=%s" onclick="return confirm(\'Are you sure you want to delete?\')">Delete</a>', $_REQUEST['page'], $item['id']),
                ]);
            case 'referral_user_name':
                return $item['referral_user_name'];
            case 'join_commission':
                return $item['join_commission'];
            case 'unique_referral_code':
                return $item['unique_referral_code'];
            default:
                return print_r($item, true);
        }
    }

    function get_bulk_actions() {
        return [
            'bulk-delete' => 'Delete Selected',
        ];
    }

    // This function is use to process the bulk delete operation
    function process_bulk_action() {
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'aug_referral_history';

        // Single Delete
        if ($this->current_action() === 'delete' && !empty($_GET['id'])) {
            ob_start();

            $id = intval($_GET['id']);
            $table_name = $wpdb->prefix . 'aug_referral_history';
            $referral = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");
            $user_id = $referral->user_id;

            $wpdb->delete($table_name, ['id' => $id]);

            wp_delete_user(intval($user_id));

            // Safe redirect after delete
            wp_safe_redirect(admin_url('admin.php?page=' . $_REQUEST['page'] . '&deleted=1'));
            return ob_get_clean();
        }

        // Bulk Delete
        if ($this->current_action() === 'bulk-delete' && !empty($_REQUEST['id'])) {
            ob_start();
            $ids = array_map('intval', $_REQUEST['id']);
            foreach ($ids as $id) {
                $referral = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");
                $user_id = $referral->user_id;

                $wpdb->delete($table_name, ['id' => $id]);
                wp_delete_user(intval($user_id));
            }

            wp_safe_redirect(admin_url('admin.php?page=' . $_REQUEST['page'] . '&bulk_deleted=1'));
            return ob_get_clean();
        }
    }
}

// Render the Referral List Page
function render_referral_list_page() {
    global $wpdb;

    // Show admin notices
    if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
        echo '<div class="notice notice-success is-dismissible"><p>Referral deleted successfully.</p></div>';
    }

    if (isset($_GET['bulk_deleted']) && $_GET['bulk_deleted'] == 1) {
        echo '<div class="notice notice-success is-dismissible"><p>Selected referrals deleted successfully.</p></div>';
    }

    if (isset($_GET['updated']) && $_GET['updated'] == 1) {
        echo '<div class="notice notice-success is-dismissible"><p>Referral updated successfully.</p></div>';
    }

    // Handle Edit Form
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id'])) {
        $id = intval($_GET['id']);
        $table_name = $wpdb->prefix . 'aug_referral_history';
        $referral = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");

        if ($referral) {
            ?>
            <div class="wrap">
                <h1>Edit Referral</h1>
                <form method="post">
                    <input type="hidden" name="id" value="<?php echo esc_attr($referral->id); ?>">
                    <table class="form-table">
                        <tr>
                            <th><label>Username</label></th>
                            <td><input type="text" name="username" value="<?php echo esc_attr($referral->username); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label>Referral User Name</label></th>
                            <td><input type="text" name="referral_user_name" value="<?php echo esc_attr($referral->referral_user_name); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label>Join Commission</label></th>
                            <td><input type="number" step="0.01" name="join_commission" value="<?php echo esc_attr($referral->join_commission); ?>" class="regular-text"></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" name="update_referral" class="button-primary" value="Update Referral">
                        <a href="<?php echo admin_url('admin.php?page=' . $_REQUEST['page']); ?>" class="button">Cancel</a>
                    </p>
                </form>
            </div>
            <?php
        }
    } else {
        // Default view: List Table
        $table = new Aug_Admin_Referral_History();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Referral History List</h1>
            <form method="post">
                <?php
                $table->prepare_items();
                $table->display();
                ?>
            </form>
        </div>
        <?php
    }
}

// Handle Save (Update Referral)
if (isset($_POST['update_referral'])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'aug_referral_history';

    $id = intval($_POST['id']);
    $username = sanitize_text_field($_POST['username']);
    $referral_user_name = sanitize_text_field($_POST['referral_user_name']);
    $join_commission = floatval($_POST['join_commission']);

    $wpdb->update(
        $table_name,
        [
            'username' => $username,
            'referral_user_name' => $referral_user_name,
            'join_commission' => $join_commission,
        ],
        ['id' => $id]
    );

    wp_safe_redirect(admin_url('admin.php?page=' . $_REQUEST['page'] . '&updated=1'));
    exit;
}

?>

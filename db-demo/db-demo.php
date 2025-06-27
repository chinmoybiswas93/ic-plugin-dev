<?php
/**
 * Plugin Name: DB Demo
 * Description: A demo plugin for database operations.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: db-demo
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main DB Demo Plugin Class
 * 
 * Handles database operations and admin interface for storing person data
 */
class DB_Demo
{
    /**
     * Plugin version for database updates
     */
    const VERSION = '1.0';

    /**
     * Database table name (without prefix)
     */
    const TABLE_NAME = 'demo_table';

    // ========================================
    // INITIALIZATION & SETUP
    // ========================================

    /**
     * Initialize the plugin
     */
    public function __construct()
    {
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks()
    {
        register_activation_hook(__FILE__, array($this, 'on_plugin_activation'));
        add_action('admin_menu', array($this, 'register_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Form submission handlers
        add_action('admin_post_demo_db_submit', array($this, 'handle_add_person_submission'));
        add_action('admin_post_demo_db_edit', array($this, 'handle_edit_person_submission'));
        add_action('admin_post_demo_db_delete', array($this, 'handle_delete_person_submission'));
    }

    /**
     * Plugin activation callback
     */
    public function on_plugin_activation()
    {
        $this->create_database_table();
        error_log('DB Demo plugin activated.');
    }

    // ========================================
    // DATABASE OPERATIONS
    // ========================================

    /**
     * Create the database table
     */
    private function create_database_table()
    {
        global $wpdb;

        $table_name = $this->get_full_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20) NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Get the full table name with WordPress prefix
     * 
     * @return string
     */
    private function get_full_table_name()
    {
        global $wpdb;
        return $wpdb->prefix . self::TABLE_NAME;
    }

    /**
     * Insert person into database
     * 
     * @param array $person_data
     * @return int|false
     */
    private function insert_person_to_database($person_data)
    {
        global $wpdb;

        return $wpdb->insert(
            $this->get_full_table_name(),
            $person_data,
            array('%s', '%s', '%s')
        );
    }

    /**
     * Update person in database
     * 
     * @param int $person_id
     * @param array $person_data
     * @return int|false
     */
    private function update_person_in_database($person_id, $person_data)
    {
        global $wpdb;

        return $wpdb->update(
            $this->get_full_table_name(),
            $person_data,
            array('id' => $person_id),
            array('%s', '%s', '%s'),
            array('%d')
        );
    }

    /**
     * Delete person from database
     * 
     * @param int $person_id
     * @return int|false
     */
    private function delete_person_from_database($person_id)
    {
        global $wpdb;

        return $wpdb->delete(
            $this->get_full_table_name(),
            array('id' => $person_id),
            array('%d')
        );
    }

    /**
     * Get person by ID from database
     * 
     * @param int $person_id
     * @return array|null
     */
    private function get_person_by_id_from_database($person_id)
    {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->get_full_table_name()} WHERE id = %d", $person_id),
            ARRAY_A
        );
    }

    /**
     * Get all persons from database
     * 
     * @return array
     */
    private function get_all_persons_from_database()
    {
        global $wpdb;

        $table_name = $this->get_full_table_name();

        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
            return array();
        }

        $results = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY created_at DESC",
            ARRAY_A
        );

        return $results ?: array();
    }

    // ========================================
    // ADMIN INTERFACE
    // ========================================

    /**
     * Register admin menu page
     */
    public function register_admin_menu()
    {
        add_menu_page(
            __('DB Demo', 'db-demo'),           // Page title
            __('DB Demo', 'db-demo'),           // Menu title
            'manage_options',                    // Capability
            'db-demo',                          // Menu slug
            array($this, 'render_admin_page'),  // Callback
            'dashicons-database',               // Icon
            20                                  // Position
        );
    }

    /**
     * Enqueue admin styles and scripts
     */
    public function enqueue_admin_assets($hook)
    {
        if ($hook !== 'toplevel_page_db-demo') {
            return;
        }

        wp_add_inline_style('wp-admin', $this->get_admin_css_styles());
    }

    /**
     * Get admin CSS styles
     * 
     * @return string
     */
    private function get_admin_css_styles()
    {
        return '
            .db-demo-table .column-id {
                width: 60px !important;
                text-align: center;
            }
            .db-demo-table .column-created {
                width: 150px;
            }
            .db-demo-table .column-actions {
                width: 120px !important;
                text-align: center;
            }
            .db-demo-table .button {
                margin: 0 2px;
            }
            .db-demo-form-buttons {
                display: flex;
                gap: 10px;
                align-items: center;
            }
            .db-demo-form-buttons .button {
                margin: 0;
            }
        ';
    }

    /**
     * Render the main admin page
     */
    public function render_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('DB Demo Plugin', 'db-demo') . '</h1>';

        $this->display_admin_notices();

        // Check if we're in edit mode
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['person_id'])) {
            $this->render_edit_person_form();
        } else {
            $this->render_add_person_form();
        }

        $this->render_persons_list_table();

        echo '</div>';
    }

    /**
     * Display admin notices based on URL parameters
     */
    private function display_admin_notices()
    {
        if (!isset($_GET['message'])) {
            return;
        }

        $message = sanitize_text_field($_GET['message']);
        $notice_class = 'notice notice-success is-dismissible';
        $notice_text = '';

        switch ($message) {
            case 'success':
                $notice_text = __('Person added successfully!', 'db-demo');
                break;
            case 'updated':
                $notice_text = __('Person updated successfully!', 'db-demo');
                break;
            case 'deleted':
                $notice_text = __('Person deleted successfully!', 'db-demo');
                break;
            case 'error':
                $notice_class = 'notice notice-error is-dismissible';
                $notice_text = __('An error occurred. Please try again.', 'db-demo');
                break;
            default:
                return;
        }

        if ($notice_text) {
            printf(
                '<div class="%s"><p>%s</p></div>',
                esc_attr($notice_class),
                esc_html($notice_text)
            );
        }
    }

    // ========================================
    // FORM RENDERING
    // ========================================

    /**
     * Render the add person form
     */
    private function render_add_person_form()
    {
        ?>
        <h2><?php esc_html_e('Add Person', 'db-demo'); ?></h2>

        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('demo_db_submit_action', 'demo_db_nonce'); ?>
            <input type="hidden" name="action" value="demo_db_submit">

            <?php $this->render_person_form_fields(); ?>

            <?php submit_button(__('Add Person', 'db-demo')); ?>
        </form>
        <?php
    }

    /**
     * Render the edit person form
     */
    private function render_edit_person_form()
    {
        $person_id = intval($_GET['person_id']);

        if (!wp_verify_nonce($_GET['nonce'], 'edit_person_' . $person_id)) {
            wp_die(__('Security check failed.', 'db-demo'));
        }

        $person = $this->get_person_by_id_from_database($person_id);

        if (!$person) {
            wp_die(__('Person not found.', 'db-demo'));
        }

        ?>
        <h2><?php esc_html_e('Edit Person', 'db-demo'); ?></h2>

        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('demo_db_edit_action', 'demo_db_edit_nonce'); ?>
            <input type="hidden" name="action" value="demo_db_edit">
            <input type="hidden" name="person_id" value="<?php echo esc_attr($person_id); ?>">

            <?php $this->render_person_form_fields($person); ?>

            <div class="db-demo-form-buttons">
                <?php submit_button(__('Update Person', 'db-demo'), 'primary', 'submit', false); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=db-demo')); ?>" class="button">
                    <?php esc_html_e('Cancel', 'db-demo'); ?>
                </a>
            </div>
        </form>
        <?php
    }

    /**
     * Render person form fields (shared between add and edit forms)
     * 
     * @param array|null $person Existing person data for edit mode
     */
    private function render_person_form_fields($person = null)
    {
        $name_value = $person ? esc_attr($person['name']) : '';
        $email_value = $person ? esc_attr($person['email']) : '';
        $phone_value = $person ? esc_attr($person['phone']) : '';

        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="person_name"><?php esc_html_e('Name', 'db-demo'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="person_name" name="person_name" class="regular-text" required maxlength="255"
                            value="<?php echo $name_value; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="person_email"><?php esc_html_e('Email', 'db-demo'); ?></label>
                    </th>
                    <td>
                        <input type="email" id="person_email" name="person_email" class="regular-text" required maxlength="100"
                            value="<?php echo $email_value; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="person_phone"><?php esc_html_e('Phone', 'db-demo'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="person_phone" name="person_phone" class="regular-text" maxlength="20"
                            value="<?php echo $phone_value; ?>">
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    }

    // ========================================
    // TABLE RENDERING
    // ========================================

    /**
     * Render the persons list table
     */
    private function render_persons_list_table()
    {
        $persons = $this->get_all_persons_from_database();

        if (empty($persons)) {
            echo '<p>' . esc_html__('No persons found.', 'db-demo') . '</p>';
            return;
        }

        ?>
        <h2><?php esc_html_e('Persons', 'db-demo'); ?></h2>

        <table class="widefat fixed striped db-demo-table">
            <thead>
                <tr>
                    <th class="column-id"><?php esc_html_e('ID', 'db-demo'); ?></th>
                    <th><?php esc_html_e('Name', 'db-demo'); ?></th>
                    <th><?php esc_html_e('Email', 'db-demo'); ?></th>
                    <th><?php esc_html_e('Phone', 'db-demo'); ?></th>
                    <th class="column-created"><?php esc_html_e('Created At', 'db-demo'); ?></th>
                    <th class="column-actions"><?php esc_html_e('Actions', 'db-demo'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($persons as $person): ?>
                    <tr>
                        <td class="column-id"><?php echo esc_html($person['id']); ?></td>
                        <td><?php echo esc_html($person['name']); ?></td>
                        <td><?php echo esc_html($person['email']); ?></td>
                        <td><?php echo esc_html($person['phone'] ?: '—'); ?></td>
                        <td class="column-created"><?php echo esc_html($this->format_date_for_display($person['created_at'])); ?>
                        </td>
                        <td class="column-actions">
                            <?php $this->render_person_action_buttons($person['id']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Render action buttons for each person row
     * 
     * @param int $person_id
     */
    private function render_person_action_buttons($person_id)
    {
        $edit_url = add_query_arg(array(
            'action' => 'edit',
            'person_id' => $person_id,
            'nonce' => wp_create_nonce('edit_person_' . $person_id)
        ), admin_url('admin.php?page=db-demo'));

        ?>
        <a href="<?php echo esc_url($edit_url); ?>" class="button button-small">
            <?php esc_html_e('Edit', 'db-demo'); ?>
        </a>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: inline;">
            <?php wp_nonce_field('delete_person_' . $person_id, 'delete_nonce'); ?>
            <input type="hidden" name="action" value="demo_db_delete">
            <input type="hidden" name="person_id" value="<?php echo esc_attr($person_id); ?>">
            <button type="submit" class="button button-small"
                onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this person?', 'db-demo'); ?>');">
                <?php esc_html_e('Delete', 'db-demo'); ?>
            </button>
        </form>
        <?php
    }

    // ========================================
    // FORM SUBMISSION HANDLERS
    // ========================================

    /**
     * Handle add person form submission
     */
    public function handle_add_person_submission()
    {
        if (!$this->verify_form_submission_security('demo_db_nonce', 'demo_db_submit_action')) {
            return;
        }

        $person_data = $this->validate_and_sanitize_person_data($_POST);
        if (!$person_data) {
            wp_safe_redirect(admin_url('admin.php?page=db-demo&message=error'));
            exit;
        }

        $result = $this->insert_person_to_database($person_data);
        $message = ($result !== false) ? 'success' : 'error';

        wp_safe_redirect(admin_url('admin.php?page=db-demo&message=' . $message));
        exit;
    }

    /**
     * Handle edit person form submission
     */
    public function handle_edit_person_submission()
    {
        if (!$this->verify_form_submission_security('demo_db_edit_nonce', 'demo_db_edit_action')) {
            return;
        }

        if (empty($_POST['person_id'])) {
            wp_safe_redirect(admin_url('admin.php?page=db-demo&message=error'));
            exit;
        }

        $person_id = intval($_POST['person_id']);
        $person_data = $this->validate_and_sanitize_person_data($_POST);

        if (!$person_data) {
            wp_safe_redirect(admin_url('admin.php?page=db-demo&message=error'));
            exit;
        }

        $result = $this->update_person_in_database($person_id, $person_data);
        $message = ($result !== false) ? 'updated' : 'error';

        wp_safe_redirect(admin_url('admin.php?page=db-demo&message=' . $message));
        exit;
    }

    /**
     * Handle delete person form submission
     */
    public function handle_delete_person_submission()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions.'));
        }

        $person_id = intval($_POST['person_id']);

        if (!wp_verify_nonce($_POST['delete_nonce'], 'delete_person_' . $person_id)) {
            wp_die(__('Security check failed.', 'db-demo'));
        }

        $result = $this->delete_person_from_database($person_id);
        $message = ($result !== false) ? 'deleted' : 'error';

        wp_safe_redirect(admin_url('admin.php?page=db-demo&message=' . $message));
        exit;
    }

    // ========================================
    // UTILITY & HELPER METHODS
    // ========================================

    /**
     * Verify form submission security (permissions and nonce)
     * 
     * @param string $nonce_field
     * @param string $nonce_action
     * @return bool
     */
    private function verify_form_submission_security($nonce_field, $nonce_action)
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions.'));
        }

        if (!wp_verify_nonce($_POST[$nonce_field], $nonce_action)) {
            wp_die(__('Security check failed.', 'db-demo'));
        }

        return true;
    }

    /**
     * Validate and sanitize person data from form submission
     * 
     * @param array $post_data
     * @return array|false
     */
    private function validate_and_sanitize_person_data($post_data)
    {
        if (empty($post_data['person_name']) || empty($post_data['person_email'])) {
            return false;
        }

        $name = sanitize_text_field($post_data['person_name']);
        $email = sanitize_email($post_data['person_email']);
        $phone = isset($post_data['person_phone']) ? sanitize_text_field($post_data['person_phone']) : '';

        if (!is_email($email)) {
            return false;
        }

        return array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        );
    }

    /**
     * Format date for display in admin interface
     * 
     * @param string $date
     * @return string
     */
    private function format_date_for_display($date)
    {
        return date_i18n(
            get_option('date_format') . ' ' . get_option('time_format'),
            strtotime($date)
        );
    }
}

// Initialize the plugin
new DB_Demo();
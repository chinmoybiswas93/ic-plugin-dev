<?php

class Admin_settings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_quick_qr_code_settings', [$this, 'settings_save']);
        // add_action('admin_notices', [$this, 'settings_notice']);
    }

    public function add_admin_menu()
    {
        //ad a menu to dashboard
        add_menu_page(
            'Quick QR Code Settings',
            'Quick QR Code',
            'manage_options',
            'quick-qr-code',
            [$this, 'settings_page'],
            'dashicons-admin-generic',
            100
        );
    }

    public function settings_page()
    {
        // Check if the user has the required capability
        if (!current_user_can('manage_options')) {
            return;
        }

        echo '<div class="wrap">';
        echo '<h1>Quick QR Code Settings</h1>';

        // Show the notice here
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>';
            echo "<script>
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.delete('status');
                    window.history.replaceState({}, document.title, url.pathname + url.search);
                }
            </script>";
        }

        // Include the settings page HTML
        include plugin_dir_path(__FILE__) . 'settings-page.php';
        echo '</div>';
    }

    public function settings_save()
    {
        // Verify nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'quick_qr_code_nonce')) {
            wp_die('Invalid nonce');
        }

        $errors = [];
        $input = $_POST;

        // Validation
        if (empty($input['qr-code-label'])) {
            $errors['qr-code-label'] = 'Label is required.';
        }
        if (!isset($input['qr-code-size']) || !is_numeric($input['qr-code-size']) || $input['qr-code-size'] < 50 || $input['qr-code-size'] > 1000) {
            $errors['qr-code-size'] = 'Size must be between 50 and 1000.';
        }
        if (!isset($input['qr-code-margin']) || !is_numeric($input['qr-code-margin']) || $input['qr-code-margin'] < 0 || $input['qr-code-margin'] > 20) {
            $errors['qr-code-margin'] = 'Margin must be between 0 and 20.';
        }
        if (!empty($input['qr-code-dark']) && !preg_match('/^[a-fA-F0-9]{6}$/', $input['qr-code-dark'])) {
            $errors['qr-code-dark'] = 'Dark color must be a 6-digit hex code.';
        }
        if (!empty($input['qr-code-light']) && !preg_match('/^[a-fA-F0-9]{6}$/', $input['qr-code-light'])) {
            $errors['qr-code-light'] = 'Light color must be a 6-digit hex code.';
        }
        if (!empty($input['qr-code-logo-url']) && !filter_var($input['qr-code-logo-url'], FILTER_VALIDATE_URL)) {
            $errors['qr-code-logo-url'] = 'Logo URL must be a valid URL.';
        }
        if (!isset($input['qr-code-logo-size']) || !is_numeric($input['qr-code-logo-size']) || $input['qr-code-logo-size'] < 10 || $input['qr-code-logo-size'] > 100) {
            $errors['qr-code-logo-size'] = 'Logo size must be between 10 and 100.';
        }
        if (empty($input['qr-code-font-size'])) {
            $errors['qr-code-font-size'] = 'Font size is required.';
        }
        if (!in_array($input['qr-code-position'] ?? '', ['left', 'right'])) {
            $errors['qr-code-position'] = 'Position must be left or right.';
        }

        if (!empty($errors)) {
            // Store errors and old input in a transient for one request
            set_transient('quick_qr_code_errors', $errors, 30);
            set_transient('quick_qr_code_old', $input, 30);
            wp_safe_redirect(admin_url('admin.php?page=quick-qr-code'));
            exit;
        }

        // Sanitization (as before)
        $sanitized = [];
        $sanitized['qr-code-label'] = sanitize_text_field($input['qr-code-label'] ?? '');
        $sanitized['qr-code-size'] = max(50, min(1000, intval($input['qr-code-size'])));
        $sanitized['qr-code-margin'] = max(0, min(20, intval($input['qr-code-margin'])));
        $sanitized['qr-code-dark'] = preg_replace('/[^a-fA-F0-9]/', '', $input['qr-code-dark'] ?? '000000');
        $sanitized['qr-code-light'] = preg_replace('/[^a-fA-F0-9]/', '', $input['qr-code-light'] ?? 'ffffff');
        $sanitized['qr-code-logo-url'] = esc_url_raw($input['qr-code-logo-url'] ?? '');
        $sanitized['qr-code-logo-size'] = max(10, min(100, intval($input['qr-code-logo-size'])));
        $sanitized['qr-code-font-size'] = sanitize_text_field($input['qr-code-font-size'] ?? '12px');
        $sanitized['qr-code-position'] = in_array($input['qr-code-position'] ?? 'right', ['left', 'right']) ? $input['qr-code-position'] : 'right';

        update_option('quick_qr_code_settings', $sanitized);
        wp_safe_redirect(admin_url('admin.php?page=quick-qr-code&status=success'));
        exit;
    }
}

new Admin_settings();
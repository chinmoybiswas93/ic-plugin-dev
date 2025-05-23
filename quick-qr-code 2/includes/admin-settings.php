<?php

class Admin_settings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('wp_ajax_quick_qr_code_save_settings', [$this, 'ajax_save_settings']);
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

    public function enqueue_admin_scripts()
    {
        wp_enqueue_style('quick-qr-code-admin', plugin_dir_url(__DIR__) . 'assets/css/admin-style.css', [], time(), 'all');
        wp_enqueue_script('quick-qr-code-admin', plugin_dir_url(__DIR__) . 'assets/js/admin-script.js', ['jquery'], time(), true);
        wp_localize_script('quick-qr-code-admin', 'QuickQRCodeAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('quick_qr_code_ajax_nonce'),
        ]);
    }

    public function settings_page()
    {
        // Check if the user has the required capability
        if (!current_user_can('manage_options')) {
            return;
        }

        echo '<div class="wrap">';
        echo '<h1>Quick QR Code Settings</h1>';
        // Include the settings page HTML
        include plugin_dir_path(__FILE__) . 'template/form.php';
        echo '</div>';
    }

    public function ajax_save_settings()
    {
        check_ajax_referer('quick_qr_code_ajax_nonce', 'security');

        $errors = [];
        $input = $_POST;

        // Validation (same as before)
        if (empty($input['qr-code-label'])) {
            $errors[] = 'Label is required.';
        }
        if (!isset($input['qr-code-size']) || !is_numeric($input['qr-code-size']) || $input['qr-code-size'] < 50 || $input['qr-code-size'] > 1000) {
            $errors[] = 'Size must be between 50 and 1000.';
        }
        if (!isset($input['qr-code-margin']) || !is_numeric($input['qr-code-margin']) || $input['qr-code-margin'] < 0 || $input['qr-code-margin'] > 20) {
            $errors[] = 'Margin must be between 0 and 20.';
        }
        if (!empty($input['qr-code-dark']) && !preg_match('/^[a-fA-F0-9]{6}$/', $input['qr-code-dark'])) {
            $errors[] = 'Dark color must be a 6-digit hex code.';
        }
        if (!empty($input['qr-code-light']) && !preg_match('/^[a-fA-F0-9]{6}$/', $input['qr-code-light'])) {
            $errors[] = 'Light color must be a 6-digit hex code.';
        }
        if (!empty($input['qr-code-logo-url']) && !filter_var($input['qr-code-logo-url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Logo URL must be a valid URL.';
        }
        if (!isset($input['qr-code-logo-size']) || !is_numeric($input['qr-code-logo-size']) || $input['qr-code-logo-size'] < 10 || $input['qr-code-logo-size'] > 100) {
            $errors[] = 'Logo size must be between 10 and 100.';
        }
        if (empty($input['qr-code-font-size'])) {
            $errors[] = 'Font size is required.';
        }
        if (!in_array($input['qr-code-position'] ?? '', ['left', 'right'])) {
            $errors[] = 'Position must be left or right.';
        }

        if (!empty($errors)) {
            wp_send_json_error(['errors' => $errors]);
        }

        // Sanitization (same as before)
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

        wp_send_json_success(['message' => 'Settings saved successfully.']);
    }
}

new Admin_settings();
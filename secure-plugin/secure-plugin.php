<?php
/*
Plugin Name: Secure Form Plugin
Description: A simple form submission plugin
Version: 1.0
Author: Chinmoy Biswas
*/

function secure_plugin_enqueue_scripts()
{
    wp_enqueue_style('secure-plugin-style', plugin_dir_url(__FILE__) . 'assets/css/style.css', [], time(), 'all');

    wp_enqueue_script('secure-plugin', plugin_dir_url(__FILE__) . 'ajax-script.js', array('jquery'), time(), true);

    wp_localize_script('secure-plugin', 'securePlugin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('secure_plugin_nonce_ajax'),
    ));
}
add_action('wp_enqueue_scripts', 'secure_plugin_enqueue_scripts');

//create the form shortcode
function secure_plugin_form_shortcode()
{
    ob_start();
    include plugin_dir_path(__FILE__) . 'form.php';
    $form = ob_get_clean();
    return shortcode_unautop($form);
}
add_shortcode('ic_secure_form', 'secure_plugin_form_shortcode');


//handle the form submission
function secure_plugin_init()
{

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['secure_form_submit']) && $_POST['secure_form_submit'] == 'Submit') {
        $isValid = true;
        //check nonce
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'secure_plugin_nonce')) {
            echo '<h3>Error: Invalid nonce</h3>';
            $isValid = false;
        }
        //check if name is string
        if (!is_string($_POST['name'])) {
            echo '<h3>Error: Name must be a string</h3>';
            $isValid = false;
        }

        if (!is_email($_POST['email'])) {
            echo '<h3>Error: Email must be a valid email address</h3>';
            $isValid = false;
        }

        if (!is_numeric($_POST['age'])) {
            echo '<h3>Error: Age must be a number</h3>';
            $isValid = false;
        }

        if (!is_string($_POST['message'])) {
            echo '<h3>Error: Message must be a string</h3>';
            $isValid = false;
        }


        $sanitized = $_POST;
        $sanitized['name'] = sanitize_text_field($_POST['name']);
        $sanitized['email'] = sanitize_email($_POST['email']);
        $sanitized['age'] = intval($_POST['age']);
        $sanitized['message'] = sanitize_textarea_field($_POST['message']);

        if ($isValid) {
            //save to options table
            update_option('secure_form_submission', $sanitized);
            //redirect to the same page
            $redirect_url = isset($_POST['redirect_url']) ? esc_url($_POST['redirect_url']) : home_url();
            wp_safe_redirect($redirect_url);
            exit;
        }
    }
}
add_action('init', 'secure_plugin_init');


//handle the ajax request
function secure_plugin_form_ajax()
{
    if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'secure_plugin_nonce_ajax')) {
        wp_send_json_error('Invalid nonce');
    }
    //parse serialized data
    parse_str($_POST['formData'], $post);

    //check if name is string
    if (!is_string($post['name'])) {
        wp_send_json_error('Name must be a string');
    }

    if (!is_email($post['email'])) {
        wp_send_json_error('Email must be a valid email address');
    }

    if (!is_numeric($post['age'])) {
        wp_send_json_error('Age must be a number');
    }

    if (!is_string($post['message'])) {
        wp_send_json_error('Message must be a string');
    }


    $sanitized = $post;
    $sanitized['name'] = sanitize_text_field($post['name']);
    $sanitized['email'] = sanitize_email($post['email']);
    $sanitized['age'] = intval($post['age']);
    $sanitized['message'] = sanitize_textarea_field($post['message']);

    update_option('secure_form_submission', $sanitized);
    //send success response
    wp_send_json_success($sanitized);
}

add_action('wp_ajax_secure_plugin_form_action', 'secure_plugin_form_ajax');

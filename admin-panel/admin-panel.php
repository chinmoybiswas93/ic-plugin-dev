<?php
/*
Plugin Name: Admin Panel Plugin
Description: A Test Plugin for Admin Panel 
Version: 1.0
Author: Chinmoy Biswas
*/


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function admin_panel_menu()
{
    add_menu_page(
        'Admin Panel',
        'Admin Panel',
        'manage_options',
        'admin-panel',
        'admin_panel_page',
        'dashicons-admin-generic',
        100
    );
}

add_action('admin_menu', 'admin_panel_menu');

function admin_panel_page()
{
    echo '<div class="wrap">';
    echo '<h1>Admin Panel</h1>';
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
        echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>';
    }
    include plugin_dir_path(__FILE__) . 'form.php';
    echo '</div>';
}

function admin_panel_enqueue_scripts($hook)
{
    if ($hook != 'toplevel_page_admin-panel') {
        return;
    }
    wp_enqueue_style('admin-panel-style', plugin_dir_url(__FILE__) . 'assets/css/style.css', [], time(), 'all');

    wp_enqueue_script('admin-panel', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), time(), true);

}
add_action('admin_enqueue_scripts', 'admin_panel_enqueue_scripts');


function admin_panel_settings_save()
{
    update_option('admin_panel_settings', $_POST);
    wp_safe_redirect(admin_url('admin.php?page=admin-panel&settings-updated=true'));
    exit;
}
add_action('admin_post_admin_panel_settings', 'admin_panel_settings_save');
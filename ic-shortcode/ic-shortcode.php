<?php
/*
Plugin Name: IC Shortcode
Description: A simple WordPress plugin to add custom shortcodes.
Version: 1.0
Author: Chinmoy Biswas
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__FILE__) . 'includes/class-message-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-asset-manager.php';
class IC_Shortcode
{
    public function __construct()
    {
        add_action('init', array($this, 'init'));
    }

    public function init()
    {
        add_shortcode('ic_shortcode_hello', array($this, 'render_shortcode'));
        new IC_Asset_Manager(); // Initialize asset manager
        new IC_Message_Shortcode();
    }

    public function render_shortcode($atts, $content = null)
    {
        // Extract shortcode attributes
        $atts = shortcode_atts(
            array(
                'title' => 'Default Title',
                'color' => 'blue',
            ),
            $atts
        );

        // Allow nested shortcodes and formatting in content
        $content = do_shortcode($content);

        return sprintf(
            '<div style="color: %s; border: 2px solid %s; padding:10px;"><h2>%s</h2><div>%s</div></div>',
            esc_attr($atts['color']),
            esc_attr($atts['color']),
            esc_html($atts['title']),
            $content ? $content : esc_html__('This is the default content.', 'ic-shortcode')
        );
    }

    public function enqueue_styles()
    {
        wp_enqueue_style('ic-shortcode-style', plugin_dir_url(__FILE__) . 'assets/style.css');
    }
}

new IC_Shortcode();

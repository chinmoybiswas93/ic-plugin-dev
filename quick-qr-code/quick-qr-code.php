<?php
/*
Plugin Name: Quick QR Code Plugin
Description: A simple QR code for post 
Version: 1.0
Author: Chinmoy Biswas
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class QuickQRCode
{
    const VERSION = "1.0.0";
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_filter('the_content', array($this, 'append_qr_code'));
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('quick-qr-code', plugin_dir_url(__FILE__) . 'assets/css/style.css', [], time(), 'all');
        wp_enqueue_script('quick-qr-code', plugin_dir_url(__FILE__) . 'assets/js/script.js', ['jquery'], time(), true);
    }

    public function append_qr_code($content)
    {
        if (!is_singular()) {
            return $content;
        }

        $post_type = get_post_type();
        $allowed_post_types = apply_filters('quick_qr_code_post_types', array('post', 'page'));

        if (!in_array($post_type, $allowed_post_types)) {
            return $content;
        }

        $post_url = get_permalink();
        //filters
        $size = apply_filters('quick_qr_code_size', 120);
        $margin = apply_filters('quick_qr_code_margin', 2);
        $dark = apply_filters('quick_qr_code_dark_color', '000000');
        $light = apply_filters('quick_qr_code_light_color', 'ffffff');
        $label = apply_filters('quick_qr_code_label', "Scan Me");
        $logo_url = apply_filters('quick_qr_code_logo_url', '');
        $logo_size = apply_filters('quick_qr_code_logo_size', 50);
        $fontSize = apply_filters('quick_qr_code_font_size', '12px');
        $position = apply_filters('quick_qr_code_position', 'right');

        $qr_url = "https://quickchart.io/qr?";
        $params = array(
            'text' => $post_url,
            'size' => $size,
            'margin' => $margin,
            'dark' => $dark,
            'light' => $light,
            'format' => 'png',
        );

        if (!empty($logo_url)) {
            $params['centerImageUrl'] = $logo_url;
            $params['centerImageSizeRatio'] = $logo_size / 100;
        }

        $qr_url .= http_build_query($params);

        $position_class = ($position == 'left') ? 'quick-qr-left' : 'quick-qr-right';
        $qr_html = sprintf(
            '<div class="quick-qr-code %s" data-size="%d">
            <div class="quick-qr-label" style="font-size:%s">%s</div>
            <img src="%s" alt="QR Code" style="width: %dpx; height: %dpx;">
            </div>',
            esc_attr($position_class),
            intval($size),
            esc_attr($fontSize),
            esc_html($label),
            esc_url($qr_url),
            intval($size),
            intval($size)
        );


        return "{$content}{$qr_html}";
    }
}

new QuickQRCode();
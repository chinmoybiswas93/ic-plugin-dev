<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class IC_Message_Shortcode
{
    public function __construct()
    {

        add_shortcode('ic_message', array($this, 'render_shortcode'));
    }

    public function render_shortcode($atts, $content = null)
    {
        // Extract shortcode attributes
        $atts = shortcode_atts(
            array(
                'type' => 'info',
            ),
            $atts
        );

        // Allow nested shortcodes and formatting in content
        $content = do_shortcode($content);
        $content = wpautop($content); // Convert line breaks to paragraphs

        // Define message types with colors, icons, and styles
        $message_types = array(
            'success' => array(
                'color' => '#155724',
                'bg_color' => '#d4edda',
                'border_color' => '#c3e6cb',
                'icon' => '✓'
            ),
            'warning' => array(
                'color' => '#856404',
                'bg_color' => '#fff3cd',
                'border_color' => '#ffeaa7',
                'icon' => '⚠'
            ),
            'danger' => array(
                'color' => '#721c24',
                'bg_color' => '#f8d7da',
                'border_color' => '#f5c6cb',
                'icon' => '✕'
            ),
            'info' => array(
                'color' => '#0c5460',
                'bg_color' => '#d1ecf1',
                'border_color' => '#bee5eb',
                'icon' => 'ℹ'
            )
        );

        // Get the message type or default to 'info'
        $type = isset($message_types[$atts['type']]) ? $atts['type'] : 'info';
        $message_style = $message_types[$type];

        // Create the template
        $template = sprintf(
            '<div class="ic-message ic-message-%s" style="
                color: %s;
                background-color: %s;
                border: 2px solid %s;
                border-radius: 4px;
                padding: 15px;
                margin: 10px 0;
                display: flex;
                align-items: center;
                font-family: Arial, sans-serif;
            ">
                <span class="ic-message-icon" style="
                    font-size: 18px;
                    margin-right: 10px;
                    font-weight: bold;
                    flex-shrink: 0;
                    display:inline-block;
                    margin-top: 2px;
                ">%s</span>
                <div class="ic-message-content" style="flex: 1;">%s</div>
            </div>',
            esc_attr($type),
            esc_attr($message_style['color']),
            esc_attr($message_style['bg_color']),
            esc_attr($message_style['border_color']),
            $message_style['icon'],
            $content ? $content : esc_html__('This is the default message content.', 'ic-shortcode')
        );

        return $template;
    }
}
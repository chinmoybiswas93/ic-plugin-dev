<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class IC_Asset_Manager
{

    private $assets = array();
    public function __construct()
    {
        $this->assets = array(
            'css' => array(
                'ic-shortcode-main' => array(
                    'src' => plugin_dir_url(__FILE__) . '../assets/style.css',
                    'deps' => array(),
                    'ver' => '1.0.0',
                    'media' => 'all',
                )
            ),
            'js' => array(
                'ic-shortcode-main' => array(
                    'src' => plugin_dir_url(__FILE__) . '../assets/script.js',
                    'deps' => array('jquery'),
                    'ver' => '1.0.0',
                    'in_footer' => true,
                )
            ),
        );
        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function enqueue_assets()
    {
        // Enqueue JavaScript files
        foreach ($this->assets['js'] as $handle => $asset) {
            wp_enqueue_script(
                $handle,
                $asset['src'],
                $asset['deps'],
                $asset['ver'],
                $asset['in_footer']
            );
        }

        // Enqueue CSS files
        foreach ($this->assets['css'] as $handle => $asset) {
            wp_enqueue_style(
                $handle,
                $asset['src'],
                $asset['deps'],
                $asset['ver'],
                $asset['media']
            );
        }
    }

}


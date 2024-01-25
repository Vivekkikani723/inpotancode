<?php

/**
 * Plugin Name:       Custom Post Filter
 * Description:       A plugin to add post filtering functionality. Use shortcode [post_filter] to display it.
 * Version:           1.0.0
 * Author:            Himat Parsana
 * Text Domain:       cpf
 */

defined('ABSPATH') || die("Invalid Request");

/**
 * ============================
 * define plugin path/url/file
 * ============================
 */
define('PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_URL', plugin_dir_url(__FILE__));
define('PLUGIN_FILE', __FILE__);

/**
 * =============
 * Include Path
 * =============
 */
include PLUGIN_PATH . 'view/class-custom-post-filter.php';
include PLUGIN_PATH . 'view/shortcode-custom-post-filter.php';
include PLUGIN_PATH . 'view/shortcode-related-insights.php';
include PLUGIN_PATH . 'view/ajax-post-filter.php';

/**
 * ================
 * Enqueue scripts
 * ================
 */
if ( ! class_exists('post_filter_scripts') ) :

    class post_filter_scripts
    {

        public function __construct()
        {
            add_action('wp_enqueue_scripts', array(__CLASS__, 'add_enqueue_scripts'));
        }

        //function for enqueue script and style
        public static function add_enqueue_scripts()
        {

            wp_enqueue_script('custom-post-filter', PLUGIN_URL . 'view/js/script.js', array('jquery'), '1.0.7', true);
        }
    }
endif;

new post_filter_scripts;

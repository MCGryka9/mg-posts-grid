<?php
/**
 * Plugin Name: MG Posts Grid
 * Description: Uniwersalny grid wpisów (posty / CPT) z filtrem kategorii i shortcode.
 * Version: 1.0.0
 * Author: Gryczan.eu
 */

if (!defined('ABSPATH')) exit;

define('MGPG_PATH', plugin_dir_path(__FILE__));
define('MGPG_URL', plugin_dir_url(__FILE__));

require_once MGPG_PATH . 'includes/admin-page.php';
require_once MGPG_PATH . 'includes/shortcode.php';

/* Assets */
add_action('wp_enqueue_scripts', function () {
    wp_register_style(
        'mgpg-grid',
        MGPG_URL . 'assets/css/grid.css',
        [],
        '1.0.0'
    );

    wp_register_script(
        'mgpg-filter',
        MGPG_URL . 'assets/js/filter.js',
        [],
        '1.0.0',
        true
    );
});

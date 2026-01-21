<?php
/**
 * Plugin Name: MG Posts Grid
 * Description: Responsywna siatka wpisów z dynamicznymi kategoriami.
 * Version: 1.1
 * Author: Gryczan.eu
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Definicja stałych dla łatwiejszego zarządzania ścieżkami
define( 'MG_GRID_PATH', plugin_dir_path( __FILE__ ) );
define( 'MG_GRID_URL', plugin_dir_url( __FILE__ ) );

// 1. Ładowanie stylów CSS
function mg_posts_grid_assets() {
    wp_enqueue_style( 
        'mg-grid-style', 
        MG_GRID_URL . 'assets/css/style.css', 
        array(), 
        '1.1' 
    );
}
add_action( 'wp_enqueue_scripts', 'mg_posts_grid_assets' );

// 2. Ładowanie logiki shortcode'u
require_once MG_GRID_PATH . 'includes/shortcode.php';

// 3. Ładowanie panelu administracyjnego
require_once MG_GRID_PATH . 'includes/admin-page.php';
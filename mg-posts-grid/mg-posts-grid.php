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

// 1. Rejestracja typu posta Realizacja
function mg_register_realizacja_cpt() {
    $labels = array(
        'name' => 'Realizacje',
        'singular_name' => 'Realizacja',
        'add_new' => 'Dodaj nową',
        'add_new_item' => 'Dodaj nową realizację',
        'edit_item' => 'Edytuj realizację',
        'all_items' => 'Wszystkie realizacje',
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-portfolio',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'taxonomies' => array('category', 'post_tag'), // Obsługa domyślnych kategorii i tagów WP
        'show_in_rest' => true,
    );
    register_post_type('realizacja', $args);
}
add_action('init', 'mg_register_realizacja_cpt');

// 2. Dodanie pola na własny link
function mg_add_custom_link_meta_box() {
    add_meta_box('mg_post_link', 'Link zewnętrzny', 'mg_display_custom_link_meta_box', 'realizacja', 'side');
}
add_action('add_meta_boxes', 'mg_add_custom_link_meta_box');

function mg_display_custom_link_meta_box($post) {
    $value = get_post_meta($post->ID, '_mg_custom_link', true);
    echo '<input type="url" name="mg_custom_link" value="' . esc_attr($value) . '" style="width:100%" placeholder="https://..." />';
}

function mg_save_custom_link_meta($post_id) {
    if (isset($_POST['mg_custom_link'])) {
        update_post_meta($post_id, '_mg_custom_link', esc_url_raw($_POST['mg_custom_link']));
    }
}
add_action('save_post', 'mg_save_custom_link_meta');

// 3. Ładowanie stylów CSS
function mg_posts_grid_assets() {
    wp_enqueue_style( 
        'mg-grid-style', 
        MG_GRID_URL . 'assets/css/style.css', 
        array(), 
        '1.1' 
    );
}
add_action( 'wp_enqueue_scripts', 'mg_posts_grid_assets' );

// 4. Ładowanie logiki shortcode'u
require_once MG_GRID_PATH . 'includes/shortcode.php';

// 5. Ładowanie panelu administracyjnego
require_once MG_GRID_PATH . 'includes/admin-page.php';
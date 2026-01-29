<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function mg_posts_grid_display( $atts ) {
    $atts = shortcode_atts( array(
        'count'    => 6,
        'type'     => 'realizacja', // Zmiana domyślnego typu
        'category' => '', 
        'taxonomy' => '', 
        'term'     => '',
        'filter'   => '0',
    ), $atts, 'mg_posts_grid' );

    $post_type = sanitize_text_field( $atts['type'] );
    
    // Sprawdzamy, czy w adresie URL jest wybrana kategoria z filtra
    $url_filter = isset($_GET['mg_cat']) ? sanitize_text_field($_GET['mg_cat']) : '';

    $args = array(
        'post_type'      => $post_type,
        'posts_per_page' => intval( $atts['count'] ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    // Wykrywanie właściwej taksonomii dla filtra (np. category lub project_category dla Divi)
    $tax_name = !empty($atts['taxonomy']) ? $atts['taxonomy'] : '';
    if (empty($tax_name)) {
        $taxonomies = get_object_taxonomies($post_type);
        $tax_name = !empty($taxonomies) ? $taxonomies[0] : 'category';
    }

    // LOGIKA FILTROWANIA
    if ( ! empty($url_filter) && $url_filter !== 'all' ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => $tax_name,
                'field'    => 'slug',
                'terms'    => $url_filter,
            ),
        );
    } elseif ( ! empty( $atts['taxonomy'] ) && ! empty( $atts['term'] ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => sanitize_text_field( $atts['taxonomy'] ),
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $atts['term'] ),
            ),
        );
    } elseif ( ! empty( $atts['category'] ) ) {
        $args['category_name'] = sanitize_text_field( $atts['category'] );
    }

    $query = new WP_Query( $args );
    $output = '';

    // --- SEKCJA FILTRA (DROPDOWN) ---
    if ( $atts['filter'] === '1' ) {
        $terms = get_terms( array(
            'taxonomy'   => $tax_name,
            'hide_empty' => true,
        ) );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $output .= '<div class="mg-grid-filter-wrapper">';
            $output .= '<select id="mg-grid-cat-filter" onchange="location = this.value;">';
            $output .= '<option value="' . remove_query_arg('mg_cat') . '">Wszystkie kategorie</option>';
            
            foreach ( $terms as $term ) {
                $url = add_query_arg( 'mg_cat', $term->slug );
                $selected = ( $url_filter === $term->slug ) ? 'selected' : '';
                $output .= '<option value="' . esc_url($url) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
            }
            
            $output .= '</select>';
            $output .= '</div>';
        }
    }

    if ( $query->have_posts() ) {
        $output .= '<div class="mg-grid-container">';

while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();

            // 1. Logika własnego linku
            $custom_link = get_post_meta($post_id, '_mg_custom_link', true);
            $final_link = !empty($custom_link) ? esc_url($custom_link) : get_permalink();

            // 2. Pobieranie kategorii do etykiety
            $display_cat = '';
            $current_taxs = get_object_taxonomies(get_post_type($post_id));
            foreach ($current_taxs as $tax) {
                $post_terms = get_the_terms($post_id, $tax);
                if (!empty($post_terms) && !is_wp_error($post_terms)) {
                    $display_cat = $post_terms[0]->name;
                    break;
                }
            }

            $img_url = get_the_post_thumbnail_url( $post_id, 'large' );
            $img_url = $img_url ? $img_url : 'https://via.placeholder.com/500x500?text=No+Image';

            // 3. Generowanie HTML z użyciem $final_link
            $output .= '<article class="mg-grid-card">';
            $output .= '<a href="' . $final_link . '" target="_blank" class="mg-grid-image-link">';
            $output .= '<div class="mg-grid-image" style="background-image: url(' . esc_url( $img_url ) . ');"></div>';
            $output .= '</a>';
            $output .= '<div class="mg-grid-content">';
            $output .= '<h3 class="mg-grid-title"><a href="' . $final_link . '">' . get_the_title() . '</a></h3>';
            
            if ( ! empty( $display_cat ) ) {
                $output .= '<span class="mg-grid-category">' . esc_html( $display_cat ) . '</span>';
            }
            $output .= '<div class="mg-grid-excerpt">' . wp_trim_words( get_the_excerpt(), 50 ) . '</div>';
            $output .= '</div>';
            $output .= '</article>';
        }

        $output .= '</div>';
        wp_reset_postdata();
    } else {
        $output .= '<p class="mg-grid-error">Brak treści w tej kategorii.</p>';
    }

    return $output;
}

add_shortcode( 'mg_posts_grid', 'mg_posts_grid_display' );

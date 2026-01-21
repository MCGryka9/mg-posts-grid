<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function mg_posts_grid_display( $atts ) {
    $atts = shortcode_atts( array(
        'count'    => 6,
        'type'     => 'post',
        'category' => '',
        'taxonomy' => '', // Jeśli używasz Divi, możesz tu wpisać 'project_category'
        'term'     => '',
    ), $atts, 'mg_posts_grid' );

    $args = array(
        'post_type'      => sanitize_text_field( $atts['type'] ),
        'posts_per_page' => intval( $atts['count'] ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    // Filtrowanie (taksonomie lub kategorie)
    if ( ! empty( $atts['taxonomy'] ) && ! empty( $atts['term'] ) ) {
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

    if ( $query->have_posts() ) {
        $output .= '<div class="mg-grid-container">';

        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $current_post_type = get_post_type($post_id);

            // --- INTELIGENTNE POBIERANIE KATEGORII ---
            $display_cat = '';

            // 1. Sprawdzamy czy użytkownik wymusił konkretną taksonomię w shortcode
            if ( ! empty( $atts['taxonomy'] ) ) {
                $terms = get_the_terms( $post_id, $atts['taxonomy'] );
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    $display_cat = $terms[0]->name;
                }
            } 
            // 2. Jeśli nie, szukamy automatycznie (obsługa Divi i innych CPT)
            else {
                // Pobieramy wszystkie taksonomie przypisane do tego typu posta
                $taxonomies = get_object_taxonomies( $current_post_type, 'names' );
                
                foreach ( $taxonomies as $tax ) {
                    $terms = get_the_terms( $post_id, $tax );
                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                        // Wybieramy pierwszy znaleziony termin i kończymy szukanie
                        $display_cat = $terms[0]->name;
                        break; 
                    }
                }
            }

            $img_url = get_the_post_thumbnail_url( $post_id, 'large' );
            $img_url = $img_url ? $img_url : 'https://via.placeholder.com/500x500?text=No+Image';

            $output .= '<article class="mg-grid-card">';
            
            // Obrazek
            $output .= '<a href="' . get_permalink() . '" class="mg-grid-image-link">';
            $output .= '<div class="mg-grid-image" style="background-image: url(' . esc_url( $img_url ) . ');"></div>';
            $output .= '</a>';

            // Treść
            $output .= '<div class="mg-grid-content">';
            
            // Tytuł
            $output .= '<h3 class="mg-grid-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            
            // DYNAMICZNY SPAN Z KATEGORIĄ
            if ( ! empty( $display_cat ) ) {
                $output .= '<span class="mg-grid-category">' . esc_html( $display_cat ) . '</span>';
            }

            $output .= '<div class="mg-grid-excerpt">' . wp_trim_words( get_the_excerpt(), 12 ) . '</div>';
            
            $output .= '</div>';
            $output .= '</article>';
        }

        $output .= '</div>';
        wp_reset_postdata();
    } else {
        $output = '<p class="mg-grid-error">Brak treści do wyświetlenia.</p>';
    }

    return $output;
}
add_shortcode( 'mg_posts_grid', 'mg_posts_grid_display' );

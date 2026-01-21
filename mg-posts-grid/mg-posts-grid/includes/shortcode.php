<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function mg_posts_grid_display( $atts ) {
    // 1. Definicja atrybutów shortcode
    $atts = shortcode_atts( array(
        'count'    => 6,
        'type'     => 'post',
        'category' => '',         // Slug dla standardowych kategorii
        'taxonomy' => '',         // Nazwa własnej taksonomii
        'term'     => '',         // Slug termu we własnej taksonomii
    ), $atts, 'mg_posts_grid' );

    // 2. Budowanie zapytania WP_Query
    $args = array(
        'post_type'      => sanitize_text_field( $atts['type'] ),
        'posts_per_page' => intval( $atts['count'] ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    // Filtrowanie po taksonomiach lub kategoriach
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

            // 3. DYNAMICZNE POBIERANIE KATEGORII
            $category_name = '';
            
            if ( ! empty( $atts['taxonomy'] ) ) {
                // Dla Custom Post Types i własnych taksonomii
                $terms = get_the_terms( $post_id, $atts['taxonomy'] );
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    $category_name = $terms[0]->name;
                }
            } else {
                // Dla standardowych wpisów
                $categories = get_the_category( $post_id );
                if ( ! empty( $categories ) ) {
                    $category_name = $categories[0]->name;
                }
            }

            // Pobieranie miniatury (wymuszamy duży rozmiar, CSS zajmie się proporcjami)
            $img_url = get_the_post_thumbnail_url( $post_id, 'large' );
            $img_url = $img_url ? $img_url : 'https://via.placeholder.com/500x500?text=Brak+zdjęcia';

            // Generowanie HTML kafelka
            $output .= '<article class="mg-grid-card">';
            
            // Obrazek (klikalny)
            $output .= '<a href="' . get_permalink() . '" class="mg-grid-image-link">';
            $output .= '<div class="mg-grid-image" style="background-image: url(' . esc_url( $img_url ) . ');"></div>';
            $output .= '</a>';

            // Kontener treści
            $output .= '<div class="mg-grid-content">';
            
            // Tytuł
            $output .= '<h3 class="mg-grid-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            
            // SPAN Z KATEGORIĄ (dynamiczny)
            if ( ! empty( $category_name ) ) {
                $output .= '<span class="mg-grid-category">' . esc_html( $category_name ) . '</span>';
            }

            // Zajawka (skrócona do 12 słów)
            $output .= '<div class="mg-grid-excerpt">' . wp_trim_words( get_the_excerpt(), 12 ) . '</div>';
            
            $output .= '</div>'; // Koniec .mg-grid-content
            $output .= '</article>';
        }

        $output .= '</div>'; // Koniec .mg-grid-container
        wp_reset_postdata();
    } else {
        $output = '<p class="mg-grid-error">Nie znaleziono żadnych wpisów.</p>';
    }

    return $output;
}
add_shortcode( 'mg_posts_grid', 'mg_posts_grid_display' );
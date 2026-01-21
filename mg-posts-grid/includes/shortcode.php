<?php
if (!defined('ABSPATH')) exit;

add_shortcode('mg_posts', 'mgpg_render_posts');

function mgpg_render_posts($atts) {

    $atts = shortcode_atts([
        'post_type'      => 'post',
        'taxonomy'       => '',
        'terms'          => '',
        'columns_pc'     => 4,
        'columns_mobile' => 2,
        'show_filter'    => 1,
    ], $atts);

    wp_enqueue_style('mgpg-grid');
    wp_enqueue_script('mgpg-filter');

    $tax_query = [];
    if ($atts['taxonomy'] && $atts['terms']) {
        $tax_query[] = [
            'taxonomy' => $atts['taxonomy'],
            'field'    => 'slug',
            'terms'    => explode(',', $atts['terms']),
        ];
    }

    $query = new WP_Query([
        'post_type'      => $atts['post_type'],
        'posts_per_page' => -1,
        'tax_query'      => $tax_query,
    ]);

    if (!$query->have_posts()) {
        return '<p>Brak wpisów.</p>';
    }

    ob_start();
    ?>

    <div class="mgpg-wrapper"
         style="--cols-pc:<?= intval($atts['columns_pc']); ?>;--cols-mobile:<?= intval($atts['columns_mobile']); ?>">

        <?php if ($atts['show_filter'] && $atts['taxonomy']) :
            $terms = get_terms(['taxonomy' => $atts['taxonomy'], 'hide_empty' => true]);
        ?>
            <select class="mgpg-filter">
                <option value="all">Wszystkie</option>
                <?php foreach ($terms as $term) : ?>
                    <option value="<?= esc_attr($term->slug); ?>">
                        <?= esc_html($term->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <div class="mgpg-grid">
            <?php while ($query->have_posts()) : $query->the_post();
                $post_terms = [];
                if ($atts['taxonomy']) {
                    $assigned = get_the_terms(get_the_ID(), $atts['taxonomy']);
                    if ($assigned) {
                        foreach ($assigned as $t) {
                            $post_terms[] = $t->slug;
                        }
                    }
                }
            ?>
                <article class="mgpg-card" data-terms="<?= esc_attr(implode(' ', $post_terms)); ?>">
                    <a href="<?php the_permalink(); ?>">
                        <div class="mgpg-image">
                            <?= get_the_post_thumbnail(get_the_ID(), 'large'); ?>
                        </div>
                        <div class="mgpg-content">
                            <h3><?php the_title(); ?></h3>
                            <?php if ($atts['taxonomy'] && $assigned) : ?>
                                <span class="mgpg-term"><?= esc_html($assigned[0]->name); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>

    <?php
    return ob_get_clean();
}

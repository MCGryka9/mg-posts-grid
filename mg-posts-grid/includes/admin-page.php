<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    add_menu_page(
        'MG Posts Grid',
        'MG Posts Grid',
        'manage_options',
        'mg-posts-grid',
        'mgpg_admin_page',
        'dashicons-screenoptions'
    );
});

function mgpg_admin_page() {
?>
<div class="wrap">
    <h1>MG Posts Grid</h1>

    <h2>Jak używać shortcode</h2>
    <pre>
[mg_posts
 post_type="project"
 taxonomy="project_category"
 terms=""
 columns_pc="4"
 columns_mobile="2"
 show_filter="1"
]
    </pre>

    <p><strong>Parametry:</strong></p>
    <ul>
        <li><code>post_type</code> – post, project lub dowolny CPT</li>
        <li><code>taxonomy</code> – np. project_category</li>
        <li><code>terms</code> – slug-i oddzielone przecinkiem lub puste</li>
        <li><code>columns_pc</code> – ilość kolumn na PC</li>
        <li><code>columns_mobile</code> – ilość kolumn na mobile</li>
        <li><code>show_filter</code> – 1 lub 0</li>
    </ul>
</div>
<?php
}

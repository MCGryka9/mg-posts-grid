<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function mg_posts_grid_add_admin_menu() {
    add_options_page(
        'MG Posts Grid - Instrukcja',
        'MG Posts Grid',
        'manage_options',
        'mg-posts-grid-info',
        'mg_posts_grid_admin_html'
    );
}
add_action( 'admin_menu', 'mg_posts_grid_add_admin_menu' );

function mg_posts_grid_admin_html() {
    ?>
    <div class="wrap">
        <h1>📦 MG Posts Grid - Zaawansowana obsługa</h1>
        
        <div class="card" style="max-width: 900px; padding: 20px;">
            <h2>📋 Tabela parametrów shortcode</h2>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 20%;">Parametr</th>
                        <th>Opis</th>
                        <th>Domyślnie</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>type</strong></td>
                        <td>Typ postu (slug), np. <code>post</code>, <code>product</code>, <code>portfolio_item</code>.</td>
                        <td><code>post</code></td>
                    </tr>
                    <tr>
                        <td><strong>count</strong></td>
                        <td>Liczba wyświetlanych elementów.</td>
                        <td><code>6</code></td>
                    </tr>
                    <tr>
                        <td><strong>category</strong></td>
                        <td>Slug kategorii (tylko dla standardowych wpisów).</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td><strong>taxonomy</strong></td>
                        <td>Nazwa customowej taksonomii (np. <code>product_cat</code>).</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td><strong>term</strong></td>
                        <td>Slug konkretnej kategorii wewnątrz <code>taxonomy</code>.</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card" style="max-width: 900px; margin-top: 20px; padding: 20px;">
            <h2>💡 Zaawansowane przykłady</h2>
            
            <p><strong>1. Wyświetlanie produktów z WooCommerce:</strong><br>
            <code>[mg_posts_grid type="product" count="4"]</code></p>
            
            <p><strong>2. Wyświetlanie projektów z taksonomii "typ-projektu" o nazwie "web-design":</strong><br>
            <code>[mg_posts_grid type="portfolio" taxonomy="typ-projektu" term="web-design"]</code></p>
            
            <p><strong>3. Standardowe wpisy z kategorii "Blog":</strong><br>
            <code>[mg_posts_grid count="3" category="blog"]</code></p>
        </div>
    </div>
    <?php
}
<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class ProductPostTypeServiceProvider extends SageServiceProvider
{
    public const POST_TYPE = 'product';

    public const TAXONOMY_CATEGORY = 'product_category';

    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        parent::boot();

        add_action('init', [$this, 'registerProductPostType']);
        add_action('init', [$this, 'registerProductCategoryTaxonomy']);
    }

    public function registerProductPostType(): void
    {
        $labels = [
            'name' => __('Producten', 'sage'),
            'singular_name' => __('Product', 'sage'),
            'add_new' => __('Nieuw product', 'sage'),
            'add_new_item' => __('Product toevoegen', 'sage'),
            'edit_item' => __('Product bewerken', 'sage'),
            'new_item' => __('Nieuw product', 'sage'),
            'view_item' => __('Product bekijken', 'sage'),
            'search_items' => __('Producten zoeken', 'sage'),
            'not_found' => __('Geen producten gevonden', 'sage'),
            'not_found_in_trash' => __('Geen producten in prullenbak', 'sage'),
            'menu_name' => __('Producten', 'sage'),
        ];

        register_post_type(self::POST_TYPE, [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'hierarchical' => false,
            'menu_position' => 22,
            'menu_icon' => 'dashicons-cart',
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'rewrite' => ['slug' => 'product', 'with_front' => false],
        ]);
    }

    public function registerProductCategoryTaxonomy(): void
    {
        $labels = [
            'name' => __('Productcategorieën', 'sage'),
            'singular_name' => __('Productcategorie', 'sage'),
            'search_items' => __('Categorieën zoeken', 'sage'),
            'all_items' => __('Alle categorieën', 'sage'),
            'parent_item' => __('Bovenliggende categorie', 'sage'),
            'parent_item_colon' => __('Bovenliggende categorie:', 'sage'),
            'edit_item' => __('Categorie bewerken', 'sage'),
            'update_item' => __('Categorie bijwerken', 'sage'),
            'add_new_item' => __('Nieuwe categorie', 'sage'),
            'new_item_name' => __('Nieuwe categorienaam', 'sage'),
            'menu_name' => __('Categorieën', 'sage'),
        ];

        register_taxonomy(self::TAXONOMY_CATEGORY, [self::POST_TYPE], [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'product-categorie', 'with_front' => false],
        ]);
    }
}

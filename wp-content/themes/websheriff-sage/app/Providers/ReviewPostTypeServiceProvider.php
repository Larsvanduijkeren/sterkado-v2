<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class ReviewPostTypeServiceProvider extends SageServiceProvider
{
    public const POST_TYPE = 'review';

    public const TAXONOMY_CATEGORY = 'review_category';

    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        parent::boot();

        add_action('init', [$this, 'registerReviewPostType']);
        add_action('init', [$this, 'registerReviewCategoryTaxonomy']);
    }

    public function registerReviewPostType(): void
    {
        $labels = [
            'name' => __('Reviews', 'sage'),
            'singular_name' => __('Review', 'sage'),
            'add_new' => __('Nieuwe review', 'sage'),
            'add_new_item' => __('Review toevoegen', 'sage'),
            'edit_item' => __('Review bewerken', 'sage'),
            'new_item' => __('Nieuwe review', 'sage'),
            'view_item' => __('Review bekijken', 'sage'),
            'search_items' => __('Reviews zoeken', 'sage'),
            'not_found' => __('Geen reviews gevonden', 'sage'),
            'not_found_in_trash' => __('Geen reviews in prullenbak', 'sage'),
            'menu_name' => __('Reviews', 'sage'),
        ];

        register_post_type(self::POST_TYPE, [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => false,
            'show_in_rest' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'hierarchical' => false,
            'menu_position' => 24,
            'menu_icon' => 'dashicons-star-filled',
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'rewrite' => ['slug' => 'review', 'with_front' => false],
        ]);
    }

    public function registerReviewCategoryTaxonomy(): void
    {
        $labels = [
            'name' => __('Reviewcategorieën', 'sage'),
            'singular_name' => __('Reviewcategorie', 'sage'),
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
            'show_in_nav_menus' => false,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'review-categorie', 'with_front' => false],
        ]);
    }
}

<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class VacancyPostTypeServiceProvider extends SageServiceProvider
{
    public const POST_TYPE = 'vacancy';

    public const TAXONOMY_CATEGORY = 'vacancy_category';

    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        parent::boot();

        add_action('init', [$this, 'registerVacancyPostType']);
        add_action('init', [$this, 'registerVacancyCategoryTaxonomy']);
        add_filter('use_block_editor_for_post_type', [$this, 'disableBlockEditorForVacancy'], 10, 2);
        add_filter('get_the_archive_title', [$this, 'filterArchiveTitle'], 10, 3);
    }

    public function registerVacancyPostType(): void
    {
        $labels = [
            'name' => __('Vacatures', 'sage'),
            'singular_name' => __('Vacature', 'sage'),
            'add_new' => __('Nieuwe vacature', 'sage'),
            'add_new_item' => __('Vacature toevoegen', 'sage'),
            'edit_item' => __('Vacature bewerken', 'sage'),
            'new_item' => __('Nieuwe vacature', 'sage'),
            'view_item' => __('Vacature bekijken', 'sage'),
            'search_items' => __('Vacatures zoeken', 'sage'),
            'not_found' => __('Geen vacatures gevonden', 'sage'),
            'not_found_in_trash' => __('Geen vacatures in prullenbak', 'sage'),
            'menu_name' => __('Vacatures', 'sage'),
            'archives' => __('Vacatures', 'sage'),
        ];

        register_post_type(self::POST_TYPE, [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'hierarchical' => false,
            'menu_position' => 23,
            'menu_icon' => 'dashicons-id',
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'rewrite' => ['slug' => 'vacatures', 'with_front' => false],
        ]);
    }

    public function registerVacancyCategoryTaxonomy(): void
    {
        $labels = [
            'name' => __('Vacaturecategorieën', 'sage'),
            'singular_name' => __('Vacaturecategorie', 'sage'),
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
            'rewrite' => ['slug' => 'vacature-categorie', 'with_front' => false],
        ]);
    }

    public function disableBlockEditorForVacancy(bool $use, string $postType): bool
    {
        if ($postType === self::POST_TYPE) {
            return false;
        }

        return $use;
    }

    public function filterArchiveTitle(string $title, string $_originalTitle = '', mixed $_term = null): string
    {
        if (is_post_type_archive(self::POST_TYPE)) {
            return __('Vacatures', 'sage');
        }

        return $title;
    }
}

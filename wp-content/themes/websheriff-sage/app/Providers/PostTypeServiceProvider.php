<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class PostTypeServiceProvider extends SageServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        add_action('init', [$this, 'registerPostTypes'], 0);
    }

    public function registerPostTypes(): void
    {
        $this->registerQuestionPostType();
    }

    protected function registerQuestionPostType(): void
    {
        register_post_type('question', [
            'labels'              => [
                'name'          => __('Questions', 'sage'),
                'singular_name' => __('Question', 'sage'),
            ],
            'description'         => __('Questions', 'sage'),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'publicly_queryable'  => false,
            'exclude_from_search' => false,
            'has_archive'         => true,
            'show_in_rest'        => false,
            'menu_icon'           => 'dashicons-editor-help',
            'menu_position'       => 8,
            'supports'            => ['title', 'editor', 'revisions'],
            'capability_type'     => 'page',
            'map_meta_cap'        => true,
            'can_export'          => false,
        ]);
    }
}

<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class TaxonomyServiceProvider extends SageServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        add_action('init', [$this, 'registerTaxonomies'], 0);
    }

    public function registerTaxonomies(): void
    {
        $this->registerVacancyCategoryTaxonomy();
        $this->registerQuestionCategoryTaxonomy();
    }

    /**
     * Kept for vacancy archive/selection blocks and synced terms; vacancy CPT is not registered.
     */
    protected function registerVacancyCategoryTaxonomy(): void
    {
        register_taxonomy('vacancy_category', [], [
            'labels'            => [
                'name'          => __('Vacancy categories', 'sage'),
                'singular_name' => __('Vacancy category', 'sage'),
            ],
            'hierarchical'      => true,
            'show_ui'           => false,
            'show_admin_column' => false,
            'public'            => false,
            'rewrite'           => false,
            'query_var'         => false,
            'show_in_rest'      => false,
        ]);
    }

    protected function registerQuestionCategoryTaxonomy(): void
    {
        register_taxonomy('question_category', ['question'], [
            'labels'            => [
                'name'          => __('Categories', 'sage'),
                'singular_name' => __('Category', 'sage'),
            ],
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'public'            => false,
            'rewrite'           => false,
            'query_var'         => false,
            'show_in_rest'      => true,
        ]);
    }
}

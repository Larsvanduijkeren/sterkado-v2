<?php

namespace App\View\Composers;

use App\Providers\VacancyPostTypeServiceProvider;
use Roots\Acorn\View\Composer;

class SingleVacancy extends Composer
{
    /**
     * @var array<int, string>
     */
    protected static $views = [
        'partials.single-vacancy',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function with(): array
    {
        $post = get_post();
        if (! $post instanceof \WP_Post || $post->post_type !== VacancyPostTypeServiceProvider::POST_TYPE) {
            return [
                'vacancy_related_posts' => [],
            ];
        }

        $related = [];
        $terms = get_the_terms($post->ID, VacancyPostTypeServiceProvider::TAXONOMY_CATEGORY);
        if (is_array($terms)) {
            $termId = 0;
            foreach ($terms as $term) {
                if ($term instanceof \WP_Term) {
                    $termId = (int) $term->term_id;
                    break;
                }
            }
            if ($termId > 0) {
                $related = get_posts([
                    'post_type' => VacancyPostTypeServiceProvider::POST_TYPE,
                    'post_status' => 'publish',
                    'posts_per_page' => 6,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post__not_in' => [(int) $post->ID],
                    'tax_query' => [
                        [
                            'taxonomy' => VacancyPostTypeServiceProvider::TAXONOMY_CATEGORY,
                            'field' => 'term_id',
                            'terms' => $termId,
                        ],
                    ],
                    'no_found_rows' => true,
                ]);
                if ($related !== [] && function_exists('update_post_caches')) {
                    update_post_caches($related);
                }
            }
        }

        return [
            'vacancy_related_posts' => $related,
        ];
    }
}

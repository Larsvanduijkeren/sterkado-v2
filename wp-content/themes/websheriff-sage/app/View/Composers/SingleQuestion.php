<?php

namespace App\View\Composers;

use App\Providers\QuestionPostTypeServiceProvider;
use Roots\Acorn\View\Composer;

class SingleQuestion extends Composer
{
    /**
     * @var array<int, string>
     */
    protected static $views = [
        'partials.single-question',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function with(): array
    {
        $post = get_post();
        if (! $post instanceof \WP_Post || $post->post_type !== QuestionPostTypeServiceProvider::POST_TYPE) {
            return [
                'question_sidebar_cards' => [],
                'question_sidebar_icon_class' => 'fa-solid fa-gift',
                'question_option_phone' => null,
            ];
        }

        $rawTerms = get_the_terms($post->ID, QuestionPostTypeServiceProvider::TAXONOMY_CATEGORY);
        if (! is_array($rawTerms)) {
            $rawTerms = [];
        }
        usort($rawTerms, static function ($a, $b): int {
            if (! $a instanceof \WP_Term || ! $b instanceof \WP_Term) {
                return 0;
            }

            return strcasecmp($a->name, $b->name);
        });

        $cards = [];
        foreach ($rawTerms as $term) {
            if (! $term instanceof \WP_Term) {
                continue;
            }
            $posts = get_posts([
                'post_type' => QuestionPostTypeServiceProvider::POST_TYPE,
                'post_status' => 'publish',
                'posts_per_page' => 12,
                'orderby' => 'title',
                'order' => 'ASC',
                'post__not_in' => [(int) $post->ID],
                'tax_query' => [
                    [
                        'taxonomy' => QuestionPostTypeServiceProvider::TAXONOMY_CATEGORY,
                        'field' => 'term_id',
                        'terms' => (int) $term->term_id,
                    ],
                ],
                'no_found_rows' => true,
            ]);
            if ($posts !== [] && function_exists('update_post_caches')) {
                update_post_caches($posts);
            }
            $cards[] = [
                'term' => $term,
                'posts' => $posts,
            ];
        }

        $phone = null;
        if (function_exists('get_field')) {
            $phoneRaw = get_field('phone', 'option');
            $phone = is_string($phoneRaw) ? trim($phoneRaw) : '';
            $phone = $phone !== '' ? $phone : null;
        }

        return [
            'question_sidebar_cards' => $cards,
            'question_sidebar_icon_class' => 'fa-solid fa-gift',
            'question_option_phone' => $phone,
        ];
    }
}

<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class SinglePostRelated extends Composer
{
    /**
     * @var array<int, string>
     */
    protected static $views = [
        'partials.single-post',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function with(): array
    {
        $post = get_post();
        if (! $post instanceof \WP_Post || $post->post_type !== 'post') {
            return [
                'related_posts' => [],
            ];
        }

        return [
            'related_posts' => \App\related_posts_for_article($post, 8),
        ];
    }
}

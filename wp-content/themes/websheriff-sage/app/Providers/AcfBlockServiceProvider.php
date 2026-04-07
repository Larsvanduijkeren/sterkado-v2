<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class AcfBlockServiceProvider extends SageServiceProvider
{
    /**
     * Slug => Title
     */
    protected array $blocks = [
        'home-hero'     => 'Home Hero',
        'hero'          => 'Hero',
        'text'          => 'Text',
        'text-images'   => 'Text Images',
        'content-tabs'  => 'Content Tabs',
        'content-cards' => 'Content Cards',
        'vacancy-selection' => 'Vacancy Selection',
        'vacancy-archive'   => 'Vacancy Archive',
        'faq'           => 'FAQ',
        'partners'      => 'Partners',
        'contact'       => 'Contact',
    ];

    public function boot(): void
    {
        parent::boot();

        add_filter('block_categories_all', [$this, 'addBlockCategory'], 10, 2);
        add_action('acf/init', [$this, 'registerBlocks']);
        add_filter('allowed_block_types_all', [$this, 'allowedBlocks'], 10, 2);
    }

    public function addBlockCategory(array $categories, $post): array
    {
        foreach ($categories as $cat) {
            if (($cat['slug'] ?? null) === 'websheriff') {
                return $categories;
            }
        }

        $categories[] = [
            'slug'  => 'websheriff',
            'title' => __('Websheriff blocks', 'websheriff'),
        ];

        return $categories;
    }

    public function registerBlocks(): void
    {
        if (!function_exists('acf_register_block_type')) {
            return;
        }

        foreach ($this->blocks as $name => $title) {
            acf_register_block_type([
                'name'            => $name,
                'title'           => __($title, 'websheriff'),
                'category'        => 'websheriff',
                'icon'            => 'editor-code',
                'mode'            => 'auto',
                'supports'        => [
                    'anchor' => true,
                ],
                'render_callback' => [$this, 'renderBlock'],
            ]);
        }
    }

    /**
     * Render callback for all ACF blocks.
     */
    public function renderBlock(array $block, string $content = '', bool $isPreview = false, int $postId = 0): void
    {
        $slug = str_replace('acf/', '', $block['name'] ?? '');
        $view = "components.blocks.{$slug}";

        $fields = $this->getBlockFields($block);

        $data = [
            'block'      => $block,
            'fields'     => $fields,
            'is_preview' => $isPreview,
            'post_id'    => $postId,
            'slug'       => $slug,
            'content'    => $content,
        ];

        $data = array_merge($data, $this->prepareBlockData($slug, $fields, $block, $postId));

        if (function_exists('view') && view()->exists($view)) {
            echo view($view, $data)->render();

            return;
        }

        echo "<!-- Missing block view: {$view} -->";
    }

    /**
     * Prepare block-specific data (queries, preloaded ACF). Avoids N+1 and keeps Blade presentational.
     */
    protected function prepareBlockData(string $slug, array $fields, array $block, int $postId): array
    {
        switch ($slug) {
            case 'faq':
                return $this->prepareFaqData($fields);
            case 'vacancy-selection':
                return $this->prepareVacancySelectionData($fields);
            case 'vacancy-archive':
                return $this->prepareVacancyArchiveData($fields);
            default:
                return [];
        }
    }

    protected function prepareFaqData(array $fields): array
    {
        $termIds = $this->normalizeTermIds($fields['category_selection'] ?? null);
        $args = [
            'post_type'      => 'question',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        ];
        if (!empty($termIds)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'question_category',
                    'field'    => 'term_id',
                    'terms'    => $termIds,
                ],
            ];
        }
        $posts = get_posts($args);
        if (!empty($posts) && function_exists('update_post_caches')) {
            update_post_caches($posts);
        }

        return ['questions' => $posts];
    }

    protected function prepareVacancySelectionData(array $fields): array
    {
        $termIds = $this->normalizeTermIds($fields['category_selection'] ?? null);
        $args = [
            'post_type'      => 'vacancy',
            'posts_per_page' => 6,
            'orderby'        => 'menu_order date',
            'order'          => 'DESC',
        ];
        if (!empty($termIds)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'vacancy_category',
                    'field'    => 'term_id',
                    'terms'    => $termIds,
                ],
            ];
        }
        $posts = get_posts($args);
        if (!empty($posts) && function_exists('update_post_caches')) {
            update_post_caches($posts);
        }
        $posts = $this->sortPostsWithImageBadgeFirst($posts);

        return ['vacancies' => $posts];
    }

    protected function prepareVacancyArchiveData(array $fields): array
    {
        $args = [
            'post_type'      => 'vacancy',
            'posts_per_page' => 9,
            'paged'          => max(1, (int) get_query_var('paged') ?: (int) get_query_var('page') ?: 1),
            'orderby'        => 'menu_order date',
            'order'          => 'DESC',
        ];
        $query = new \WP_Query($args);
        if (!empty($query->posts)) {
            $query->posts = $this->sortPostsWithImageBadgeFirst($query->posts);
        }

        return ['query' => $query];
    }

    /**
     * Items with a non-empty card image badge (ACF field "badge") first; original query order kept within each group.
     *
     * @param  array<int, \WP_Post>  $posts
     * @return array<int, \WP_Post>
     */
    protected function sortPostsWithImageBadgeFirst(array $posts): array
    {
        if (count($posts) < 2) {
            return $posts;
        }
        $rows = [];
        foreach ($posts as $index => $post) {
            $badge = function_exists('get_field') ? get_field('badge', $post->ID) : null;
            $hasBadge = $badge !== null && $badge !== '' && $badge !== false;
            $rows[] = [
                'post'        => $post,
                'badge_group' => $hasBadge ? 0 : 1,
                'index'       => $index,
            ];
        }
        usort($rows, static function (array $a, array $b): int {
            if ($a['badge_group'] !== $b['badge_group']) {
                return $a['badge_group'] <=> $b['badge_group'];
            }

            return $a['index'] <=> $b['index'];
        });

        return array_map(static fn (array $row): \WP_Post => $row['post'], $rows);
    }

    /** @return array<int> */
    protected function normalizeTermIds($value): array
    {
        if (is_array($value)) {
            return array_map('intval', $value);
        }
        if (is_numeric($value)) {
            return [(int) $value];
        }

        return [];
    }

    /**
     * Best-effort block field retrieval.
     */
    protected function getBlockFields(array $block): array
    {
        $blockId = $block['id'] ?? null;

        if ($blockId && function_exists('get_fields')) {
            $scoped = get_fields($blockId);
            if (is_array($scoped)) {
                return $scoped;
            }
        }

        if (function_exists('get_fields')) {
            $fallback = get_fields();

            return is_array($fallback) ? $fallback : [];
        }

        return [];
    }

    public function allowedBlocks($allowed, $context): array
    {
        $postType = $context->post->post_type ?? null;

        if (in_array($postType, ['post', 'job'], true)) {
            return [
                'core/paragraph',
                'core/heading',
                'core/list',
                'core/list-item',
                'core/image',
                'acf/content-cards',
            ];
        }

        $acfBlocks = array_map(fn ($slug) => "acf/{$slug}", array_keys($this->blocks));

        return array_values(array_unique($acfBlocks));
    }
}

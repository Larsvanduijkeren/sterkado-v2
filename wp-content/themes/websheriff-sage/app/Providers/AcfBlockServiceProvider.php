<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class AcfBlockServiceProvider extends SageServiceProvider
{
    /**
     * Slug => Title
     */
    protected array $blocks = [
        'hero'        => 'Hero',
        'logos'       => 'Logos',
        'text'        => 'Text',
        'text-images'   => 'Text Images',
        'request-quote' => 'Request quote',
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
        if (! function_exists('acf_register_block_type')) {
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

        if (function_exists('view') && view()->exists($view)) {
            echo view($view, $data)->render();

            return;
        }

        echo "<!-- Missing block view: {$view} -->";
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

        if ($postType === 'post') {
            return [
                'core/paragraph',
                'core/heading',
                'core/list',
                'core/list-item',
                'core/image',
            ];
        }

        $acfBlocks = array_map(fn ($slug) => "acf/{$slug}", array_keys($this->blocks));

        return array_values(array_unique($acfBlocks));
    }
}

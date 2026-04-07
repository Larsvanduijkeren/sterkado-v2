<?php
    
    namespace App;
    
    /**
     * Estimate reading time (Dutch label) for a post.
     */
    function reading_time_label(\WP_Post $post, int $wpm = 220): string
    {
        $content = $post->post_content ?? '';
        
        // Expand blocks/shortcodes and remove markup
        $content = do_shortcode($content);
        $content = wp_strip_all_tags($content);
        
        // Count words (simple + good enough for NL)
        $words = str_word_count($content);
        
        $minutes = (int) max(1, ceil($words / max(1, $wpm)));
        
        return "{$minutes} min leestijd";
    }

    /**
     * Footer modifier classes derived from the last ACF block on the page (background + waves),
     * e.g. footer--after-bg-grey has-grey-waves
     */
    function last_section_footer_modifiers(?\WP_Post $post = null): string
    {
        $post ??= get_queried_object();
        if (! $post instanceof \WP_Post) {
            return '';
        }

        $content = $post->post_content ?? '';
        if ($content === '') {
            return '';
        }

        $lastAcfBlock = null;
        $walk = static function (array $blocks) use (&$walk, &$lastAcfBlock): void {
            foreach ($blocks as $block) {
                $name = $block['blockName'] ?? '';
                if ($name !== '' && str_starts_with($name, 'acf/')) {
                    $lastAcfBlock = $block;
                }
                if (! empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
                    $walk($block['innerBlocks']);
                }
            }
        };
        $walk(parse_blocks($content));

        if ($lastAcfBlock === null) {
            return '';
        }

        $blockName = $lastAcfBlock['blockName'] ?? '';
        $attrs = is_array($lastAcfBlock['attrs'] ?? null) ? $lastAcfBlock['attrs'] : [];
        $data = is_array($attrs['data'] ?? null) ? $attrs['data'] : [];

        // Hero block has no shared Blocks field group; match template (bg-white, no configurable waves).
        if ($blockName === 'acf/hero') {
            $bg = 'white';
            $waves = false;
        } else {
            $bg = $data['background_color'] ?? $data['field_69a6f8b242ed7'] ?? null;
            $wavesRaw = $data['add_waves'] ?? $data['field_69a6f8f042ed9'] ?? false;
            $waves = filter_var($wavesRaw, FILTER_VALIDATE_BOOLEAN)
                || $wavesRaw === 1
                || $wavesRaw === '1';
        }

        $allowed = ['white', 'red', 'sand', 'grey'];
        if (! is_string($bg) || ! in_array($bg, $allowed, true)) {
            $bg = 'white';
        }

        $classes = [
            'footer--after-bg-' . $bg,
        ];

        if ($waves) {
            $classes[] = 'has-' . $bg . '-waves';
            $classes[] = 'footer--after-waves';
        }

        return implode(' ', $classes);
    }

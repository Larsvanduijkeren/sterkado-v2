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
 * Social links from ACF Options (same source as the footer).
 *
 * @return list<array{platform: string, url: string, target: string, aria_label: string, icon_class: string}>
 */
function social_links_from_options(): array
{
    if (! function_exists('get_field')) {
        return [];
    }
    $rows = get_field('social_links', 'option');
    if (! is_array($rows)) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        if (! is_array($row)) {
            continue;
        }
        $platform = isset($row['platform']) ? (string) $row['platform'] : 'other';
        $link = $row['link'] ?? null;
        if (! is_array($link) || empty($link['url'])) {
            continue;
        }
        $url = (string) $link['url'];
        $target = ! empty($link['target']) ? (string) $link['target'] : '_blank';
        $title = isset($link['title']) ? trim((string) $link['title']) : '';
        $aria = $title !== '' ? $title : social_link_platform_aria_label($platform);
        $out[] = [
            'platform' => $platform,
            'url' => $url,
            'target' => $target,
            'aria_label' => $aria,
            'icon_class' => social_link_platform_icon_class($platform),
        ];
    }

    return $out;
}

function social_link_platform_aria_label(string $platform): string
{
    return match ($platform) {
        'facebook' => __('Facebook', 'sage'),
        'instagram' => __('Instagram', 'sage'),
        'linkedin' => __('LinkedIn', 'sage'),
        'youtube' => __('YouTube', 'sage'),
        'tiktok' => __('TikTok', 'sage'),
        'x_twitter' => __('X', 'sage'),
        default => __('Social link', 'sage'),
    };
}

function social_link_platform_icon_class(string $platform): string
{
    return match ($platform) {
        'facebook' => 'fa-brands fa-facebook-f',
        'instagram' => 'fa-brands fa-instagram',
        'linkedin' => 'fa-brands fa-linkedin-in',
        'youtube' => 'fa-brands fa-youtube',
        'tiktok' => 'fa-brands fa-tiktok',
        'x_twitter' => 'fa-brands fa-x-twitter',
        default => 'fa-solid fa-link',
    };
}

/**
 * Allow iframe embeds (e.g. Google Maps) from trusted editor content.
 */
function office_map_sanitize_embed(string $html): string
{
    $allowed = wp_kses_allowed_html('post');
    $allowed['iframe'] = [
        'src' => true,
        'width' => true,
        'height' => true,
        'style' => true,
        'title' => true,
        'allow' => true,
        'allowfullscreen' => true,
        'loading' => true,
        'referrerpolicy' => true,
        'class' => true,
        'id' => true,
        'frameborder' => true,
    ];

    return wp_kses($html, $allowed);
}

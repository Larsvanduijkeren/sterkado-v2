<?php

namespace App\View\Composers;

use function App\last_section_footer_modifiers;
use Roots\Acorn\View\Composer;

class Footer extends Composer
{
    /**
     * @var array<int, string>
     */
    protected static $views = [
        'sections.footer',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function with(): array
    {
        $headingHtml = $this->footerHeadingHtml();
        $displayLogo = $this->footerDisplayLogo();

        return [
            'footerAfterSectionClass' => $this->footerAfterSectionClass(),
            'footerHeadingHtml'       => $headingHtml,
            'footerDisplayLogo'       => $displayLogo,
            'footerNavColumns'        => $this->footerNavColumns(),
            'footerSocialRows'        => $this->footerSocialRows(),
            'footerCreditsRaw'        => $this->footerCreditsRaw(),
            'footerShowCredits'       => trim(wp_strip_all_tags($this->footerCreditsRaw())) !== '',
            'footerShowTopRow'        => $headingHtml !== '' || $displayLogo !== null,
        ];
    }

    /**
     * Classes reflecting the last section’s background / waves (singular content only).
     */
    protected function footerAfterSectionClass(): string
    {
        if (! is_singular()) {
            return '';
        }

        $post = get_queried_object();
        if (! $post instanceof \WP_Post) {
            return '';
        }

        return last_section_footer_modifiers($post);
    }

    protected function footerHeadingHtml(): string
    {
        if (! function_exists('get_field')) {
            return '';
        }
        $v = get_field('footer_heading', 'option');
        if (! is_string($v)) {
            return '';
        }
        $trimmed = trim($v);
        if ($trimmed === '') {
            return '';
        }

        return wp_kses($trimmed, [
            'span'   => [
                'class' => true,
            ],
            'br'     => [],
            'em'     => [],
            'strong' => [],
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function footerDisplayLogo(): ?array
    {
        if (! function_exists('get_field')) {
            return null;
        }
        $footerLogo = get_field('footer_logo', 'option');
        if (is_array($footerLogo) && ! empty($footerLogo['url'] ?? $footerLogo['ID'] ?? null)) {
            return $footerLogo;
        }
        $logo = get_field('logo', 'option');

        return is_array($logo) && ! empty($logo['url'] ?? $logo['ID'] ?? null) ? $logo : null;
    }

    /**
     * @return list<array{title: string, html: string}>
     */
    protected function footerNavColumns(): array
    {
        $out = [];
        for ($i = 1; $i <= 5; $i++) {
            $title = '';
            if (function_exists('get_field')) {
                $v = get_field("footer_nav_{$i}_title", 'option');
                $title = is_string($v) ? trim($v) : '';
            }
            $html = $this->footerNavMenuHtml("footer-nav-{$i}");
            $out[] = [
                'title' => $title,
                'html'  => $html,
            ];
        }

        return $out;
    }

    /**
     * @return list<array{platform: string, url: string, target: string, aria_label: string, icon_class: string}>
     */
    protected function footerSocialRows(): array
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
            $aria = $title !== '' ? $title : $this->socialPlatformAriaLabel($platform);
            $out[] = [
                'platform'   => $platform,
                'url'        => $url,
                'target'     => $target,
                'aria_label' => $aria,
                'icon_class' => $this->socialPlatformIconClass($platform),
            ];
        }

        return $out;
    }

    protected function footerCreditsRaw(): string
    {
        if (! function_exists('get_field')) {
            return '';
        }
        $credits = get_field('footer_credits', 'option');

        return is_string($credits) ? $credits : '';
    }

    protected function footerNavMenuHtml(string $themeLocation): string
    {
        if (! has_nav_menu($themeLocation)) {
            return '';
        }
        $html = wp_nav_menu([
            'theme_location' => $themeLocation,
            'echo'           => false,
            'container'      => false,
            'menu_class'     => 'link-list',
            'fallback_cb'    => false,
            'depth'          => 2,
        ]);

        return is_string($html) ? $html : '';
    }

    protected function socialPlatformAriaLabel(string $platform): string
    {
        return match ($platform) {
            'facebook'  => __('Facebook', 'sage'),
            'instagram' => __('Instagram', 'sage'),
            'linkedin'  => __('LinkedIn', 'sage'),
            'youtube'   => __('YouTube', 'sage'),
            'tiktok'    => __('TikTok', 'sage'),
            'x_twitter' => __('X', 'sage'),
            default     => __('Social link', 'sage'),
        };
    }

    protected function socialPlatformIconClass(string $platform): string
    {
        return match ($platform) {
            'facebook'  => 'fa-brands fa-facebook-f',
            'instagram' => 'fa-brands fa-instagram',
            'linkedin'  => 'fa-brands fa-linkedin-in',
            'youtube'   => 'fa-brands fa-youtube',
            'tiktok'    => 'fa-brands fa-tiktok',
            'x_twitter' => 'fa-brands fa-x-twitter',
            default     => 'fa-solid fa-link',
        };
    }
}

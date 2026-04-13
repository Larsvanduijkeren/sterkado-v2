<?php

namespace App\View\Composers;

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
        $socialRows = $this->footerSocialRows();
        $copyrightText = $this->footerCopyrightText();
        $legalMenuHtml = $this->footerLegalMenuHtml();
        $showBottomMeta = $copyrightText !== '' || trim(wp_strip_all_tags($legalMenuHtml)) !== '';

        return [
            'footerAfterSectionClass' => $this->footerAfterSectionClass(),
            'footerHeadingHtml' => $headingHtml,
            'footerDisplayLogo' => $displayLogo,
            'footerNavColumns' => $this->footerNavColumns(),
            'footerSocialRows' => $socialRows,
            'footerCopyrightText' => $copyrightText,
            'footerLegalMenuHtml' => $legalMenuHtml,
            'footerShowBottomMeta' => $showBottomMeta,
            'footerShowBottomRow' => $showBottomMeta || $socialRows !== [],
            'footerCreditsRaw' => $this->footerCreditsRaw(),
            'footerShowCredits' => trim(wp_strip_all_tags($this->footerCreditsRaw())) !== '',
            'footerShowTopRow' => $headingHtml !== '' || $displayLogo !== null,
        ];
    }

    protected function footerAfterSectionClass(): string
    {
        return '';
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
            'span' => [
                'class' => true,
            ],
            'br' => [],
            'em' => [],
            'strong' => [],
        ]);
    }

    /**
     * @return array<string, mixed>|null  Normalized image data with a usable `url` for the img src attribute
     */
    protected function footerDisplayLogo(): ?array
    {
        $candidates = [];
        if (function_exists('get_field')) {
            $candidates[] = get_field('footer_logo', 'option');
            $candidates[] = get_field('logo', 'option');
        }
        foreach ($candidates as $raw) {
            $normalized = $this->normalizeImageForFooter($raw);
            if ($normalized !== null) {
                return $normalized;
            }
        }

        $customLogoId = (int) get_theme_mod('custom_logo', 0);
        if ($customLogoId > 0) {
            return $this->normalizeImageForFooter($customLogoId);
        }

        return null;
    }

    /**
     * @param  mixed  $raw  ACF image (array), attachment ID, or absolute URL string
     * @return array<string, mixed>|null
     */
    protected function normalizeImageForFooter(mixed $raw): ?array
    {
        if ($raw === null || $raw === false || $raw === '') {
            return null;
        }

        if (is_numeric($raw)) {
            $id = (int) $raw;
            if ($id <= 0) {
                return null;
            }
            $url = wp_get_attachment_image_url($id, 'medium')
                ?: wp_get_attachment_image_url($id, 'large')
                ?: wp_get_attachment_image_url($id, 'full');
            if ($url === false || $url === '') {
                return null;
            }
            $alt = (string) get_post_meta($id, '_wp_attachment_image_alt', true);

            return [
                'ID' => $id,
                'url' => $url,
                'alt' => $alt,
                'sizes' => [
                    'medium' => $url,
                    'large' => wp_get_attachment_image_url($id, 'large') ?: $url,
                ],
            ];
        }

        if (is_string($raw)) {
            $trim = trim($raw);
            if ($trim === '' || filter_var($trim, FILTER_VALIDATE_URL) === false) {
                return null;
            }

            return [
                'url' => $trim,
                'alt' => '',
                'sizes' => [
                    'medium' => $trim,
                    'large' => $trim,
                ],
            ];
        }

        if (! is_array($raw)) {
            return null;
        }

        if (! empty($raw['url']) && is_string($raw['url'])) {
            return $raw;
        }

        $id = isset($raw['ID']) ? (int) $raw['ID'] : 0;
        if ($id <= 0) {
            return null;
        }

        $url = wp_get_attachment_image_url($id, 'medium')
            ?: wp_get_attachment_image_url($id, 'large')
            ?: wp_get_attachment_image_url($id, 'full');
        if ($url === false || $url === '') {
            return null;
        }

        return array_merge($raw, [
            'url' => $url,
            'sizes' => array_merge(
                is_array($raw['sizes'] ?? null) ? $raw['sizes'] : [],
                [
                    'medium' => wp_get_attachment_image_url($id, 'medium') ?: $url,
                    'large' => wp_get_attachment_image_url($id, 'large') ?: $url,
                ]
            ),
        ]);
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
            $html = $this->footerNavMenuHtml("footer-nav-{$i}", 'link-list', 2);
            $out[] = [
                'title' => $title,
                'html' => $html,
            ];
        }

        return $out;
    }

    protected function footerCopyrightText(): string
    {
        if (! function_exists('get_field')) {
            return '';
        }
        $v = get_field('footer_copyright', 'option');

        return is_string($v) ? trim($v) : '';
    }

    protected function footerLegalMenuHtml(): string
    {
        return $this->footerNavMenuHtml('copyright-nav', 'footer__legal-menu', 1);
    }

    /**
     * @return list<array{platform: string, url: string, target: string, aria_label: string, icon_class: string}>
     */
    protected function footerSocialRows(): array
    {
        return \App\social_links_from_options();
    }

    protected function footerCreditsRaw(): string
    {
        if (! function_exists('get_field')) {
            return '';
        }
        $credits = get_field('footer_credits', 'option');

        return is_string($credits) ? $credits : '';
    }

    protected function footerNavMenuHtml(string $themeLocation, string $menuClass = 'link-list', int $depth = 2): string
    {
        if (! has_nav_menu($themeLocation)) {
            return '';
        }
        $html = wp_nav_menu([
            'theme_location' => $themeLocation,
            'echo' => false,
            'container' => false,
            'menu_class' => $menuClass,
            'fallback_cb' => false,
            'depth' => $depth,
        ]);

        return is_string($html) ? $html : '';
    }
}

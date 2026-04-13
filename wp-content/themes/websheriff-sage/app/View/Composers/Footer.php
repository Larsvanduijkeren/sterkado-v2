<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

use function App\last_section_footer_modifiers;

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
            'span' => [
                'class' => true,
            ],
            'br' => [],
            'em' => [],
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

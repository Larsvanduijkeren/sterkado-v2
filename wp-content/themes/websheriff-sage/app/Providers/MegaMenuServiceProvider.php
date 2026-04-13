<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class MegaMenuServiceProvider extends SageServiceProvider
{
    /**
     * @var array<string, array<string, mixed>>|null
     */
    protected static ?array $megaMap = null;

    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        parent::boot();

        add_filter('wp_nav_menu_args', [$this, 'injectMegaConfigsIntoMainNav']);
        add_filter('nav_menu_css_class', [$this, 'addMegaMenuItemClasses'], 10, 4);
        add_filter('walker_nav_menu_start_el', [$this, 'appendMegaPanelHtml'], 10, 4);
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    public function injectMegaConfigsIntoMainNav(array $args): array
    {
        if (($args['theme_location'] ?? null) !== 'header-main-nav') {
            return $args;
        }

        $args['mega_configs'] = $this->getMegaMenuMap();

        return $args;
    }

    /**
     * @param  string[]  $classes
     * @return string[]
     */
    public function addMegaMenuItemClasses(array $classes, $menu_item, $args = null, int $depth = 0): array
    {
        if ($depth !== 0 || ! $menu_item instanceof \WP_Post) {
            return $classes;
        }

        $map = null;
        if (is_object($args)) {
            $map = $args->mega_configs ?? null;
        }
        if (! is_array($map) || $map === []) {
            return $classes;
        }

        $itemClasses = array_filter((array) $menu_item->classes);
        foreach (array_keys($map) as $trigger) {
            if ($trigger !== '' && in_array($trigger, $itemClasses, true)) {
                $classes[] = 'has-mega-panel';

                break;
            }
        }

        return $classes;
    }

    /**
     * @param  \stdClass|null  $args
     */
    public function appendMegaPanelHtml(string $item_output, $menu_item, int $depth, $args = null): string
    {
        if ($depth !== 0 || ! $menu_item instanceof \WP_Post) {
            return $item_output;
        }

        $map = null;
        if (is_object($args)) {
            $map = $args->mega_configs ?? null;
        }
        if (! is_array($map) || $map === []) {
            return $item_output;
        }

        $itemClasses = array_filter((array) $menu_item->classes);
        foreach ($map as $trigger => $config) {
            if ($trigger === '' || ! in_array($trigger, $itemClasses, true)) {
                continue;
            }

            if (function_exists('view') && view()->exists('partials.header-mega-menu')) {
                $item_output .= view('partials.header-mega-menu', [
                    'mega' => $config,
                    'trigger' => $trigger,
                ])->render();
            }

            break;
        }

        return $item_output;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function getMegaMenuMap(): array
    {
        if (self::$megaMap !== null) {
            return self::$megaMap;
        }

        if (! function_exists('get_field')) {
            self::$megaMap = [];

            return self::$megaMap;
        }

        $rows = get_field('header_mega_menus', 'option');
        if (! is_array($rows) || $rows === []) {
            self::$megaMap = [];

            return self::$megaMap;
        }

        $map = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $trigger = isset($row['trigger_class']) ? sanitize_html_class((string) $row['trigger_class']) : '';
            if ($trigger === '') {
                continue;
            }
            $layout = isset($row['mega_layout']) ? (string) $row['mega_layout'] : '';
            if (! in_array($layout, ['card_grid', 'featured_links'], true)) {
                continue;
            }

            $map[$trigger] = $this->normalizeMegaRow($row, $layout);
        }

        self::$megaMap = $map;

        return self::$megaMap;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function normalizeMegaRow(array $row, string $layout): array
    {
        $base = [
            'layout' => $layout,
        ];

        if ($layout === 'card_grid') {
            $cardsRaw = $row['grid_cards'] ?? null;
            $cardsRaw = is_array($cardsRaw) ? $cardsRaw : [];
            $cards = [];
            foreach ($cardsRaw as $card) {
                if (! is_array($card)) {
                    continue;
                }
                $img = $card['card_image'] ?? null;
                if (! is_array($img) || (empty($img['ID']) && empty($img['url']))) {
                    continue;
                }
                $link = $card['card_link'] ?? null;
                $link = is_array($link) ? $link : [];
                $cards[] = [
                    'image' => $img,
                    'title' => isset($card['card_title']) ? trim((string) $card['card_title']) : '',
                    'url' => isset($link['url']) ? trim((string) $link['url']) : '',
                    'link_title' => isset($link['title']) ? trim((string) $link['title']) : '',
                    'target' => isset($link['target']) ? (string) $link['target'] : '_self',
                ];
            }

            return $base + [
                'intro_title' => isset($row['grid_intro_title']) ? trim((string) $row['grid_intro_title']) : '',
                'intro_text' => isset($row['grid_intro_text']) ? trim((string) $row['grid_intro_text']) : '',
                'section_title' => isset($row['grid_section_title']) ? trim((string) $row['grid_section_title']) : '',
                'cards' => $cards,
            ];
        }

        $socialRaw = $row['feat_social'] ?? null;
        $socialRaw = is_array($socialRaw) ? $socialRaw : [];
        $social = [];
        foreach ($socialRaw as $s) {
            if (! is_array($s)) {
                continue;
            }
            $link = $s['social_link'] ?? null;
            $link = is_array($link) ? $link : [];
            $href = isset($link['url']) ? trim((string) $link['url']) : '';
            if ($href === '') {
                continue;
            }
            $social[] = [
                'url' => $href,
                'title' => isset($link['title']) ? trim((string) $link['title']) : '',
                'target' => isset($link['target']) ? (string) $link['target'] : '_self',
                'icon' => isset($s['social_icon']) ? (string) $s['social_icon'] : 'linkedin',
            ];
        }

        $linksRaw = $row['feat_column_links'] ?? null;
        $linksRaw = is_array($linksRaw) ? $linksRaw : [];
        $columnLinks = [];
        foreach ($linksRaw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $link = $item['link'] ?? null;
            $link = is_array($link) ? $link : [];
            $href = isset($link['url']) ? trim((string) $link['url']) : '';
            if ($href === '') {
                continue;
            }
            $columnLinks[] = [
                'url' => $href,
                'title' => isset($link['title']) ? trim((string) $link['title']) : '',
                'target' => isset($link['target']) ? (string) $link['target'] : '_self',
            ];
        }

        $featLink = $row['feat_highlight_link'] ?? null;
        $featLink = is_array($featLink) ? $featLink : [];

        return $base + [
            'col1_title' => isset($row['feat_col1_title']) ? trim((string) $row['feat_col1_title']) : '',
            'col1_text' => isset($row['feat_col1_text']) ? (string) $row['feat_col1_text'] : '',
            'social_heading' => isset($row['feat_social_heading']) ? trim((string) $row['feat_social_heading']) : '',
            'social' => $social,
            'col2_title' => isset($row['feat_col2_title']) ? trim((string) $row['feat_col2_title']) : '',
            'column_links' => $columnLinks,
            'highlight_title' => isset($row['feat_highlight_title']) ? trim((string) $row['feat_highlight_title']) : '',
            'highlight_meta' => isset($row['feat_highlight_meta']) ? trim((string) $row['feat_highlight_meta']) : '',
            'highlight_excerpt' => isset($row['feat_highlight_excerpt']) ? trim((string) $row['feat_highlight_excerpt']) : '',
            'highlight_link' => [
                'url' => isset($featLink['url']) ? trim((string) $featLink['url']) : '',
                'title' => isset($featLink['title']) ? trim((string) $featLink['title']) : '',
                'target' => isset($featLink['target']) ? (string) $featLink['target'] : '_self',
            ],
        ];
    }
}

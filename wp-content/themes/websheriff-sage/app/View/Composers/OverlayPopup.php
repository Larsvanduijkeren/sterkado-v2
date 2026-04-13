<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class OverlayPopup extends Composer
{
    /**
     * @var array<int, string>
     */
    protected static $views = [
        'layouts.app',
    ];

    /**
     * @return array{overlayPopup: array<string, mixed>|null}
     */
    protected function with(): array
    {
        return [
            'overlayPopup' => $this->overlayPopupData(),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function overlayPopupData(): ?array
    {
        if (! function_exists('get_field')) {
            return null;
        }

        $enabled = get_field('overlay_popup_enabled', 'option');
        if (! filter_var($enabled, FILTER_VALIDATE_BOOLEAN)) {
            return null;
        }

        $titleRaw = get_field('overlay_popup_title', 'option');
        $title = is_string($titleRaw) ? trim($titleRaw) : '';

        $textRaw = get_field('overlay_popup_text', 'option');
        $text = is_string($textRaw) ? trim($textRaw) : '';
        $textHtml = $text !== '' ? wp_kses_post($text) : '';

        $buttonsRaw = get_field('overlay_popup_buttons', 'option');
        $buttons = [];
        if (is_array($buttonsRaw)) {
            foreach ($buttonsRaw as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $link = $row['button'] ?? null;
                $link = is_array($link) ? $link : [];
                $url = isset($link['url']) ? trim((string) $link['url']) : '';
                $linkTitle = isset($link['title']) ? trim((string) $link['title']) : '';
                if ($url === '' || $linkTitle === '') {
                    continue;
                }
                $target = isset($link['target']) && (string) $link['target'] !== ''
                    ? (string) $link['target']
                    : '_self';
                $buttons[] = [
                    'url' => $url,
                    'title' => $linkTitle,
                    'target' => $target,
                ];
            }
        }

        if ($title === '' && $textHtml === '' && $buttons === []) {
            return null;
        }

        $daysRaw = get_field('overlay_popup_cookie_days', 'option');
        $days = is_numeric($daysRaw) ? (int) $daysRaw : 7;
        $days = max(1, min(90, $days));
        $maxAge = $days * (int) (defined('DAY_IN_SECONDS') ? DAY_IN_SECONDS : 86400);

        $cookieName = apply_filters('websheriff/overlay_popup_cookie_name', 'ws_overlay_popup_dismiss');

        return [
            'title' => $title,
            'text_html' => $textHtml,
            'buttons' => $buttons,
            'cookie_name' => is_string($cookieName) && $cookieName !== '' ? $cookieName : 'ws_overlay_popup_dismiss',
            'cookie_max_age' => $maxAge,
        ];
    }
}

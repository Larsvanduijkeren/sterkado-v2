@php
$column_count = $fields['column_count'] ?? '2';
$intro_title = $fields['intro_title'] ?? null;
$intro_text = $fields['intro_text'] ?? null;
$intro_buttons = $fields['intro_buttons'] ?? null;
$cards = $fields['cards'] ?? null;
$id = $block['anchor'] ?? null;
$cards = is_array($cards) ? array_values(array_filter($cards, static function ($row): bool {
    return is_array($row);
})) : [];
$has_intro = (bool) ($intro_title || $intro_text || $intro_buttons);
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="content-cards columns-{{ $column_count }}{{ $has_intro ? ' has-intro' : '' }}">
    <div class="container">
        @if($intro_title || $intro_text || $intro_buttons)
        <div class="intro center" data-aos="fade-up">
            @if($intro_title)
            <h2>{{ $intro_title }}</h2>
            @endif
            @if($intro_text)
            <div class="intro-text">{!! wp_kses_post($intro_text) !!}</div>
            @endif
            @if($intro_buttons)
            <div class="buttons">
                @foreach($intro_buttons as $button)
                @php
                $button_obj = $button['button'] ?? $button;
                $url = $button_obj['url'] ?? null;
                $button_title = $button_obj['title'] ?? null;
                $target = $button_obj['target'] ?? '_self';
                $bs = isset($button['button_style']) && is_string($button['button_style']) ? trim($button['button_style']) : '';
                $btnRowClass = \App\acf_button_style_class($bs !== '' ? $bs : null, $loop->first ? 'primary' : 'tertiary');
                @endphp
                @if($url && $button_title)
                <a
                    href="{{ esc_url($url) }}"
                    target="{{ esc_attr($target) }}"
                    class="{{ esc_attr($btnRowClass) }}">
                    {{ $button_title }}
                </a>
                @endif
                @endforeach
            </div>
            @endif
        </div>
        @endif

        @if(count($cards))
        <div class="content-cards-grid" data-aos="fade-up">
            @foreach($cards as $card)
            @php
            $img = $card['image'] ?? null;
            $highlight = $card['highlight_label'] ?? '';
            $tag = $card['label'] ?? '';
            $cardTitle = $card['title'] ?? '';
            $cardTitle = is_string($cardTitle) ? trim($cardTitle) : '';
            $cardText = $card['text'] ?? '';
            $link = $card['link'] ?? null;
            $link = is_array($link) ? $link : [];
            $linkUrl = $link['url'] ?? '';
            $linkTitle = $link['title'] ?? '';
            $linkTarget = $link['target'] ?? '_self';
            $lbs = isset($card['link_button_style']) && is_string($card['link_button_style']) ? trim($card['link_button_style']) : '';
            $cardLinkBtnClass = \App\acf_button_style_class($lbs !== '' ? $lbs : null, 'secondary');
            @endphp
            <article class="content-cards-card">
                @if(is_array($img) && (!empty($img['ID']) || !empty($img['url'])))
                <div class="image">
                    <img
                        src="{{ esc_url($img['sizes']['large'] ?? $img['url'] ?? '') }}"
                        alt="{{ esc_attr($img['alt'] ?? '') }}"
                        loading="lazy"
                        decoding="async">
                    @if($highlight !== '')
                    <span class="content-cards-highlight">{{ e($highlight) }}</span>
                    @endif
                </div>
                @endif
                <div class="body">
                    @if($tag !== '')
                    <span class="label label--uppercase">{{ $tag }}</span>
                    @endif
                    @if($cardTitle !== '')
                    <h2>{{ e($cardTitle) }}</h2>
                    @endif
                    @if($cardText !== '')
                    <div class="text">{!! wp_kses_post($cardText) !!}</div>
                    @endif
                    @if($linkUrl !== '' && $linkTitle !== '')
                    <div class="buttons">
                        <a
                            href="{{ esc_url($linkUrl) }}"
                            class="{{ esc_attr($cardLinkBtnClass) }}"
                            target="{{ esc_attr($linkTarget) }}"
                            @if(($linkTarget ?? '_self') === '_blank') rel="noopener noreferrer" @endif>{{ $linkTitle }}</a>
                    </div>
                    @endif
                </div>
            </article>
            @endforeach
        </div>
        @endif
    </div>
</section>

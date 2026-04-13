@php
$galleryRaw = $fields['gallery'] ?? null;
$gallery = is_array($galleryRaw) ? array_values(array_filter($galleryRaw, static function ($item): bool {
    return is_array($item) && (!empty($item['ID']) || !empty($item['url']));
})) : [];
$image_layout = $fields['image_layout'] ?? 'contained';
$title = $fields['title'] ?? null;
$checklist_raw = $fields['checklist'] ?? null;
$checklist = [];
if (is_array($checklist_raw)) {
    foreach ($checklist_raw as $row) {
        if (! is_array($row)) {
            continue;
        }
        $line = isset($row['item']) ? trim((string) $row['item']) : '';
        if ($line !== '') {
            $checklist[] = $line;
        }
    }
}
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$stat_cards_raw = $fields['stat_cards'] ?? null;
$stat_cards = [];
if (is_array($stat_cards_raw)) {
    foreach ($stat_cards_raw as $row) {
        if (! is_array($row)) {
            continue;
        }
        $st = isset($row['title']) ? trim((string) $row['title']) : '';
        $ss = isset($row['subtitle']) ? trim((string) $row['subtitle']) : '';
        $pos = isset($row['position']) ? (string) $row['position'] : 'bottom-left';
        if ($st !== '' || $ss !== '') {
            $stat_cards[] = [
                'title' => $st,
                'subtitle' => $ss,
                'position' => in_array($pos, ['top-left', 'bottom-left', 'bottom-right'], true) ? $pos : 'bottom-left',
            ];
        }
    }
}
$stat_cards = array_slice($stat_cards, 0, 3);
$partner_heading = $fields['partner_heading'] ?? null;
$partner_logos_raw = $fields['partner_logos'] ?? null;
$partner_logos = is_array($partner_logos_raw) ? array_values(array_filter($partner_logos_raw, static function ($row): bool {
    if (! is_array($row)) {
        return false;
    }
    $img = $row['image'] ?? null;

    return is_array($img) && (!empty($img['ID']) || !empty($img['url']));
})) : [];
$id = $block['anchor'] ?? null;
$is_bleed = $image_layout === 'bleed';
@endphp

<section
    @if($id) id="{{ $id }}" @endif
    class="hero hero--split{{ $is_bleed ? ' hero-image-bleed' : ' hero-image-contained' }}">
    <div class="hero-split">
        <div class="hero-split-grid">
            <div class="hero-copy-col">
                <div class="hero-copy" data-aos="fade-up">
                    <div class="title-wrapper">
                        <div class="hero-breadcrumb">
                            {!! do_shortcode('[rank_math_breadcrumb]') !!}
                        </div>
                        @if($title)
                        <h1>{{ $title }}</h1>
                        @endif
                    </div>

                    <div class="hero-body">
                        @if(count($checklist))
                        <ul class="hero-checklist">
                            @foreach($checklist as $line)
                            <li>{{ $line }}</li>
                            @endforeach
                        </ul>
                        @elseif($text)
                        <div class="hero-legacy-text">{!! $text !!}</div>
                        @endif

                        @if($buttons)
                        <div class="buttons">
                            @foreach($buttons as $button)
                            @php
                            $button_obj = $button['button'] ?? $button;
                            $url = $button_obj['url'] ?? null;
                            $button_title = $button_obj['title'] ?? null;
                            $target = $button_obj['target'] ?? '_self';
                            @endphp
                            @if($url && $button_title)
                            <a
                                href="{{ esc_url($url) }}"
                                target="{{ esc_attr($target) }}"
                                class="{{ $loop->first ? 'btn-secondary' : 'btn-ghost' }}">
                                {{ $button_title }}
                            </a>
                            @endif
                            @endforeach
                        </div>
                        @endif

                        @if($partner_heading || count($partner_logos))
                        <div class="hero-partners">
                            @if($partner_heading)
                            <p class="hero-partners-heading">{{ $partner_heading }}</p>
                            @endif
                            @if(count($partner_logos))
                            <div class="hero-partners-logos">
                                @foreach($partner_logos as $row)
                                @php
                                $logo = $row['image'] ?? null;
                                $link = $row['link'] ?? null;
                                $link = is_array($link) ? $link : [];
                                $logo_url = $logo['sizes']['medium'] ?? $logo['url'] ?? '';
                                $link_href = $link['url'] ?? '';
                                $link_target = $link['target'] ?? '_self';
                                $link_title = $link['title'] ?? '';
                                @endphp
                                @if($link_href !== '')
                                <a
                                    href="{{ esc_url($link_href) }}"
                                    class="hero-partners-logo-link"
                                    target="{{ esc_attr($link_target) }}"
                                    @if($link_target === '_blank') rel="noopener noreferrer" @endif>
                                    <img src="{{ esc_url($logo_url) }}" alt="{{ esc_attr($link_title) }}" loading="lazy" decoding="async">
                                </a>
                                @else
                                <span class="hero-partners-logo">
                                    <img src="{{ esc_url($logo_url) }}" alt="" loading="lazy" decoding="async">
                                </span>
                                @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(!empty($gallery))
            <div class="hero-visual-col">
                <div class="hero-visual">
                    @if(count($stat_cards))
                    <div class="hero-stats">
                        @foreach($stat_cards as $idx => $card)
                        <div class="hero-stat hero-stat--{{ $card['position'] }}">
                            @if($card['title'] !== '')
                            <span class="hero-stat-title">{{ $card['title'] }}</span>
                            @endif
                            @if($card['subtitle'] !== '')
                            <span class="hero-stat-sub">{{ $card['subtitle'] }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                    <div class="hero-visual-media">
                        @if(count($gallery) > 1)
                        <div class="swiper hero-gallery-swiper" aria-label="{{ __('Achtergrondafbeeldingen', 'sage') }}">
                            <div class="swiper-wrapper">
                                @foreach($gallery as $img)
                                <div class="swiper-slide">
                                    <img
                                        src="{{ esc_url($img['sizes']['full'] ?? $img['url'] ?? '') }}"
                                        alt="{{ esc_attr($img['alt'] ?? '') }}"
                                        loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                        decoding="async">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        @php $img = $gallery[0]; @endphp
                        <img
                            src="{{ esc_url($img['sizes']['full'] ?? $img['url'] ?? '') }}"
                            alt="{{ esc_attr($img['alt'] ?? '') }}"
                            loading="eager"
                            decoding="async">
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

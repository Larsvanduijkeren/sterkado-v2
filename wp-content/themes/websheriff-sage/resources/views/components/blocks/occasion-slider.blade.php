@php
$column_count = $fields['column_count'] ?? '2';
$intro_title = $fields['intro_title'] ?? null;
$intro_text = $fields['intro_text'] ?? null;
$intro_buttons = $fields['intro_buttons'] ?? null;
$slides_raw = $fields['slides'] ?? null;
$id = $block['anchor'] ?? null;
$slides = [];
if (is_array($slides_raw)) {
    foreach ($slides_raw as $row) {
        if (! is_array($row)) {
            continue;
        }
        $link = $row['link'] ?? null;
        $link = is_array($link) ? $link : [];
        $url = $link['url'] ?? '';
        if ($url === '') {
            continue;
        }
        $slides[] = $row;
    }
}
$slide_count = count($slides);
$has_intro = (bool) ($intro_title || $intro_text || $intro_buttons);
$show_nav = $slide_count > 1;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="occasion-slider{{ $has_intro ? ' has-intro' : '' }}"
    data-slides-tablet="{{ $column_count }}">
    <div class="container">
        @if($has_intro)
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
                $b_url = $button_obj['url'] ?? null;
                $b_title = $button_obj['title'] ?? null;
                $b_target = $button_obj['target'] ?? '_self';
                $bs = isset($button['button_style']) && is_string($button['button_style']) ? trim($button['button_style']) : '';
                $btnRowClass = \App\acf_button_style_class($bs !== '' ? $bs : null, 'secondary');
                @endphp
                @if($b_url && $b_title)
                <a
                    href="{{ esc_url($b_url) }}"
                    target="{{ esc_attr($b_target) }}"
                    class="{{ esc_attr($btnRowClass) }}"
                    @if(($b_target ?? '_self') === '_blank') rel="noopener noreferrer" @endif>{{ $b_title }}</a>
                @endif
                @endforeach
            </div>
            @endif
        </div>
        @endif

        @if($slide_count)
        <div class="occasion-slider-outer" data-aos="fade-up">
            @if($show_nav)
            <button
                type="button"
                class="occasion-slider-nav occasion-slider-nav-prev"
                aria-label="{{ esc_attr(__('Previous slide', 'sage')) }}"></button>
            <button
                type="button"
                class="occasion-slider-nav occasion-slider-nav-next"
                aria-label="{{ esc_attr(__('Next slide', 'sage')) }}"></button>
            @endif
            <div class="swiper occasion-slider-swiper">
                <div class="swiper-wrapper">
                    @foreach($slides as $slide)
                    @php
                    $img = $slide['image'] ?? null;
                    $label = $slide['label'] ?? '';
                    $link = $slide['link'] ?? [];
                    $link = is_array($link) ? $link : [];
                    $cardUrl = $link['url'] ?? '';
                    $cardTarget = $link['target'] ?? '_self';
                    $has_img = is_array($img) && (!empty($img['ID']) || !empty($img['url']));
                    @endphp
                    <div class="swiper-slide">
                        <a
                            href="{{ esc_url($cardUrl) }}"
                            class="occasion-slider-card"
                            @if($cardTarget) target="{{ esc_attr($cardTarget) }}" @endif
                            @if($cardTarget === '_blank') rel="noopener noreferrer" @endif>
                            <div class="occasion-slider-card-media{{ $has_img ? '' : ' occasion-slider-card-media--empty' }}">
                                @if($has_img)
                                <img
                                    src="{{ esc_url($img['sizes']['large'] ?? $img['url'] ?? '') }}"
                                    alt="{{ esc_attr($img['alt'] ?? '') }}"
                                    loading="lazy"
                                    decoding="async">
                                @endif
                            </div>
                            @if($label !== '')
                            <div class="occasion-slider-card-footer">
                                <span class="occasion-slider-card-label">{{ $label }}</span>
                            </div>
                            @else
                            <div class="occasion-slider-card-footer occasion-slider-card-footer--empty"></div>
                            @endif
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

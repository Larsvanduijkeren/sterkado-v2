@php
$title = $fields['title'] ?? '';
$title_emphasis = $fields['title_emphasis_phrase'] ?? '';
$text = $fields['text'] ?? null;
$button = $fields['button'] ?? null;
$button = is_array($button) ? $button : [];
$ctaButtonClass = \App\acf_button_style_class(
    isset($fields['button_style']) && is_string($fields['button_style']) && $fields['button_style'] !== ''
        ? trim($fields['button_style'])
        : null,
    'primary'
);
$gallery_raw = $fields['images'] ?? null;
$id = $block['anchor'] ?? null;
$images = is_array($gallery_raw) ? array_values(array_filter($gallery_raw, static function ($item): bool {
    return is_array($item) && (!empty($item['ID']) || !empty($item['url']));
})) : [];
$half = max(1, (int) ceil(count($images) / 2));
$images_left = array_slice($images, 0, $half);
$images_right = array_slice($images, $half);
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="cta">
    <div class="container">
        <div class="cta-card" data-aos="fade-up">
            <div class="cta-layout">
                <div class="cta-side cta-side-left{{ count($images_left) ? '' : ' is-empty' }}" @if(count($images_left)) aria-hidden="true" @endif>
                    @foreach($images_left as $img)
                    <div class="cta-floating-img cta-floating-img--l{{ ($loop->index % 6) + 1 }}">
                        <img
                            src="{{ esc_url($img['sizes']['medium'] ?? $img['url'] ?? '') }}"
                            alt=""
                            loading="lazy"
                            decoding="async">
                    </div>
                    @endforeach
                </div>

                <div class="cta-center content">
                    @if($title !== '')
                    <h2 class="cta-title">
                        @if($title_emphasis !== '' && str_contains($title, $title_emphasis))
                        @php
                        $parts = explode($title_emphasis, $title, 2);
                        $t_before = $parts[0] ?? '';
                        $t_after = $parts[1] ?? '';
                        @endphp
                        {{ $t_before }}<span class="cta-title-emphasis">{{ $title_emphasis }}</span>{{ $t_after }}
                        @else
                        {{ $title }}
                        @endif
                    </h2>
                    @endif

                    @if($text)
                    <div class="cta-text">{!! wp_kses_post($text) !!}</div>
                    @endif

                    @if(!empty($button['url']) && !empty($button['title']))
                    <div class="buttons">
                        <a
                            href="{{ esc_url($button['url']) }}"
                            class="{{ esc_attr($ctaButtonClass) }}"
                            target="{{ esc_attr($button['target'] ?? '_self') }}"
                            @if(($button['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif>{{ $button['title'] }}</a>
                    </div>
                    @endif
                </div>

                <div class="cta-side cta-side-right{{ count($images_right) ? '' : ' is-empty' }}" @if(count($images_right)) aria-hidden="true" @endif>
                    @foreach($images_right as $img)
                    <div class="cta-floating-img cta-floating-img--r{{ ($loop->index % 6) + 1 }}">
                        <img
                            src="{{ esc_url($img['sizes']['medium'] ?? $img['url'] ?? '') }}"
                            alt=""
                            loading="lazy"
                            decoding="async">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

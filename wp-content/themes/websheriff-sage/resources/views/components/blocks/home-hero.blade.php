@php
$cards = $fields['cards'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = $fields['add_waves'] ?? false;
$id = $block['anchor'] ?? null;
@endphp

<section
    @if($id) id="{{ $id }}" @endif
    class="home-hero bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        @if($cards)
        <div class="grid">
            @foreach($cards as $index => $card)
            @php
            $image = $card['image'] ?? null;
            $title = $card['title'] ?? null;
            $text = $card['text'] ?? null;
            $button = $card['button'] ?? null;
            $btn_url = is_array($button) ? trim((string) ($button['url'] ?? '')) : '';
            $btn_title = is_array($button) ? ($button['title'] ?? null) : null;
            $btn_target = is_array($button) ? ($button['target'] ?? '_self') : '_self';
            $hasCardLink = $btn_url !== '';
            $cardAriaLabel = $btn_title ?: ($title ? wp_strip_all_tags($title) : '');
            if ($hasCardLink && trim((string) $cardAriaLabel) === '') {
                $cardAriaLabel = __('Meer informatie', 'sage');
            }
            $cardTag = $hasCardLink ? 'a' : 'div';
            $btnClass = $index === 0 ? 'btn white' : 'btn-text white';
            @endphp
            <{{ $cardTag }}
                @if($hasCardLink)
                class="card home-hero__card-link"
                href="{{ esc_url($btn_url) }}"
                target="{{ esc_attr($btn_target) }}"
                @if(($btn_target ?? '_self') === '_blank')
                rel="noopener noreferrer"
                @endif
                aria-label="{{ esc_attr($cardAriaLabel) }}"
                @else
                class="card"
                @endif>
                @if($image)
                <div class="image">
                    <div class="image-inner">
                        <img src="{{ esc_url($image['sizes']['large'] ?? $image['url'] ?? '') }}" alt="{{ esc_attr($image['alt'] ?? '') }}">
                    </div>
                </div>
                @endif

                <div class="content">
                    @if($index === 0)
                    @if($title)
                    <h1 class="h3">{{ $title }}</h1>
                    @endif
                    @else
                    @if($title)
                    <h2 class="h4">{{ $title }}</h2>
                    @endif
                    @endif

                    @if($text)
                    {!! $text !!}
                    @endif

                    @if($hasCardLink && $btn_title)
                    <span class="{{ $btnClass }}">{{ $btn_title }}</span>
                    @endif
                </div>
            </{{ $cardTag }}>
            @endforeach
        </div>
        @endif
    </div>
</section>
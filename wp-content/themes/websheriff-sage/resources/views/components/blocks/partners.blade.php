@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$logos = $fields['logos'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = $fields['add_waves'] ?? false;

$id = $block['anchor'] ?? null;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="partners bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        <div class="intro" data-aos="fade-up">
            @if($label)
            <span class="label">{{ $label }}</span>
            @endif

            @if($title)
            <h2>{{ $title }}</h2>
            @endif
        </div>

        @if($logos && count($logos) > 0)
        <div class="slider overflow-wrap" data-aos="fade-up">
            <div class="swiper">
                <div class="swiper-wrapper">
                    @foreach($logos as $item)
                    @php
                    $image = $item['image'] ?? null;
                    $link = $item['link'] ?? null;
                    $url = is_array($link) ? ($link['url'] ?? null) : null;
                    $target = is_array($link) ? ($link['target'] ?? '_self') : '_self';
                    @endphp
                    <div class="swiper-slide partner-logo">
                        @if($image)
                        @if($url)
                        <a href="{{ $url }}" target="{{ $target }}" rel="{{ $target === '_blank' ? 'noopener noreferrer' : '' }}" class="partner-logo__link">
                            <img src="{{ $image['sizes']['medium'] ?? $image['url'] }}" alt="{{ $image['alt'] ?? '' }}" class="partner-logo__img">
                        </a>
                        @else
                        <span class="partner-logo__wrap">
                            <img src="{{ $image['sizes']['medium'] ?? $image['url'] }}" alt="{{ $image['alt'] ?? '' }}" class="partner-logo__img">
                        </span>
                        @endif
                        @endif
                    </div>
                    @endforeach
                </div>
                <div class="swiper-scrollbar"></div>
            </div>
        </div>
        @endif
    </div>
</section>

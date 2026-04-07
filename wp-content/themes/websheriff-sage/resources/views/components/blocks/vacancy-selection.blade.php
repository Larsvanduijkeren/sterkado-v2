@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = $fields['add_waves'] ?? false;

$id = $block['anchor'] ?? null;
$has_items = isset($vacancies) && $vacancies;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="vacancy-selection bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        <div class="intro center" data-aos="fade-up">
            @if($label)
            <span class="label">{{ $label }}</span>
            @endif

            @if($title)
            <h2>{{ $title }}</h2>
            @endif

            @if($text)
            <div class="intro-text">{!! $text !!}</div>
            @endif
        </div>

        @if($has_items)
        <div class="cards selection-slider-wrap" data-aos="fade-up">
            <div class="swiper selection-swiper">
                <div class="swiper-wrapper">
                    @foreach($vacancies as $vacancy)
                    <div class="swiper-slide">
                        @include('components.vacancy-card', ['vacancy' => $vacancy])
                    </div>
                    @endforeach
                </div>
                <div class="swiper-scrollbar"></div>
            </div>
        </div>
        @endif

        @if(!empty($buttons) && is_array($buttons))
        @php
        $cta = count($buttons) > 1 ? $buttons[1] : $buttons[0];
        $cta_button = $cta['button'] ?? $cta;
        $cta_url = $cta_button['url'] ?? null;
        $cta_title = $cta_button['title'] ?? null;
        $cta_target = $cta_button['target'] ?? '_self';
        @endphp
        @if($cta_url && $cta_title)
        <div class="buttons center" data-aos="fade-up">
            <a href="{{ $cta_url }}" target="{{ $cta_target }}" class="btn">{{ $cta_title }}</a>
        </div>
        @endif
        @endif
    </div>
</section>

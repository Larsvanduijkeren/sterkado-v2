@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$cards = $fields['cards'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = $fields['add_waves'] ?? false;

$id = $block['anchor'] ?? null;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="content-cards bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
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

        @if(!empty($cards) && is_array($cards))
        <div class="cards" data-aos="fade-up">
            @foreach($cards as $card)
            @php
            $card_image = $card['image'] ?? null;
            $card_title = $card['title'] ?? null;
            $card_text = $card['text'] ?? null;
            $card_button = $card['button'] ?? null;
            @endphp
            <div class="content-card">
                @if(!empty($card_image))
                <div class="image">
                    <img src="{{ $card_image['sizes']['large'] ?? $card_image['url'] }}" alt="{{ $card_image['alt'] ?? '' }}">
                </div>
                @endif
                <div class="content">
                    @if($card_title)
                    <h3 class="h4">{{ $card_title }}</h3>
                    @endif
                    @if($card_text)
                    <div class="summary">{!! $card_text !!}</div>
                    @endif
                    @if(!empty($card_button['url']) && !empty($card_button['title']))
                    <a href="{{ $card_button['url'] }}" target="{{ $card_button['target'] ?? '_self' }}" class="btn">{{ $card_button['title'] }}</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

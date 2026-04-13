@php
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$id = $block['anchor'] ?? null;
$is_preview = $is_preview ?? false;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="card-links">
    <div class="container">
        <div class="card-links-banner" data-aos="fade-up">
            <div class="card-links-layout">
                <div class="card-links-intro content">
                    @if($title)
                    <h2>{{ $title }}</h2>
                    @endif
                    @if($text)
                    <div class="card-links-intro-text">{!! wp_kses_post($text) !!}</div>
                    @endif
                    @if($is_preview && count($cards ?? []) === 0)
                    <p class="card-links-preview-note">{{ __('Geen kaarten om te tonen: voeg rijen toe bij “Cards (manual)” met een geldige link.', 'sage') }}</p>
                    @endif
                </div>

                @if(count($cards ?? []))
                <div class="card-links-cards">
                    @foreach($cards ?? [] as $card)
                    <article class="card-links-card">
                        @if($card['name'] !== '')
                        <h3 class="card-links-card-title">{{ $card['name'] }}</h3>
                        @endif
                        @if($card['description'] !== '')
                        <div class="card-links-card-text">{!! wp_kses_post($card['description']) !!}</div>
                        @endif
                        @if($card['url'] !== '' && $card['button_label'] !== '')
                        <div class="buttons">
                            <a href="{{ esc_url($card['url']) }}" class="{{ esc_attr(\App\acf_button_style_class($card['button_style'] ?? null, 'secondary')) }}">{{ $card['button_label'] }}</a>
                        </div>
                        @endif
                    </article>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

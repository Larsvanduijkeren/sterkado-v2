@php
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = filter_var($fields['add_waves'] ?? false, FILTER_VALIDATE_BOOLEAN);
$id = $block['anchor'] ?? null;
$is_preview = $is_preview ?? false;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="card-links bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        <div class="card-links-banner">
            <div class="card-links-layout">
                <div class="card-links-intro content" data-aos="fade-up">
                    @if($title)
                    <h2>{{ $title }}</h2>
                    @endif
                    @if($text)
                    <div class="card-links-intro-text">{!! wp_kses_post($text) !!}</div>
                    @endif
                    @if($is_preview && count($category_cards ?? []) === 0)
                    <p class="card-links-preview-note">{{ __('Geen categorieën op dit bericht; kaarten verschijnen op de site zodra termen gekoppeld zijn.', 'sage') }}</p>
                    @endif
                </div>

                @if(count($category_cards ?? []))
                <div class="card-links-cards" data-aos="fade-up">
                    @foreach($category_cards ?? [] as $card)
                    <article class="card-links-card">
                        @if($card['name'] !== '')
                        <h3 class="card-links-card-title">{{ $card['name'] }}</h3>
                        @endif
                        @if($card['description'] !== '')
                        <div class="card-links-card-text">{!! wp_kses_post($card['description']) !!}</div>
                        @endif
                        @if($card['url'] !== '' && $card['button_label'] !== '')
                        <div class="buttons">
                            <a href="{{ esc_url($card['url']) }}" class="btn-secondary">{{ $card['button_label'] }}</a>
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

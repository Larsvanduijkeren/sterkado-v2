@php
$background_color = $fields['background_color'] ?? 'white';
$add_waves = filter_var($fields['add_waves'] ?? false, FILTER_VALIDATE_BOOLEAN);
$id = $block['anchor'] ?? null;
$is_preview = $is_preview ?? false;
$top = is_array($gallery_marquee_top ?? null) ? $gallery_marquee_top : [];
$bottom = is_array($gallery_marquee_bottom ?? null) ? $gallery_marquee_bottom : [];
$durTop = (int) ($gallery_marquee_top_duration ?? 40);
$durBottom = (int) ($gallery_marquee_bottom_duration ?? 40);
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="gallery-marquee bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    @if(count($top) || count($bottom))
    <div class="gallery-marquee-rows">
        @if(count($top))
        <div class="gallery-marquee-row gallery-marquee-row-forward" style="--gallery-marquee-duration: {{ $durTop }}s;">
            <div class="gallery-marquee-scroll">
                @foreach([0, 1] as $stripIndex)
                <div class="gallery-marquee-strip" @if($stripIndex === 1) aria-hidden="true" @endif>
                    @foreach($top as $img)
                    @php
                    $src = $img['sizes']['large'] ?? $img['url'] ?? '';
                    $alt = $img['alt'] ?? '';
                    @endphp
                    @if($src !== '')
                    <figure class="gallery-marquee-cell">
                        <img src="{{ esc_url($src) }}" alt="{{ esc_attr($alt) }}" loading="{{ $stripIndex === 0 && $loop->first ? 'eager' : 'lazy' }}" decoding="async">
                    </figure>
                    @endif
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(count($bottom))
        <div class="gallery-marquee-row gallery-marquee-row-reverse" style="--gallery-marquee-duration: {{ $durBottom }}s;">
            <div class="gallery-marquee-scroll">
                @foreach([0, 1] as $stripIndex)
                <div class="gallery-marquee-strip" @if($stripIndex === 1) aria-hidden="true" @endif>
                    @foreach($bottom as $img)
                    @php
                    $src = $img['sizes']['large'] ?? $img['url'] ?? '';
                    $alt = $img['alt'] ?? '';
                    @endphp
                    @if($src !== '')
                    <figure class="gallery-marquee-cell">
                        <img src="{{ esc_url($src) }}" alt="{{ esc_attr($alt) }}" loading="lazy" decoding="async">
                    </figure>
                    @endif
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @elseif($is_preview)
    <div class="container">
        <p class="gallery-marquee-empty">{{ __('Voeg afbeeldingen toe aan de galerij om de marquee te tonen.', 'sage') }}</p>
    </div>
    @endif
</section>

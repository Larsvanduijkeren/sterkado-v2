@php
$galleryRaw = $fields['gallery'] ?? null;
$gallery = is_array($galleryRaw) ? array_values(array_filter($galleryRaw, static function ($item): bool {
    return is_array($item) && (!empty($item['ID']) || !empty($item['url']));
})) : [];
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = filter_var($fields['add_waves'] ?? false, FILTER_VALIDATE_BOOLEAN);

$id = $block['anchor'] ?? null;

$googleBadgeShow = function_exists('get_field') ? get_field('google_badge_show', 'option') : false;
$googleRating = function_exists('get_field') ? get_field('google_badge_rating', 'option') : '';
$googleReviewCount = function_exists('get_field') ? get_field('google_badge_review_count', 'option') : '';
$googleBadgeLink = function_exists('get_field') ? get_field('google_badge_link', 'option') : '';
$showGoogleBadge = $googleBadgeShow && ($googleRating !== '' || $googleReviewCount !== '');
@endphp

<section
    @if($id) id="{{ $id }}" @endif
    class="hero bg-{{ $background_color }}{{ $showGoogleBadge ? ' hero--has-google-badge' : '' }}{{ $add_waves ? ' has-waves' : '' }}">

    <div class="container">
        <div class="card">
            @if(!empty($gallery))
            <div class="image">
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
            @endif

            <div class="content">
                <div class="title-wrapper">
                    <div class="hero__breadcrumb">
                        {!! do_shortcode('[rank_math_breadcrumb]') !!}
                    </div>

                    @if($title)
                    <h1>{{ $title }}</h1>
                    @endif
                </div>

                <div class="wrapper">
                    @if($text)
                    {!! $text !!}
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
                            class="{{ $loop->first ? 'btn btn-accent' : 'btn white' }}">
                            {{ $button_title }}
                        </a>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            @if($showGoogleBadge)
            <div class="hero-google-badge">
                @if(!empty($googleBadgeLink))
                <a class="hero-google-badge__inner" href="{{ esc_url($googleBadgeLink) }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Google reviews', 'sage') }}">
                @else
                <div class="hero-google-badge__inner" role="img" aria-label="{{ __('Google reviews', 'sage') }}">
                @endif
                    <span class="hero-google-badge__logo" aria-hidden="true">
                        <img
                            src="{{ esc_url(get_template_directory_uri() . '/resources/images/google-logo.svg') }}"
                            alt=""
                            width="18"
                            height="18"
                            loading="lazy"
                            decoding="async">
                    </span>
                    <span class="hero-google-badge__stars" aria-hidden="true">
                        @for($i = 0; $i < 5; $i++)<span class="hero-google-badge__star">★</span>@endfor
                    </span>
                    <span class="hero-google-badge__text">
                        @if($googleRating !== '')<span class="hero-google-badge__rating">{{ $googleRating }}</span>@endif
                        @if($googleRating !== '' && $googleReviewCount !== '')<span class="hero-google-badge__sep">|</span>@endif
                        @if($googleReviewCount !== '')<span class="hero-google-badge__count">{{ $googleReviewCount }}</span>@endif
                    </span>
                @if(!empty($googleBadgeLink))
                </a>
                @else
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>

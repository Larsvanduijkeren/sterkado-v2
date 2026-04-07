@php
$logo = get_field('logo', 'option');
$headerCta = get_field('header_button', 'option') ?: [];
if (empty($headerCta['url']) && function_exists('get_field')) {
    $legacyButtons = get_field('header_buttons', 'option');
    if (!empty($legacyButtons[0]['button']['url'])) {
        $headerCta = $legacyButtons[0]['button'];
    }
}
$phone = get_field('phone', 'option');
$email = get_field('email', 'option');
$header_usps = get_field('header_usps', 'option');
@endphp

@include('partials.mobile-nav', [
    'headerCta' => $headerCta,
])

<span class="hamburger"></span>

<div class="header-wrapper">
@if(!empty($header_usps) && is_array($header_usps))
<div class="header-usps-bar">
    <div class="header-usps-slider-wrap">
        <div class="swiper header-usps-swiper">
            <div class="swiper-wrapper">
                @foreach($header_usps as $usp)
                @if(!empty($usp['text']))
                <div class="swiper-slide">
                    <span class="usp-text"><i class="fa-solid fa-check" aria-hidden="true"></i>{{ $usp['text'] }}</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<header class="header">
    <div class="container">
        <div class="flex-wrapper">
            <a href="{{ home_url('/') }}" class="logo" aria-label="Logo for {{get_bloginfo('name')}}">
                @if(!empty($logo))
                <img src="{{ $logo['sizes']['large'] ?? $logo['url'] ?? '' }}" alt="">
                @endif
            </a>

            <div class="main-header">
                <div class="top-bar">
                    {!! wp_nav_menu(['theme_location' => 'header-nav', 'echo' => false]) !!}

                    @if(!empty($headerCta['url']) && !empty($headerCta['title']))
                    <div class="header-buttons">
                        <a
                            class="btn btn-accent small"
                            href="{{ esc_url($headerCta['url']) }}"
                            target="{{ esc_attr($headerCta['target'] ?? '_self') }}"
                        >{{ $headerCta['title'] }}</a>
                    </div>
                    @endif
                </div>

                <div class="bottom-bar">
                    @if(!empty($phone))
                    <a href="tel:{{ $phone }}" class="phone">
                        {{ $phone }}
                    </a>
                    @endif

                    @if(!empty($email))
                    <a href="mailto:{{ $email }}" class="email">
                        {{ $email }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>
</div>
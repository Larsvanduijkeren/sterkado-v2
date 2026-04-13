@php
$logo = get_field('logo', 'option');
$phone = get_field('phone', 'option');
$feedbackRaw = get_field('header_feedback_shortcode', 'option');
$feedbackRaw = is_string($feedbackRaw) ? trim($feedbackRaw) : '';
$ctaTertiary = get_field('header_cta_tertiary', 'option');
$ctaTertiary = is_array($ctaTertiary) ? $ctaTertiary : [];
$ctaSecondary = get_field('header_cta_secondary', 'option');
$ctaSecondary = is_array($ctaSecondary) ? $ctaSecondary : [];
$mainNavHtml = wp_nav_menu([
    'theme_location' => 'header-main-nav',
    'menu_class'     => 'menu header-menu header-menu--main',
    'container'      => 'div',
    'container_class'=> 'header__main-nav-inner',
    'echo'           => false,
    'fallback_cb'    => false,
]);
$topNavHtml = wp_nav_menu([
    'theme_location' => 'header-top-nav',
    'menu_class'     => 'menu header-menu header-menu--top',
    'container'      => 'div',
    'container_class'=> 'header__top-nav-inner',
    'echo'           => false,
    'fallback_cb'    => false,
]);
@endphp

@include('partials.mobile-nav', [
    'logo' => $logo,
    'phone' => $phone,
    'ctaTertiary' => $ctaTertiary,
    'ctaSecondary' => $ctaSecondary,
    'mainNavHtml' => $mainNavHtml,
    'topNavHtml' => $topNavHtml,
    'feedbackRaw' => $feedbackRaw,
])

<div class="header-wrapper">
<header class="header">
    <div class="container">
        <div class="header__row header__row--top">
            <div class="header__feedback" aria-label="{{ __('Reviews', 'sage') }}">
                @if($feedbackRaw !== '')
                <div class="header__feedback-inner">
                    {!! do_shortcode(wp_unslash($feedbackRaw)) !!}
                </div>
                @endif
            </div>

            <a href="{{ home_url('/') }}" class="header__logo-link logo" aria-label="{{ esc_attr(sprintf(__('Home — %s', 'sage'), get_bloginfo('name'))) }}">
                @if(!empty($logo['url'] ?? null))
                <img src="{{ esc_url($logo['sizes']['large'] ?? $logo['url']) }}" alt="{{ esc_attr($logo['alt'] ?? get_bloginfo('name')) }}">
                @endif
            </a>

            <div class="header__top-trail">
                @if(!empty($phone))
                <a href="tel:{{ esc_attr(preg_replace('/\s+/', '', wp_strip_all_tags($phone))) }}" class="header__phone">{{ $phone }}</a>
                @endif
                @if($topNavHtml)
                <nav class="header__top-nav" aria-label="{{ esc_attr(__('Secondary menu', 'sage')) }}">
                    {!! $topNavHtml !!}
                </nav>
                @endif
            </div>

            <span class="hamburger" role="button" tabindex="0" aria-label="{{ esc_attr(__('Menu', 'sage')) }}" aria-expanded="false"></span>
        </div>

        <div class="header__row header__row--bottom">
            @if($mainNavHtml)
            <nav class="header__main-nav" aria-label="{{ esc_attr(__('Main menu', 'sage')) }}">
                {!! $mainNavHtml !!}
            </nav>
            @else
            <div class="header__main-nav"></div>
            @endif

            <div class="header__actions">
                @if(!empty($ctaTertiary['url']) && !empty($ctaTertiary['title']))
                <a
                    href="{{ esc_url($ctaTertiary['url']) }}"
                    target="{{ esc_attr($ctaTertiary['target'] ?? '_self') }}"
                    class="btn btn-ghost">{{ e($ctaTertiary['title']) }}</a>
                @endif
                @if(!empty($ctaSecondary['url']) && !empty($ctaSecondary['title']))
                <a
                    href="{{ esc_url($ctaSecondary['url']) }}"
                    target="{{ esc_attr($ctaSecondary['target'] ?? '_self') }}"
                    class="btn-secondary">{{ e($ctaSecondary['title']) }}</a>
                @endif
            </div>
        </div>
    </div>
</header>
</div>

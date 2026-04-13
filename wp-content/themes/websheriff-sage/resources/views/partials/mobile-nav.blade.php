@php
$logo = $logo ?? null;
$phone = $phone ?? null;
$ctaTertiary = $ctaTertiary ?? [];
$ctaSecondary = $ctaSecondary ?? [];
$mainNavHtml = $mainNavHtml ?? '';
$topNavHtml = $topNavHtml ?? '';
$feedbackRaw = $feedbackRaw ?? '';
@endphp
<div class="mobile-nav">
    <div class="content">
        <div class="nav">
            <div class="flex-wrapper">
                @if(!empty($logo['url'] ?? null))
                <a href="{{ home_url('/') }}" class="mobile-nav__logo" aria-label="{{ esc_attr(sprintf(__('Home — %s', 'sage'), get_bloginfo('name'))) }}">
                    <img src="{{ esc_url($logo['sizes']['large'] ?? $logo['url']) }}" alt="{{ esc_attr($logo['alt'] ?? get_bloginfo('name')) }}">
                </a>
                @endif

                @if(is_string($feedbackRaw) && $feedbackRaw !== '')
                <div class="mobile-nav__feedback">
                    {!! do_shortcode(wp_unslash($feedbackRaw)) !!}
                </div>
                @endif

                @if(!empty($phone))
                <a class="phone" href="tel:{{ esc_attr(preg_replace('/\s+/', '', wp_strip_all_tags($phone))) }}">{{ $phone }}</a>
                @endif

                @if($topNavHtml)
                <div class="mobile-nav__label">{{ __('Menu', 'sage') }}</div>
                {!! $topNavHtml !!}
                @endif

                @if($mainNavHtml)
                <div class="mobile-nav__label mobile-nav__label--main">{{ __('Hoofdmenu', 'sage') }}</div>
                {!! $mainNavHtml !!}
                @endif

                @if((!empty($ctaTertiary['url']) && !empty($ctaTertiary['title'])) || (!empty($ctaSecondary['url']) && !empty($ctaSecondary['title'])))
                <div class="header-buttons mobile-nav__ctas">
                    @if(!empty($ctaTertiary['url']) && !empty($ctaTertiary['title']))
                    <a
                        class="btn btn-ghost"
                        href="{{ esc_url($ctaTertiary['url']) }}"
                        target="{{ esc_attr($ctaTertiary['target'] ?? '_self') }}"
                    >{{ e($ctaTertiary['title']) }}</a>
                    @endif
                    @if(!empty($ctaSecondary['url']) && !empty($ctaSecondary['title']))
                    <a
                        class="btn-secondary"
                        href="{{ esc_url($ctaSecondary['url']) }}"
                        target="{{ esc_attr($ctaSecondary['target'] ?? '_self') }}"
                    >{{ e($ctaSecondary['title']) }}</a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

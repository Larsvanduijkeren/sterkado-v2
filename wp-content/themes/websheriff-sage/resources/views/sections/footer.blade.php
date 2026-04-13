@php
$footerAfterSectionClass = $footerAfterSectionClass ?? '';
$headingHtml = $footerHeadingHtml ?? '';
$displayLogo = $footerDisplayLogo ?? null;
$navColumns = $footerNavColumns ?? [];
$socialRows = $footerSocialRows ?? [];
$showCredits = $footerShowCredits ?? false;
$creditsRaw = $footerCreditsRaw ?? '';
$showTopRow = $footerShowTopRow ?? false;
@endphp

<footer class="footer{{ $footerAfterSectionClass !== '' ? ' ' . e($footerAfterSectionClass) : '' }}">
    <section class="footer__section" aria-label="{{ esc_attr(__('Site footer', 'sage')) }}">
        <div class="container">
            <div class="footer__card">
                @if($showTopRow)
                <div class="footer__top flex-wrapper">
                    <div class="footer__heading-wrap content">
                        @if($headingHtml !== '')
                        <h2 class="h2 footer__heading">{!! $headingHtml !!}</h2>
                        @endif
                    </div>
                    @if(!empty($displayLogo))
                    <div class="footer__logo-wrap">
                        <a href="{{ home_url('/') }}" class="logo footer__logo-link" aria-label="{{ esc_attr(get_bloginfo('name')) }}">
                            <img src="{{ esc_url($displayLogo['sizes']['medium'] ?? $displayLogo['url'] ?? '') }}" alt="">
                        </a>
                    </div>
                    @endif
                </div>
                <div class="footer__divider" role="presentation"></div>
                @endif
                <div class="footer__nav-grid">
                    @foreach($navColumns as $col)
                    @if(($col['title'] ?? '') !== '' || ($col['html'] ?? '') !== '')
                    <div class="footer__nav-col">
                        @if(($col['title'] ?? '') !== '')
                        <h3 class="footer__nav-title">{{ e($col['title']) }}</h3>
                        @endif
                        @if(($col['html'] ?? '') !== '')
                        {!! $col['html'] !!}
                        @endif
                    </div>
                    @endif
                    @endforeach
                </div>
                @if(!empty($socialRows))
                <div class="footer__social-row">
                    <div class="footer__social" aria-label="{{ esc_attr(__('Social media', 'sage')) }}">
                        @foreach($socialRows as $row)
                        <a href="{{ esc_url($row['url']) }}" class="footer__social-link" target="{{ esc_attr($row['target']) }}" rel="noopener noreferrer" aria-label="{{ esc_attr($row['aria_label']) }}">
                            <i class="{{ esc_attr($row['icon_class']) }}" aria-hidden="true"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
</footer>

@if($showCredits)
<div class="footer-credits">
    <div class="container footer-credits__inner">
        <div class="footer-credits__content">
            {!! wp_kses_post($creditsRaw) !!}
        </div>
    </div>
</div>
@endif

@php
$logo = get_field('logo', 'option');
$footer_logo = get_field('footer_logo', 'option');
$footer_description = get_field('footer_description', 'option');
$footer_facebook = get_field('footer_facebook', 'option');
$footer_instagram = get_field('footer_instagram', 'option');
$footer_offer_title = get_field('footer_offer_title', 'option') ?: __('Ons aanbod', 'sage');
$footer_offer_links = get_field('footer_offer_links', 'option');
$footer_links_title = get_field('footer_links_title', 'option') ?: __('Handige links', 'sage');
$footer_links = get_field('footer_links', 'option');
$footer_contact_title = get_field('footer_contact_title', 'option') ?: __('Contact', 'sage');
$footer_phone = get_field('footer_phone', 'option');
$footer_email = get_field('footer_email', 'option');
$footer_address = get_field('footer_address', 'option');
$footer_slogan = get_field('footer_slogan', 'option');
$display_logo = !empty($footer_logo) ? $footer_logo : $logo;
$footer_credits = get_field('footer_credits', 'option');
$show_footer_credits = is_string($footer_credits) && trim(wp_strip_all_tags($footer_credits)) !== '';
@endphp

<footer class="footer{{ $footerAfterSectionClass !== '' ? ' ' . e($footerAfterSectionClass) : '' }}">
    <div class="container">
        <div class="flex-wrapper">
            <div class="col col-brand">
                <a href="{{ home_url('/') }}" class="logo" aria-label="{{ get_bloginfo('name') }}">
                    @if(!empty($display_logo))
                    <img src="{{ $display_logo['sizes']['medium'] ?? $display_logo['url'] ?? '' }}" alt="">
                    @endif
                </a>
                @if($footer_description)
                <p class="description">{{ nl2br(e($footer_description)) }}</p>
                @endif
                @if($footer_facebook || $footer_instagram)
                <div class="social">
                    @if($footer_facebook)
                    <a href="{{ $footer_facebook }}" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Facebook">
                        <span class="icon-font icon-fb"></span>
                    </a>
                    @endif
                    @if($footer_instagram)
                    <a href="{{ $footer_instagram }}" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram">
                        <span class="icon-font icon-ig"></span>
                    </a>
                    @endif
                </div>
                @endif
            </div>

            <div class="col col-offer">
                <h3 class="h4 col-title">{{ $footer_offer_title }}</h3>
                @if(!empty($footer_offer_links) && is_array($footer_offer_links))
                <ul class="link-list">
                    @foreach($footer_offer_links as $item)
                    @php $link = $item['link'] ?? null; @endphp
                    @if(!empty($link['url']))
                    <li><a href="{{ $link['url'] }}" target="{{ $link['target'] ?? '_self' }}">{{ $link['title'] ?? '' }}</a></li>
                    @endif
                    @endforeach
                </ul>
                @endif
            </div>

            <div class="col col-links">
                <h3 class="h4 col-title">{{ $footer_links_title }}</h3>
                @if(!empty($footer_links) && is_array($footer_links))
                <ul class="link-list">
                    @foreach($footer_links as $item)
                    @php $link = $item['link'] ?? null; @endphp
                    @if(!empty($link['url']))
                    <li><a href="{{ $link['url'] }}" target="{{ $link['target'] ?? '_self' }}">{{ $link['title'] ?? '' }}</a></li>
                    @endif
                    @endforeach
                </ul>
                @endif
            </div>

            <div class="col col-contact">
                <h3 class="h4 col-title">{{ $footer_contact_title }}</h3>
                @if($footer_phone)
                <p class="contact-line contact-phone">
                    <a href="tel:{{ preg_replace('/\s+/', '', $footer_phone) }}">{{ $footer_phone }}</a>
                </p>
                @endif
                @if($footer_email)
                <p class="contact-line contact-email">
                    <a href="mailto:{{ $footer_email }}">{{ $footer_email }}</a>
                </p>
                @endif
                @if($footer_address)
                <p class="contact-line contact-address">{!! $footer_address !!}</p>
                @endif
            </div>
        </div>

        @if($footer_slogan)
        <div class="extra-footer">
            <span class="slogan">{!! $footer_slogan !!}</span>
        </div>
        @endif
    </div>
</footer>

@if($show_footer_credits)
<div class="footer-credits">
    <div class="container footer-credits__inner">
        <div class="footer-credits__content">
            {!! wp_kses_post($footer_credits) !!}
        </div>
    </div>
</div>
@endif

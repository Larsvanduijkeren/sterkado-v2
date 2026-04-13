@php
$id = $block['anchor'] ?? null;
$title = $fields['title'] ?? null;
$subtitle = $fields['subtitle'] ?? null;
$button = $fields['button'] ?? null;
$button = is_array($button) ? $button : [];
$buttonUrl = isset($button['url']) ? trim((string) $button['url']) : '';
$buttonTitle = isset($button['title']) ? trim((string) $button['title']) : '';
$buttonTarget = isset($button['target']) ? (string) $button['target'] : '_self';
$notFoundBtnClass = \App\acf_button_style_class(
    isset($fields['button_style']) && is_string($fields['button_style']) && $fields['button_style'] !== ''
        ? trim($fields['button_style'])
        : null,
    'tertiary'
);
$phonePrimary = $not_found_phone_primary ?? '';
$phoneSecondary = $not_found_phone_secondary ?? '';
$phoneUrl = $not_found_phone_url ?? '';
$emailPrimary = $not_found_email_primary ?? '';
$emailSecondary = $not_found_email_secondary ?? '';
$emailUrl = $not_found_email_url ?? '';
$is_preview = $is_preview ?? false;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="not-found">
    <div class="not-found-inner">
        <div class="container">
            <div class="not-found-main" @if(!$is_preview) data-aos="fade-up" @endif>
                @if($title)
                <h1 class="not-found-title h2">{{ $title }}</h1>
                @endif
                @if($subtitle)
                <p class="not-found-subtitle">{!! wp_kses_post($subtitle) !!}</p>
                @endif
                @if($buttonUrl !== '' && $buttonTitle !== '')
                <div class="not-found-actions">
                    <a
                        href="{{ esc_url($buttonUrl) }}"
                        class="{{ esc_attr($notFoundBtnClass) }} not-found-cta"
                        @if($buttonTarget !== '') target="{{ esc_attr($buttonTarget) }}" @endif
                        @if($buttonTarget === '_blank') rel="noopener noreferrer" @endif>{{ $buttonTitle }}</a>
                </div>
                @endif
                @if($phonePrimary !== '' || $emailPrimary !== '')
                <div class="not-found-contact">
                    @if($phonePrimary !== '')
                    <div class="not-found-contact-item">
                        @if($phoneUrl !== '')
                        <a
                            class="not-found-contact-link"
                            href="{{ esc_url($phoneUrl) }}"
                            aria-label="{{ esc_attr($phonePrimary) }}">
                            <span class="not-found-contact-icon not-found-contact-icon--phone" aria-hidden="true">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <span class="not-found-contact-body">
                                <span class="not-found-contact-primary">{{ $phonePrimary }}</span>
                                @if($phoneSecondary !== '')
                                <span class="not-found-contact-secondary">{{ $phoneSecondary }}</span>
                                @endif
                            </span>
                        </a>
                        @else
                        <div class="not-found-contact-link not-found-contact-link--static">
                            <span class="not-found-contact-icon not-found-contact-icon--phone" aria-hidden="true">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <span class="not-found-contact-body">
                                <span class="not-found-contact-primary">{{ $phonePrimary }}</span>
                                @if($phoneSecondary !== '')
                                <span class="not-found-contact-secondary">{{ $phoneSecondary }}</span>
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($emailPrimary !== '')
                    <div class="not-found-contact-item">
                        @if($emailUrl !== '')
                        <a
                            class="not-found-contact-link"
                            href="{{ esc_url($emailUrl) }}"
                            aria-label="{{ esc_attr($emailPrimary) }}">
                            <span class="not-found-contact-icon not-found-contact-icon--email" aria-hidden="true">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <span class="not-found-contact-body">
                                <span class="not-found-contact-primary">{{ $emailPrimary }}</span>
                                @if($emailSecondary !== '')
                                <span class="not-found-contact-secondary">{{ $emailSecondary }}</span>
                                @endif
                            </span>
                        </a>
                        @else
                        <div class="not-found-contact-link not-found-contact-link--static">
                            <span class="not-found-contact-icon not-found-contact-icon--email" aria-hidden="true">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <span class="not-found-contact-body">
                                <span class="not-found-contact-primary">{{ $emailPrimary }}</span>
                                @if($emailSecondary !== '')
                                <span class="not-found-contact-secondary">{{ $emailSecondary }}</span>
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

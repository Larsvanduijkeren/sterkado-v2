@php
$heroTitle = $fields['hero_title'] ?? null;
$heroText = $fields['hero_text'] ?? null;
$quoteImage = $fields['quote_image'] ?? null;
$quote = $fields['quote'] ?? null;
$quoteAuthor = $fields['quote_author'] ?? null;
$explanationTitle = $fields['explanation_title'] ?? null;
$explanationText = $fields['explanation_text'] ?? null;
$uspsTitle = $fields['usps_title'] ?? null;
$usps = $fields['usps'] ?? null;
$contactTitle = $fields['contact_title'] ?? null;
$contactText = $fields['contact_text'] ?? null;
$phoneTitle = $fields['phone_title'] ?? null;
$phoneText = $fields['phone_text'] ?? null;
$emailTitle = $fields['email_title'] ?? null;
$emailText = $fields['email_text'] ?? null;
$contactMeta = $fields['contact_meta'] ?? null;
$contactImage = $fields['contact_image'] ?? null;
$requestQuoteText = $fields['request_quote_text'] ?? null;
$orderNowText = $fields['order_now_text'] ?? null;
$feedbackRaw = $fields['step_feedback_shortcode'] ?? null;
$id = $block['anchor'] ?? null;
@endphp
<section @if($id) id="{{ $id }}-hero" @endif class="request-quote-hero">
    <div class="container">
        <div class="request-quote-hero__inner flex-wrapper">
            <div class="content" data-aos="fade-up">
                @if($heroTitle)
                <h1 class="h2 request-quote-hero__title">{{ e($heroTitle) }}</h1>
                @endif
                @if($heroText)
                <div class="request-quote-hero__intro">
                    {!! apply_filters('the_content', $heroText) !!}
                </div>
                @endif
            </div>
            @if($quote)
            <aside data-aos="fade-up" class="request-quote-hero__quote" aria-label="{{ esc_attr(__('Quote', 'sage')) }}">
                <div class="request-quote-hero__quote-visual">
                    @if(!empty($quoteImage['url'] ?? null))
                    <div class="request-quote-hero__quote-logo">
                        <img src="{{ esc_url($quoteImage['sizes']['large'] ?? $quoteImage['url']) }}" alt="{{ esc_attr($quoteImage['alt'] ?? '') }}" loading="lazy" decoding="async">
                    </div>
                    @endif
                    <div class="request-quote-hero__quote-card">
                        <blockquote class="request-quote-hero__quote-text">
                            <p>{!! nl2br(esc_html($quote)) !!}</p>
                        </blockquote>
                        @if($quoteAuthor)
                        <p class="request-quote-hero__quote-author">{{ e($quoteAuthor) }}</p>
                        @endif
                    </div>
                </div>
            </aside>
            @endif
        </div>
    </div>
</section>

<div class="request-quote__modal" data-rq-modal aria-hidden="true">
    <span class="request-quote__modal-overlay" data-rq-modal-overlay tabindex="-1" role="presentation"></span>
    <div class="request-quote__modal-panel" role="dialog" aria-modal="true" aria-label="{{ esc_attr(__('Meer informatie', 'sage')) }}">
        <button type="button" class="request-quote__modal-close" data-rq-modal-close aria-label="{{ esc_attr(__('Sluiten', 'sage')) }}"></button>
        <div class="request-quote__modal-body"></div>
    </div>
</div>

<section @if($id) id="{{ $id }}" @endif class="request-quote">
    <div class="container">
        <div class="request-quote__layout flex-wrapper" data-aos="fade-up">
            <div class="request-quote__main content flow-wrapper">
                <nav class="request-quote__controls controls" aria-label="{{ esc_attr(__('Stappen', 'sage')) }}">
                    <button type="button" class="request-quote__control step step-1 current" data-step-target="1">
                        <span class="request-quote__control-count">1.</span>
                        <span class="request-quote__control-label">{{ __('Aantallen & verpakking', 'sage') }}</span>
                    </button>
                    <button type="button" class="request-quote__control step step-2" data-step-target="2">
                        <span class="request-quote__control-count">2.</span>
                        <span class="request-quote__control-label">{{ __('Gegevens', 'sage') }}</span>
                    </button>
                    <button type="button" class="request-quote__control step step-3" data-step-target="3">
                        <span class="request-quote__control-count">3.</span>
                        <span class="request-quote__control-label">{{ __('Bevestiging', 'sage') }}</span>
                    </button>
                </nav>

                <div class="request-quote__flow flow">
                    <div class="request-quote__step step step-1 current">
                        <h4 class="request-quote__step-heading step-title h4">
                            {{ __('1. Aantal medewerkers en budget', 'sage') }}
                            <span class="request-quote__step-edit edit" role="button" tabindex="0">{{ __('wijzig', 'sage') }}</span>
                        </h4>
                        <div class="request-quote__step-panel step-content">
                            <div class="request-quote__info-group info-group">
                                <h4 class="h4">{{ __('1. Aantal medewerkers en budget', 'sage') }}</h4>
                                <div class="request-quote__audience-form target-audience-form form-group">
                                    <div class="input-group">
                                        <label>
                                            {{ __('Aantal medewerkers', 'sage') }}
                                            <input type="number" name="employee-amount" min="1" step="1" autocomplete="off">
                                        </label>
                                    </div>
                                    <div class="input-group">
                                        <label>
                                            {{ __('Budget per medewerker', 'sage') }}
                                            <input type="number" name="employee-budget" min="20" step="1" autocomplete="off">
                                        </label>
                                        <p class="request-quote__legend legend">{{ __('Minimaal €20', 'sage') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="request-quote__no-results no-results" role="status">
                                <h4 class="h4">{{ __('Helaas', 'sage') }}</h4>
                                <p>{{ __('Voor het opgegeven aantal medewerkers en het bijbehorende budget kunnen wij helaas geen passende offerte uitbrengen.', 'sage') }}</p>
                            </div>
                            <div class="request-quote__options options-wrapper"></div>
                            <p class="request-quote__missing missing-values-message" role="alert">{{ __('Selecteer alle opties voor u naar de volgende stap gaat.', 'sage') }}</p>
                            <div class="request-quote__actions button-wrap">
                                @if(is_string($feedbackRaw) && trim($feedbackRaw) !== '')
                                <div class="request-quote__feedback rating">{!! do_shortcode(wp_unslash($feedbackRaw)) !!}</div>
                                @endif
                                <button type="button" class="btn go-to-step go-to-details" data-step-target="2" disabled>{{ __('Naar gegevens', 'sage') }}</button>
                            </div>
                        </div>
                    </div>

                    <div class="request-quote__step step step-2 disabled">
                        <h4 class="request-quote__step-heading step-title h4">
                            {{ __('2. Vul je gegevens in', 'sage') }}
                            <span class="request-quote__step-edit edit" role="button" tabindex="0">{{ __('wijzig', 'sage') }}</span>
                        </h4>
                        <div class="request-quote__step-panel step-content">
                            <div class="request-quote__info-group info-group">
                                <h4 class="h4">{{ __('2. Vul je gegevens in', 'sage') }}</h4>
                                <div class="request-quote__user-form user-data-form form-group">
                                    <div class="input-group">
                                        <label>{{ __('Voornaam', 'sage') }}<input type="text" name="first-name" placeholder="{{ esc_attr(__('Voornaam', 'sage')) }}" autocomplete="given-name"></label>
                                    </div>
                                    <div class="input-group">
                                        <label>{{ __('Achternaam', 'sage') }}<input type="text" name="last-name" placeholder="{{ esc_attr(__('Achternaam', 'sage')) }}" autocomplete="family-name"></label>
                                    </div>
                                    <div class="input-group">
                                        <label>{{ __('Bedrijfsnaam', 'sage') }}<input type="text" name="company-name" placeholder="{{ esc_attr(__('Bedrijfsnaam', 'sage')) }}" autocomplete="organization"></label>
                                    </div>
                                    <div class="input-group">
                                        <label>{{ __('Telefoonnummer', 'sage') }}<input type="tel" name="phone" placeholder="{{ esc_attr(__('Telefoonnummer', 'sage')) }}" autocomplete="tel"></label>
                                    </div>
                                    <div class="input-group">
                                        <label>{{ __('E-mailadres', 'sage') }}<input type="email" name="email" placeholder="{{ esc_attr(__('E-mailadres', 'sage')) }}" autocomplete="email"></label>
                                    </div>
                                    <div class="input-group wide">
                                        <label>{{ __('Opmerkingen (optioneel)', 'sage') }}<textarea name="notes" rows="4" placeholder="{{ esc_attr(__('Heb je nog bijzonderheden of extra wensen? Laat het ons hier weten.', 'sage')) }}"></textarea></label>
                                    </div>
                                </div>
                            </div>
                            <div class="request-quote__actions button-wrap">
                                <button type="button" class="btn go-to-step" data-step-target="3" disabled>{{ __('Naar het overzicht', 'sage') }}</button>
                            </div>
                        </div>
                    </div>

                    <div class="request-quote__step step step-3 disabled">
                        <h4 class="request-quote__step-heading step-title h4">
                            {{ __('3. Rond je aanvraag af', 'sage') }}
                            <span class="request-quote__step-edit edit" role="button" tabindex="0">{{ __('wijzig', 'sage') }}</span>
                        </h4>
                        <div class="request-quote__step-panel step-content">
                            <div class="request-quote__info-group info-group">
                                <h4 class="h4">{{ __('3. Rond je aanvraag af', 'sage') }}</h4>
                                <div class="request-quote__final-overview final-overview-wrapper"></div>
                                <div class="request-quote__summary summary-wrapper">
                                    <div class="request-quote__data-summary data-summary"></div>
                                    <div class="request-quote__totals totals"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="request-quote__order-cta order-cta disabled">
                        <h4 class="h4">{{ __('Maak een keuze voor offerte of direct bestellen', 'sage') }}</h4>
                        <div class="request-quote__cta-row cta-wrapper">
                            <div class="request-quote__cta request-quote__cta--quote cta">
                                @if($requestQuoteText)
                                <div class="request-quote__cta-intro">{!! apply_filters('the_content', $requestQuoteText) !!}</div>
                                @endif
                                <button type="button" class="btn trigger-quote-cta">{{ __('Vraag vrijblijvend een offerte aan', 'sage') }}</button>
                            </div>
                            <div class="request-quote__cta request-quote__cta--order cta order-now">
                                @if($orderNowText)
                                <div class="request-quote__cta-intro">{!! apply_filters('the_content', $orderNowText) !!}</div>
                                @endif
                                <button type="button" class="btn trigger-order-cta">{{ __('Direct bestellen en afronden', 'sage') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="request-quote__sidebar sidebar" aria-label="{{ esc_attr(__('Overzicht en contact', 'sage')) }}">
                <div class="request-quote__sticky sticky">
                    <div class="request-quote__card card">
                        <h4 class="h4">{{ __('Overzicht', 'sage') }}</h4>
                        <div class="request-quote__overview overview-wrapper">
                            <p class="request-quote__overview-placeholder no-overview">{{ __('Stel je artikel hiernaast samen…', 'sage') }}</p>
                        </div>
                    </div>

                    @if($explanationText)
                    <div class="request-quote__card request-quote__accordion card accordion">
                        @if($explanationTitle)
                        <h4 class="h4">{{ e($explanationTitle) }}</h4>
                        @endif
                        <div class="request-quote__accordion-body text">
                            {!! apply_filters('the_content', $explanationText) !!}
                        </div>
                    </div>
                    @endif

                    @if(is_array($usps) && count($usps) > 0)
                    <div class="request-quote__card card">
                        @if($uspsTitle)
                        <h4 class="h4">{{ e($uspsTitle) }}</h4>
                        @endif
                        @foreach($usps as $uspRow)
                        @if(!empty($uspRow['usp']))
                        <div class="request-quote__usp usp">{{ e($uspRow['usp']) }}</div>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    @if($contactTitle)
                    <div class="request-quote__card request-quote__card--contact card">
                        <h4 class="h4">{{ e($contactTitle) }}</h4>
                        @if($contactText)
                        <div class="request-quote__contact-lead">{!! apply_filters('the_content', $contactText) !!}</div>
                        @endif
                        <div class="request-quote__contact contact">
                            <div class="request-quote__contact-info info">
                                @if($phoneTitle)
                                <div class="request-quote__contact-block phone">
                                    <div>
                                        <strong>{{ e($phoneTitle) }}</strong>
                                        @if($phoneText)
                                        <p>{{ e($phoneText) }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                                @if($emailTitle)
                                <div class="request-quote__contact-block email">
                                    <div>
                                        <strong>{{ e($emailTitle) }}</strong>
                                        @if($emailText)
                                        <p>{{ e($emailText) }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                            @if(!empty($contactImage['url'] ?? null))
                            <div class="request-quote__contact-image image">
                                @if($contactMeta)
                                <span class="request-quote__contact-meta meta">{{ e($contactMeta) }}</span>
                                @endif
                                <img src="{{ esc_url($contactImage['sizes']['large'] ?? $contactImage['url']) }}" alt="{{ esc_attr($contactImage['alt'] ?? '') }}" loading="lazy" decoding="async">
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>

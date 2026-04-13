@php
$post = get_post();
$question_sidebar_cards = isset($question_sidebar_cards) && is_array($question_sidebar_cards) ? $question_sidebar_cards : [];
$question_sidebar_icon_class = isset($question_sidebar_icon_class) ? (string) $question_sidebar_icon_class : 'fa-solid fa-gift';
$question_option_phone = isset($question_option_phone) ? $question_option_phone : null;
$question_option_phone = is_string($question_option_phone) && $question_option_phone !== '' ? $question_option_phone : null;
$feedback_query = isset($_GET['feedback']) ? sanitize_text_field(wp_unslash((string) $_GET['feedback'])) : '';
$breadcrumb_items = [
    ['name' => get_bloginfo('name'), 'url' => home_url('/')],
    ['name' => get_the_title($post), 'url' => get_permalink($post)],
];
$breadcrumb_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array_values(array_map(function ($item, $i) {
        return [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['name'],
            'item' => $item['url'],
        ];
    }, $breadcrumb_items, array_keys($breadcrumb_items))),
];
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

<section class="single-question" @if($feedback_query === 'error') data-open-followup="1" @endif>
    <div class="container">
        <div class="single-question-layout">
            <div class="single-question-main">
                <nav class="single-question-breadcrumb" aria-label="{{ esc_attr(__('Broodkruimelpad', 'sage')) }}">
                    <a href="{{ esc_url($breadcrumb_items[0]['url']) }}">{{ esc_html($breadcrumb_items[0]['name']) }}</a>
                    <span class="single-question-breadcrumb-sep" aria-hidden="true">›</span>
                    <span class="single-question-breadcrumb-current">{{ esc_html($breadcrumb_items[1]['name']) }}</span>
                </nav>

                @if($feedback_query === 'sent')
                <p class="single-question-notice single-question-notice--success" role="status">{{ __('Bedankt. We hebben je bericht ontvangen.', 'sage') }}</p>
                @elseif($feedback_query === 'error')
                <p class="single-question-notice single-question-notice--error" role="alert">{{ __('Controleer de verplichte velden en je e-mailadres.', 'sage') }}</p>
                @endif

                <header class="single-question-header">
                    <h1 class="h2">{{ get_the_title($post) }}</h1>
                </header>
                <div class="single-question-content">
                    {!! apply_filters('the_content', get_the_content(null, false, $post)) !!}
                </div>

                <div class="single-question-feedback">
                    <p class="single-question-feedback-label">{{ __('Heeft dit antwoord jouw probleem opgelost?', 'sage') }}</p>
                    <div class="single-question-feedback-buttons">
                        <button type="button" class="btn-secondary single-question-feedback-btn single-question-feedback-btn--yes">{{ __('Ja', 'sage') }}</button>
                        <button type="button" class="btn single-question-feedback-btn single-question-feedback-btn--no">{{ __('Nee', 'sage') }}</button>
                    </div>
                </div>

                <div class="single-question-followup" id="single-question-followup" hidden>
                    <div class="single-question-followup-panel">
                        <h2 class="single-question-followup-title">{{ __('Neem contact met ons op!', 'sage') }}</h2>
                        <form
                            class="single-question-followup-form"
                            method="post"
                            action="{{ esc_url(admin_url('admin-post.php')) }}">
                            {!! wp_nonce_field('question_feedback', 'question_feedback_nonce', true, false) !!}
                            <input type="hidden" name="action" value="question_feedback">
                            <input type="hidden" name="question_id" value="{{ (int) $post->ID }}">
                            <div class="single-question-hp" aria-hidden="true">
                                <label class="single-question-hp-label">{{ __('Laat dit veld leeg', 'sage') }}<input type="text" name="company" value="" tabindex="-1" autocomplete="off"></label>
                            </div>
                            <div class="single-question-form-group">
                                <label class="single-question-input-group">
                                    <span class="single-question-field-label">{{ __('Voornaam', 'sage') }}</span>
                                    <input type="text" name="first_name" required autocomplete="given-name">
                                </label>
                                <label class="single-question-input-group">
                                    <span class="single-question-field-label">{{ __('Achternaam', 'sage') }}</span>
                                    <input type="text" name="last_name" required autocomplete="family-name">
                                </label>
                                <label class="single-question-input-group single-question-input-group--wide">
                                    <span class="single-question-field-label">{{ __('E-mailadres', 'sage') }}</span>
                                    <input type="email" name="email" required autocomplete="email">
                                </label>
                                <label class="single-question-input-group single-question-input-group--wide">
                                    <span class="single-question-field-label">{{ __('Barcode giftcard of bestelnummer', 'sage') }}</span>
                                    <input type="text" name="reference" autocomplete="off">
                                </label>
                                <label class="single-question-input-group single-question-input-group--wide">
                                    <span class="single-question-field-label">{{ __('Waar kunnen we je mee helpen?', 'sage') }}</span>
                                    <textarea name="message" rows="5" required placeholder="{{ esc_attr(__('Waar kunnen we je mee helpen?', 'sage')) }}"></textarea>
                                </label>
                            </div>
                            <div class="single-question-form-actions">
                                <button type="submit" class="btn">{{ __('Verzenden', 'sage') }}</button>
                            </div>
                        </form>
                    </div>

                    @if($question_option_phone)
                    @php
                    $tel_href = 'tel:' . preg_replace('/\s+/', '', wp_strip_all_tags($question_option_phone));
                    @endphp
                    <div class="single-question-phone">
                        <h3 class="single-question-phone-title">{{ __('Wil je liever bellen?', 'sage') }}</h3>
                        <div class="single-question-phone-row">
                            <span class="single-question-phone-icon" aria-hidden="true">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <div class="single-question-phone-text">
                                <a class="single-question-phone-link" href="{{ esc_url($tel_href) }}">{{ esc_html($question_option_phone) }}</a>
                                <p class="single-question-phone-hours">{{ __('op werkdagen van 9.00 – 17.00 uur', 'sage') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if(count($question_sidebar_cards))
            <aside class="single-question-sidebar" aria-label="{{ esc_attr(__('Gerelateerde vragen', 'sage')) }}">
                <div class="single-question-sidebar-inner">
                    @foreach($question_sidebar_cards as $card)
                    @php
                    $term = $card['term'] ?? null;
                    $card_posts = $card['posts'] ?? [];
                    $grad_id = $term instanceof \WP_Term ? 'single-q-wave-' . (int) $term->term_id : 'single-q-wave-0';
                    @endphp
                    @include('partials.question-sidebar-card', [
                    'term' => $term,
                    'posts' => $card_posts,
                    'grad_id' => $grad_id,
                    'icon_class' => $question_sidebar_icon_class,
                    ])
                    @endforeach
                </div>
            </aside>
            @endif
        </div>
    </div>
</section>

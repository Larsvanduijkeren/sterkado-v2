@php
$intro_title = $fields['intro_title'] ?? null;
$intro_text = $fields['intro_text'] ?? null;
$search_placeholder = $fields['search_placeholder'] ?? null;
$search_button_label = $fields['search_button_label'] ?? null;
$id = $block['anchor'] ?? null;
$faq_term_cards = isset($faq_term_cards) && is_array($faq_term_cards) ? $faq_term_cards : [];
$faq_search_query = isset($faq_search_query) ? (string) $faq_search_query : '';
$faq_search_results = isset($faq_search_results) && is_array($faq_search_results) ? $faq_search_results : [];
$faq_card_icon_class = isset($faq_card_icon_class) ? (string) $faq_card_icon_class : 'fa-solid fa-gift';
$faq_form_action = isset($faq_form_action) ? (string) $faq_form_action : home_url('/');
$is_preview = $is_preview ?? false;
$has_intro = (bool) ($intro_title || $intro_text);
$search_active = $faq_search_query !== '';
$search_placeholder = is_string($search_placeholder) && $search_placeholder !== '' ? $search_placeholder : __('Zoek op onderwerp', 'sage');
$search_button_label = is_string($search_button_label) && $search_button_label !== '' ? $search_button_label : __('Zoeken', 'sage');
$faqSearchBtnClass = \App\acf_button_style_class(
    isset($fields['search_button_style']) && is_string($fields['search_button_style']) && $fields['search_button_style'] !== ''
        ? trim($fields['search_button_style'])
        : null,
    'primary'
);
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="faq">
    <div class="container">
        @if($has_intro)
        <div class="faq-intro content" data-aos="fade-up">
            @if($intro_title)
            <h2>{{ $intro_title }}</h2>
            @endif
            @if($intro_text)
            <div class="faq-intro-text">{!! wp_kses_post($intro_text) !!}</div>
            @endif
        </div>
        @endif

        <form
            class="faq-search"
            method="get"
            action="{{ esc_url($faq_form_action) }}"
            role="search"
            aria-label="{{ esc_attr(__('Zoeken in vragen', 'sage')) }}"
            data-aos="fade-up">
            <label class="faq-search-label" for="faq-search-input-{{ esc_attr($block['id'] ?? 'block') }}">{{ esc_html($search_placeholder) }}</label>
            <input
                id="faq-search-input-{{ esc_attr($block['id'] ?? 'block') }}"
                type="search"
                name="faq_s"
                value="{{ esc_attr($faq_search_query) }}"
                placeholder="{{ esc_attr($search_placeholder) }}"
                autocomplete="off">
            <button type="submit" class="{{ esc_attr($faqSearchBtnClass) }}">{{ $search_button_label }}</button>
        </form>

        @if($search_active)
        <div class="faq-results" data-aos="fade-up">
            <h3 class="faq-results-title">{{ __('Zoekresultaten', 'sage') }}</h3>
            @if(count($faq_search_results))
            <ul class="faq-results-list">
                @foreach($faq_search_results as $resultPost)
                @if($resultPost instanceof \WP_Post)
                <li>
                    <a class="faq-card-link" href="{{ esc_url(get_permalink($resultPost)) }}">
                        <i class="faq-card-link-icon fa-solid fa-angle-right" aria-hidden="true"></i>
                        <span class="faq-card-link-text">{{ get_the_title($resultPost) }}</span>
                    </a>
                </li>
                @endif
                @endforeach
            </ul>
            @else
            <p class="faq-results-empty">{{ __('Geen vragen gevonden voor deze zoekopdracht.', 'sage') }}</p>
            @endif
        </div>
        @endif

        @if(! $search_active && count($faq_term_cards))
        <div class="faq-grid" data-aos="fade-up">
            @foreach($faq_term_cards as $card)
            @php
            $term = $card['term'] ?? null;
            $posts = $card['posts'] ?? [];
            $term = $term instanceof \WP_Term ? $term : null;
            $posts = is_array($posts) ? $posts : [];
            @endphp
            @if($term)
            <article class="faq-card">
                <div class="faq-card-header">
                    <i class="{{ esc_attr($faq_card_icon_class) }} faq-card-header-icon" aria-hidden="true"></i>
                    <h3 class="faq-card-title">{{ esc_html($term->name) }}</h3>
                </div>
                <div class="faq-card-body">
                    @if(count($posts))
                    <ul class="faq-card-list">
                        @foreach($posts as $q)
                        @if($q instanceof \WP_Post)
                        <li>
                            <a class="faq-card-link" href="{{ esc_url(get_permalink($q)) }}">
                                <i class="faq-card-link-icon fa-solid fa-angle-right" aria-hidden="true"></i>
                                <span class="faq-card-link-text">{{ get_the_title($q) }}</span>
                            </a>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    @else
                    <p class="faq-card-empty">{{ __('Nog geen vragen in deze categorie.', 'sage') }}</p>
                    @endif
                </div>
            </article>
            @endif
            @endforeach
        </div>
        @elseif($is_preview)
        <p class="faq-preview-note">{{ __('Voeg vraagcategorieën en vragen toe om de kaarten te vullen.', 'sage') }}</p>
        @endif
    </div>
</section>

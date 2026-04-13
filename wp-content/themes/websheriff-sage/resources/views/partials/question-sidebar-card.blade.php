@php
$term = $term ?? null;
$posts = isset($posts) && is_array($posts) ? $posts : [];
$grad_id = isset($grad_id) ? (string) $grad_id : 'faq-wave-0';
$icon_class = isset($icon_class) ? (string) $icon_class : 'fa-solid fa-gift';
$term = $term instanceof \WP_Term ? $term : null;
@endphp
@if($term)
<article class="single-question-sidebar-card faq-card">
    <div class="faq-card-header">
        <i class="{{ esc_attr($icon_class) }} faq-card-header-icon" aria-hidden="true"></i>
        <h3 class="faq-card-title">{{ esc_html($term->name) }}</h3>
    </div>
    <div class="faq-card-divider" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 12" preserveAspectRatio="none" width="100%" height="12" focusable="false">
            <defs>
                <linearGradient id="{{ esc_attr($grad_id) }}" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="var(--color-brand-purple-secondary)" />
                    <stop offset="55%" stop-color="var(--color-brand-purple-secondary)" />
                    <stop offset="100%" stop-color="var(--color-surface-purple-tint-subtle)" />
                </linearGradient>
            </defs>
            <path fill="url(#{{ esc_attr($grad_id) }})" d="M0,12 L0,4 Q30,-1 60,4 T120,4 L120,12 Z" />
        </svg>
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
        <p class="faq-card-empty">{{ __('Geen andere vragen in deze categorie.', 'sage') }}</p>
        @endif
    </div>
</article>
@endif

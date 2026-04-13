@php
$term = $term ?? null;
$posts = isset($posts) && is_array($posts) ? $posts : [];
$icon_class = isset($icon_class) ? (string) $icon_class : 'fa-solid fa-gift';
$term = $term instanceof \WP_Term ? $term : null;
@endphp
@if($term)
<article class="single-question-sidebar-card faq-card">
    <div class="faq-card-header">
        <i class="{{ esc_attr($icon_class) }} faq-card-header-icon" aria-hidden="true"></i>
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
        <p class="faq-card-empty">{{ __('Geen andere vragen in deze categorie.', 'sage') }}</p>
        @endif
    </div>
</article>
@endif

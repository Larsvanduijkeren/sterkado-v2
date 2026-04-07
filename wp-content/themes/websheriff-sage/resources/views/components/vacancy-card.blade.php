@php
$vacancy = $vacancy ?? null;
if (!$vacancy) {
    return;
}
$summary = get_field('vacancy_summary', $vacancy->ID);
$location = get_field('vacancy_location', $vacancy->ID);
$type = get_field('vacancy_type', $vacancy->ID);
$hours = get_field('vacancy_hours', $vacancy->ID);
$button = get_field('vacancy_button', $vacancy->ID);
$badge = get_field('badge', $vacancy->ID);
$link_url = !empty($button['url']) ? $button['url'] : get_permalink($vacancy);
$link_title = !empty($button['title']) ? $button['title'] : __('Bekijk vacature', 'sage');
$link_target = !empty($button['target']) ? $button['target'] : '_self';
if (empty($summary) && has_excerpt($vacancy)) {
    $summary = get_the_excerpt($vacancy);
}
@endphp

<article class="vacancy-card">
    <a href="{{ $link_url }}" target="{{ $link_target }}" class="vacancy-card__link">
        @if(has_post_thumbnail($vacancy))
        <div class="image">
            {!! get_the_post_thumbnail($vacancy, 'large') !!}
            @if(!empty($badge))
            <span class="badge">{{ $badge }}</span>
            @endif
        </div>
        @endif
        <div class="content">
            <h3 class="title">{{ get_the_title($vacancy) }}</h3>
            @if($location || $type || $hours)
            <div class="vacancy-card__meta" aria-hidden="true">
                @if($location)
                <div class="vacancy-card__meta-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ $location }}</span>
                </div>
                @endif
                @if($type)
                <div class="vacancy-card__meta-item">
                    <i class="fa-solid fa-briefcase"></i>
                    <span>{{ $type }}</span>
                </div>
                @endif
                @if($hours)
                <div class="vacancy-card__meta-item">
                    <i class="fa-solid fa-clock"></i>
                    <span>{{ $hours }}</span>
                </div>
                @endif
            </div>
            @endif
            @if($summary)
            <div class="summary">{!! wpautop($summary) !!}</div>
            @endif
            <span class="btn">{{ $link_title }}</span>
        </div>
    </a>
</article>

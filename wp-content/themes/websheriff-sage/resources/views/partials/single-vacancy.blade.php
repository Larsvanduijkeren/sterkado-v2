@php
$post = get_post();
if (! $post instanceof \WP_Post) {
    return;
}
$heroTitleRaw = function_exists('get_field') ? get_field('hero_title', $post->ID) : null;
$heroTitleRaw = is_string($heroTitleRaw) ? trim($heroTitleRaw) : '';
$heroTitle = $heroTitleRaw !== '' ? $heroTitleRaw : get_the_title($post);
$intro = function_exists('get_field') ? get_field('intro', $post->ID) : null;
$intro = is_string($intro) ? trim($intro) : '';
$ctaLabelRaw = function_exists('get_field') ? get_field('hero_cta_label', $post->ID) : null;
$ctaLabelRaw = is_string($ctaLabelRaw) ? trim($ctaLabelRaw) : '';
$ctaLabel = $ctaLabelRaw !== '' ? $ctaLabelRaw : __('Ontdek de vacature', 'sage');
$teamPhotos = function_exists('get_field') ? get_field('team_photos', $post->ID) : null;
$teamPhotos = is_array($teamPhotos) ? array_values(array_filter($teamPhotos, static function ($item): bool {
    return is_array($item) && (! empty($item['ID']) || ! empty($item['url']));
})) : [];
$formShortcode = function_exists('get_field') ? get_field('form_shortcode', $post->ID) : null;
$formShortcode = is_string($formShortcode) ? trim($formShortcode) : '';
$relatedPosts = isset($vacancy_related_posts) && is_array($vacancy_related_posts) ? $vacancy_related_posts : [];
$relatedHeadingRaw = function_exists('get_field') ? get_field('related_heading', $post->ID) : null;
$relatedHeadingRaw = is_string($relatedHeadingRaw) ? trim($relatedHeadingRaw) : '';
$relatedHeading = $relatedHeadingRaw !== '' ? $relatedHeadingRaw : __('Meer vacatures in deze categorie', 'sage');
$badgeTerm = null;
$terms = get_the_terms($post, \App\Providers\VacancyPostTypeServiceProvider::TAXONOMY_CATEGORY);
if (is_array($terms)) {
    foreach ($terms as $t) {
        if ($t instanceof \WP_Term) {
            $badgeTerm = $t;
            break;
        }
    }
}
$marqueeDuration = count($teamPhotos) > 0 ? (int) max(32, min(95, count($teamPhotos) * 14)) : 40;
@endphp

<section class="single-vacancy">
    <header class="single-vacancy-hero">
        <div class="container">
            <div class="single-vacancy-hero-inner">
                @if($badgeTerm instanceof \WP_Term)
                @php
                $badgeLabel = function_exists('mb_strtoupper')
                    ? mb_strtoupper($badgeTerm->name, 'UTF-8')
                    : strtoupper($badgeTerm->name);
                @endphp
                <p class="single-vacancy-badge">{{ esc_html($badgeLabel) }}</p>
                @endif
                <h1 class="single-vacancy-title h2">{{ esc_html($heroTitle) }}</h1>
                @if($intro !== '')
                <div class="single-vacancy-intro">{!! wp_kses_post($intro) !!}</div>
                @endif
                <p class="single-vacancy-hero-cta-wrap">
                    <a href="#vacature-inhoud" class="btn single-vacancy-hero-cta">
                        {{ esc_html($ctaLabel) }}
                        <span class="single-vacancy-hero-cta-icon" aria-hidden="true"><i class="fa-solid fa-arrow-down"></i></span>
                    </a>
                </p>
            </div>
        </div>
    </header>

    @if(count($teamPhotos))
    <section
        class="gallery-marquee single-vacancy-team"
        aria-label="{{ esc_attr(__('Team', 'sage')) }}">
        <div class="gallery-marquee-rows">
            <div class="gallery-marquee-row gallery-marquee-row-forward" style="--gallery-marquee-duration: {{ $marqueeDuration }}s;">
                <div class="gallery-marquee-scroll">
                    @foreach([0, 1] as $stripIndex)
                    <div class="gallery-marquee-strip" @if($stripIndex === 1) aria-hidden="true" @endif>
                        @foreach($teamPhotos as $img)
                        @php
                        $src = $img['sizes']['large'] ?? $img['url'] ?? '';
                        $alt = $img['alt'] ?? '';
                        @endphp
                        @if($src !== '')
                        <figure class="gallery-marquee-cell">
                            <img src="{{ esc_url($src) }}" alt="{{ esc_attr($alt) }}" loading="{{ $stripIndex === 0 && $loop->first ? 'eager' : 'lazy' }}" decoding="async">
                        </figure>
                        @endif
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <div class="container single-vacancy-body-wrap">
        <div class="single-vacancy-layout">
            <div class="single-vacancy-main" id="vacature-inhoud">
                <div class="single-vacancy-content entry-content">
                    {!! apply_filters('the_content', get_the_content(null, false, $post)) !!}
                </div>
            </div>
            @if($formShortcode !== '')
            <aside class="single-vacancy-sidebar" aria-label="{{ esc_attr(__('Solliciteren', 'sage')) }}">
                <div class="single-vacancy-sidebar-inner">
                    <div class="single-vacancy-form-panel">
                        <h2 class="single-vacancy-form-title">{{ __('Solliciteer direct', 'sage') }}</h2>
                        <div class="single-vacancy-form">
                            {!! do_shortcode($formShortcode) !!}
                        </div>
                    </div>
                </div>
            </aside>
            @endif
        </div>
    </div>

    @if(count($relatedPosts))
    <section class="single-vacancy-related" aria-labelledby="single-vacancy-related-heading">
        <div class="container">
            <h2 id="single-vacancy-related-heading" class="single-vacancy-related-title h2">{{ esc_html($relatedHeading) }}</h2>
            <div class="vacancy-archive-grid vacancy-archive-grid--related">
                @foreach($relatedPosts as $relPost)
                @if($relPost instanceof \WP_Post)
                @include('partials.vacancy-card', ['post' => $relPost])
                @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif
</section>

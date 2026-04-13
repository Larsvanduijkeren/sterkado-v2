@php
$post = $post ?? null;
$post = $post instanceof \WP_Post ? $post : get_post();
if (! $post instanceof \WP_Post) {
    return;
}
$permalink = get_permalink($post);
$hours = function_exists('get_field') ? get_field('hours_label', $post->ID) : null;
$hours = is_string($hours) ? trim($hours) : '';
$terms = get_the_terms($post, \App\Providers\VacancyPostTypeServiceProvider::TAXONOMY_CATEGORY);
$primaryTerm = null;
if (is_array($terms)) {
    foreach ($terms as $t) {
        if ($t instanceof \WP_Term) {
            $primaryTerm = $t;
            break;
        }
    }
}
@endphp
<article class="vacancy-card">
    <div class="vacancy-card-inner">
        @if($primaryTerm instanceof \WP_Term)
        @php
        $termUpper = function_exists('mb_strtoupper')
            ? mb_strtoupper($primaryTerm->name, 'UTF-8')
            : strtoupper($primaryTerm->name);
        @endphp
        <p class="vacancy-card-label">{{ esc_html($termUpper) }}</p>
        @endif
        <h3 class="vacancy-card-title">
            <a href="{{ esc_url($permalink) }}">{{ get_the_title($post) }}</a>
        </h3>
        @if($hours !== '')
        <p class="vacancy-card-meta">{{ esc_html($hours) }}</p>
        @endif
        <div class="vacancy-card-actions">
            <a href="{{ esc_url($permalink) }}" class="btn btn-ghost vacancy-card-btn">{{ __('Bekijk deze vacature', 'sage') }}</a>
        </div>
    </div>
</article>

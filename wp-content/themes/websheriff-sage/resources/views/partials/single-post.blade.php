@php
$summary = get_field('summary');
$reading = \App\reading_time_label($post);
$meta = '<span class="date">' . get_the_date('j F, Y', $post) . '</span><span class="reading-time">' . esc_html($reading) . '</span>';

// BreadcrumbList + BlogPosting rich results
$breadcrumb_items = [
    ['name' => get_bloginfo('name'), 'url' => home_url('/')],
];
$blog_page_id = get_option('page_for_posts');
if ($blog_page_id) {
    $breadcrumb_items[] = ['name' => get_the_title($blog_page_id), 'url' => get_permalink($blog_page_id)];
} else {
    $breadcrumb_items[] = ['name' => __('Nieuws', 'sage'), 'url' => home_url('/nieuws')];
}
$breadcrumb_items[] = ['name' => get_the_title($post), 'url' => get_permalink($post)];
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
$article_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => get_the_title($post),
    'datePublished' => get_the_date('c', $post),
    'dateModified' => get_the_modified_date('c', $post),
    'url' => get_permalink($post),
    'publisher' => ['@id' => home_url('/') . '#organization'],
];
if (has_post_thumbnail($post)) {
    $article_schema['image'] = get_the_post_thumbnail_url($post, 'large');
}
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($article_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

@include('partials.single-hero', [
    'label'      => __('Nieuws', 'sage'),
    'title'      => get_the_title($post),
    'summary'    => $summary ? '<p>' . esc_html($summary) . '</p>' : null,
    'meta'       => $meta,
    'button'     => ['url' => get_option('page_for_posts') ? get_permalink(get_option('page_for_posts')) : home_url('/nieuws'), 'title' => __('Alle artikelen', 'sage')],
    'image'      => $post,
    'back_url'   => null,
    'back_label' => null,
])

<section class="post-content">
    <div class="container">
        <div class="flex-wrapper" data-aos="fade-up">
            <aside>
                <h3 class="h4">{{ __('Op deze pagina', 'sage') }}</h3>
                <div class="index"></div>
                <div class="meta">
                    <span class="date">{{ get_the_date('j F, Y') }}</span>
                    <span class="reading-time">{{ $reading }}</span>
                </div>
            </aside>
            <div class="content">
                {!! apply_filters('the_content', get_the_content(null, false, $post)) !!}
            </div>
        </div>
    </div>
</section>

@php
$permalink = get_permalink($post);
$summary = get_field('summary', $post);
$title = get_the_title($post);
$reading = \App\reading_time_label($post); // helper

// Primary category: Yoast primary, or first assigned category
$primary_cat = null;
$post_id = $post->ID ?? get_the_ID();
if (class_exists('WPSEO_Primary_Term')) {
    $yoast_primary = (new \WPSEO_Primary_Term('category', $post_id))->get_primary_term();
    if ($yoast_primary) {
        $primary_cat = $yoast_primary;
    }
}
if (!$primary_cat) {
    $categories = get_the_terms($post_id, 'category');
    if ($categories && !is_wp_error($categories) && !empty($categories)) {
        $primary_cat = $categories[0];
    }
}
$category_url = $primary_cat ? add_query_arg('archive_cat', $primary_cat->slug, get_permalink((int) get_option('page_for_posts'))) : null;
@endphp

<article class="post-card swiper-slide">
    @if(has_post_thumbnail($post))
    <div class="image">
        @if($primary_cat && $category_url)
        <a href="{{ $category_url }}" class="badge">{{ $primary_cat->name }}</a>
        @endif
        {!! get_the_post_thumbnail($post, 'big') !!}
    </div>
    @endif

    <a href="{{ $permalink }}">
        <span class="content">        
            <span class="label">Nieuws</span>
            <h3>
                {{ $title }}
            </h3>

            @if($summary)
            <p>{{$summary}}</p>
            @endif

            <span class="wrap">
                <span class="reading-time">{{ $reading }}</span>
                <span class="arrow"></span>
            </span>
        </span>
    </a>
</article>
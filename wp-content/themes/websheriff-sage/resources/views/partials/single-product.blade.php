@php
$post = get_post();
$short_description = function_exists('get_field') ? get_field('short_description', $post->ID) : null;
$short_description = is_string($short_description) ? trim($short_description) : '';
$price = function_exists('get_field') ? get_field('price', $post->ID) : null;
$price = is_string($price) ? trim($price) : '';
@endphp
<section class="single-product">
    <div class="container">
        <article class="single-product-inner">
            @if(has_post_thumbnail($post))
            <div class="single-product-media">
                {!! get_the_post_thumbnail($post, 'large', ['loading' => 'eager', 'decoding' => 'async']) !!}
            </div>
            @endif
            <header class="single-product-header">
                <h1 class="h2">{{ get_the_title($post) }}</h1>
            </header>
            @if($price !== '')
            <p class="single-product-price">{{ esc_html($price) }}</p>
            @endif
            @if($short_description !== '')
            <div class="single-product-lead">
                {!! nl2br(e($short_description)) !!}
            </div>
            @endif
            <div class="single-product-content">
                {!! apply_filters('the_content', get_the_content(null, false, $post)) !!}
            </div>
        </article>
    </div>
</section>

@extends('layouts.app')

@section('content')
@php
$notFoundPageId = 0;
if (function_exists('get_field')) {
    $raw = get_field('not_found_page', 'option');
    if (is_object($raw) && isset($raw->ID)) {
        $notFoundPageId = (int) $raw->ID;
    } else {
        $notFoundPageId = (int) $raw;
    }
}
$notFoundPost = null;
if ($notFoundPageId > 0 && get_post_status($notFoundPageId) === 'publish') {
    $candidate = get_post($notFoundPageId);
    $notFoundPost = $candidate instanceof \WP_Post ? $candidate : null;
}
@endphp

@if ($notFoundPost instanceof \WP_Post)
@php
$GLOBALS['post'] = $notFoundPost;
setup_postdata($notFoundPost);
@endphp
{!! apply_filters('the_content', $notFoundPost->post_content) !!}
@php
wp_reset_postdata();
@endphp
@else
@include('partials.page-header')

@if (! have_posts())
<section class="page-not-found">
    <div class="container">
        <div class="content" data-aos="fade-up">
            <h1 class="h2">Pagina niet gevonden</h1>
            <p>Deze pagina bestaat niet (meer). Met de onderstaande knop brengen we je terug naar de hoofdpagina.</p>
            <a href="/" class="btn">Naar home</a>
        </div>
    </div>
</section>
@endif
@endif
@endsection

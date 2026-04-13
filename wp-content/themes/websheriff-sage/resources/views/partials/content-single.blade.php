@php
$post = get_post();
@endphp
<section class="single-simple">
    <div class="container">
        <div class="single-simple__inner">
            <header class="single-simple__header">
                <h1 class="h2">{{ get_the_title($post) }}</h1>
            </header>
            <div class="single-simple__content">
                {!! apply_filters('the_content', get_the_content(null, false, $post)) !!}
            </div>
        </div>
    </div>
</section>

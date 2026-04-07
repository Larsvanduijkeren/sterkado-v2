@php
$label = $label ?? null;
$title = $title ?? null;
$summary = $summary ?? null;
$meta = $meta ?? null;
$button = $button ?? null;
$image = $image ?? null;
$back_url = $back_url ?? null;
$back_label = $back_label ?? null;
$has_image = $image && has_post_thumbnail($image);
@endphp

<section class="single-hero">
    <div class="container">
        <div class="single-hero__grid {{ $has_image ? 'single-hero__grid--has-image' : '' }}">
            <div class="single-hero__content" data-aos="fade-up">
                @if($label)
                <span class="single-hero__label">{{ $label }}</span>
                @endif

                @if($title)
                <h1 class="single-hero__title">{{ $title }}</h1>
                @endif

                @if($meta)
                <div class="single-hero__meta">{!! $meta !!}</div>
                @endif

                @if($summary)
                <div class="single-hero__summary">{!! $summary !!}</div>
                @endif

                @if($button && !empty($button['url']) && !empty($button['title']))
                <a href="{{ $button['url'] }}" target="{{ $button['target'] ?? '_self' }}" class="btn">{{ $button['title'] }}</a>
                @endif

                @if($back_url && $back_label)
                <p class="single-hero__back">
                    <a href="{{ $back_url }}" class="btn btn-ghost">{{ $back_label }}</a>
                </p>
                @endif
            </div>

            @if($has_image)
            <div class="single-hero__image" data-aos="fade-up">
                {!! get_the_post_thumbnail($image, 'large') !!}
            </div>
            @endif
        </div>
    </div>
</section>

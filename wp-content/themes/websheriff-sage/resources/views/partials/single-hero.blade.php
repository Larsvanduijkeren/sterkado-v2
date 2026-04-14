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

<section class="single-hero{{ $has_image ? ' single-hero--has-image' : '' }}">
    <div class="single-hero__track">
    <div class="single-hero__layout">
        <div class="single-hero__content" data-aos="fade-up">
            <div class="single-hero__content-inner">
                @if(! is_front_page())
                <div class="single-hero__breadcrumb">
                    {!! do_shortcode('[rank_math_breadcrumb]') !!}
                </div>
                @endif
                @if($label)
                <span class="label">{{ $label }}</span>
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
                <a
                    href="{{ esc_url($button['url']) }}"
                    target="{{ esc_attr($button['target'] ?? '_self') }}"
                    class="btn"
                    @if(($button['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif>{{ $button['title'] }}</a>
                @endif

                @if($back_url && $back_label)
                <p class="single-hero__back">
                    <a href="{{ esc_url($back_url) }}" class="btn btn-ghost">{{ $back_label }}</a>
                </p>
                @endif
            </div>
        </div>

        @if($has_image)
        <div class="single-hero__image" data-aos="fade-up">
            {!! get_the_post_thumbnail($image, 'large', [
                'loading' => 'eager',
                'decoding' => 'async',
                'sizes' => '(max-width: 991px) 100vw, min(400px, 36vw)',
            ]) !!}
        </div>
        @endif
    </div>
    </div>
</section>

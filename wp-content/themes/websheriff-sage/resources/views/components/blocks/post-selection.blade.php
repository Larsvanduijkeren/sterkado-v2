@php
$intro_title = $fields['intro_title'] ?? null;
$intro_button = $fields['intro_button'] ?? null;
$intro_button = is_array($intro_button) ? $intro_button : [];
$intro_url = $intro_button['url'] ?? '';
$intro_btn_title = $intro_button['title'] ?? '';
$intro_target = $intro_button['target'] ?? '_self';
$background_color = $fields['background_color'] ?? 'white';
$add_waves = filter_var($fields['add_waves'] ?? false, FILTER_VALIDATE_BOOLEAN);
$id = $block['anchor'] ?? null;
$posts = isset($posts) && is_array($posts) ? $posts : [];
$is_preview = $is_preview ?? false;
$has_intro = (bool) ($intro_title || ($intro_url !== '' && $intro_btn_title !== ''));
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="post-selection bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        @if($has_intro)
        <div class="post-selection-intro" data-aos="fade-up">
            @if($intro_title)
            <h2 class="post-selection-intro-title">{{ $intro_title }}</h2>
            @endif
            @if($intro_url !== '' && $intro_btn_title !== '')
            <div class="post-selection-intro-actions">
                <a
                    href="{{ esc_url($intro_url) }}"
                    class="btn-secondary"
                    target="{{ esc_attr($intro_target) }}"
                    @if($intro_target === '_blank') rel="noopener noreferrer" @endif>{{ $intro_btn_title }}</a>
            </div>
            @endif
        </div>
        @endif

        @if(count($posts))
        <div class="post-selection-slider-wrap" data-aos="fade-up">
            <div class="swiper post-selection-swiper">
                <div class="swiper-wrapper">
                    @foreach($posts as $cardPost)
                    @php
                    $permalink = get_permalink($cardPost);
                    $card_title = get_the_title($cardPost);
                    $summary = function_exists('get_field') ? get_field('summary', $cardPost->ID) : null;
                    $excerpt_raw = is_string($summary) && $summary !== '' ? $summary : get_the_excerpt($cardPost);
                    $excerpt = wp_strip_all_tags((string) $excerpt_raw);
                    $date_display = get_the_date('', $cardPost);
                    @endphp
                    <div class="swiper-slide">
                        <a
                            href="{{ esc_url($permalink) }}"
                            class="post-selection-card"
                            @if($card_title !== '') aria-label="{{ esc_attr(sprintf(__('Read article: %s', 'sage'), $card_title)) }}" @endif>
                            @if(has_post_thumbnail($cardPost))
                            <div class="post-selection-card-media">
                                {!! get_the_post_thumbnail($cardPost, 'large', ['loading' => 'lazy', 'decoding' => 'async']) !!}
                            </div>
                            @endif
                            <div class="post-selection-card-body">
                                @if($card_title !== '')
                                <h3 class="post-selection-card-title">{{ $card_title }}</h3>
                                @endif
                                @if($date_display !== '')
                                <p class="post-selection-card-meta">
                                    <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                                    <span>{{ $date_display }}</span>
                                </p>
                                @endif
                                @if($excerpt !== '')
                                <p class="post-selection-card-excerpt">{{ $excerpt }}</p>
                                @endif
                                <span class="post-selection-card-more">
                                    <span class="post-selection-card-more-text">{{ __('Lees meer', 'sage') }}</span>
                                    <span class="post-selection-card-more-arrow" aria-hidden="true"></span>
                                </span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="post-selection-pagination swiper-pagination"></div>
        </div>
        @elseif($is_preview)
        <p class="post-selection-empty">{{ __('Select posts in the block fields to show slides.', 'sage') }}</p>
        @endif
    </div>
</section>

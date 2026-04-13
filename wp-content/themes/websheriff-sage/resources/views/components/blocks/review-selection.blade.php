@php
$intro_label = $fields['intro_label'] ?? null;
$intro_title = $fields['intro_title'] ?? null;
$intro_text = $fields['intro_text'] ?? null;
$intro_buttons = $fields['intro_buttons'] ?? null;
$background_color = $fields['background_color'] ?? 'grey';
$add_waves = filter_var($fields['add_waves'] ?? false, FILTER_VALIDATE_BOOLEAN);
$id = $block['anchor'] ?? null;
$reviews = isset($reviews) && is_array($reviews) ? $reviews : [];
$is_preview = $is_preview ?? false;
$has_intro = (bool) ($intro_label || $intro_title || $intro_text || (is_array($intro_buttons) && count($intro_buttons)));
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="review-selection bg-{{ $background_color }}{{ $has_intro ? ' has-intro' : '' }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        @if($has_intro)
        <div class="intro content" data-aos="fade-up">
            @if($intro_label)
            <span class="label">{{ $intro_label }}</span>
            @endif
            @if($intro_title)
            <h2>{{ $intro_title }}</h2>
            @endif
            @if($intro_text)
            <div class="intro-text">{!! wp_kses_post($intro_text) !!}</div>
            @endif
            @if(is_array($intro_buttons) && count($intro_buttons))
            <div class="buttons">
                @foreach($intro_buttons as $button)
                @php
                $button_obj = $button['button'] ?? $button;
                $b_url = $button_obj['url'] ?? null;
                $b_title = $button_obj['title'] ?? null;
                $b_target = $button_obj['target'] ?? '_self';
                @endphp
                @if($b_url && $b_title)
                <a
                    href="{{ esc_url($b_url) }}"
                    target="{{ esc_attr($b_target) }}"
                    class="{{ $loop->first ? 'btn' : 'btn btn-ghost' }}"
                    @if(($b_target ?? '_self') === '_blank') rel="noopener noreferrer" @endif>{{ $b_title }}</a>
                @endif
                @endforeach
            </div>
            @endif
        </div>
        @endif

        @if(count($reviews))
        <div class="review-selection-slider-wrap" data-aos="fade-up">
            <div class="swiper review-selection-swiper">
                <div class="swiper-wrapper">
                    @foreach($reviews as $review)
                    @if($review instanceof \WP_Post)
                    @php
                    $quote_raw = function_exists('get_field') ? get_field('quote', $review->ID) : null;
                    $quote = is_string($quote_raw) ? trim($quote_raw) : '';
                    $name_raw = function_exists('get_field') ? get_field('reviewer_name', $review->ID) : null;
                    $name = is_string($name_raw) && trim($name_raw) !== '' ? trim($name_raw) : get_the_title($review);
                    $subtitle_raw = function_exists('get_field') ? get_field('reviewer_subtitle', $review->ID) : null;
                    $subtitle = is_string($subtitle_raw) ? trim($subtitle_raw) : '';
                    @endphp
                    <div class="swiper-slide">
                        <article class="review-selection-card">
                            @if($quote !== '')
                            <blockquote class="review-selection-quote">
                                <p>“{{ esc_html($quote) }}”</p>
                            </blockquote>
                            @endif
                            <footer class="review-selection-footer">
                                @if(has_post_thumbnail($review))
                                <div class="review-selection-avatar">
                                    {!! get_the_post_thumbnail($review, 'thumbnail', ['loading' => 'lazy', 'decoding' => 'async']) !!}
                                </div>
                                @else
                                <div class="review-selection-avatar review-selection-avatar--empty" aria-hidden="true"></div>
                                @endif
                                <div class="review-selection-meta">
                                    @if($name !== '')
                                    <p class="review-selection-name">{{ esc_html($name) }}</p>
                                    @endif
                                    @if($subtitle !== '')
                                    <p class="review-selection-subtitle">{{ esc_html($subtitle) }}</p>
                                    @endif
                                </div>
                            </footer>
                        </article>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="review-selection-pagination swiper-pagination"></div>
        </div>
        @elseif($is_preview)
        <p class="review-selection-empty">{{ __('Select reviews in the block fields to show the slider.', 'sage') }}</p>
        @endif
    </div>
</section>

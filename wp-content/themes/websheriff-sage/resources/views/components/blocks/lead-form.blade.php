@php
$id = $block['anchor'] ?? null;
$back_link = $fields['back_link'] ?? null;
$back_link = is_array($back_link) ? $back_link : [];
$back_url = isset($back_link['url']) ? trim((string) $back_link['url']) : '';
$back_title = isset($back_link['title']) ? trim((string) $back_link['title']) : '';
$back_target = isset($back_link['target']) ? (string) $back_link['target'] : '_self';
if ($back_title === '') {
    $back_title = __('Ga terug', 'sage');
}
$title = $fields['title'] ?? null;
$lead_intro = $fields['lead_intro'] ?? null;
$steps_heading = $fields['steps_heading'] ?? null;
$steps_heading = is_string($steps_heading) ? trim($steps_heading) : '';
$steps_raw = $fields['steps'] ?? null;
$steps = is_array($steps_raw) ? $steps_raw : [];
$features_title = $fields['features_title'] ?? null;
$features_title = is_string($features_title) && trim($features_title) !== '' ? trim($features_title) : __('Waarom Sterkado?', 'sage');
$features_raw = $fields['features'] ?? null;
$features = is_array($features_raw) ? $features_raw : [];
$testimonial_quote = $fields['testimonial_quote'] ?? null;
$testimonial_name = $fields['testimonial_name'] ?? null;
$testimonial_role = $fields['testimonial_role'] ?? null;
$testimonial_image = $fields['testimonial_image'] ?? null;
$testimonial_image = is_array($testimonial_image) ? $testimonial_image : [];
$footer_note = $fields['footer_note'] ?? null;
$form_shortcode = $fields['form_shortcode'] ?? null;
$form_shortcode = is_string($form_shortcode) ? trim($form_shortcode) : '';
$is_preview = $is_preview ?? false;
$has_testimonial = (is_string($testimonial_quote) && trim($testimonial_quote) !== '')
    || (is_string($testimonial_name) && trim($testimonial_name) !== '')
    || (is_string($testimonial_role) && trim($testimonial_role) !== '')
    || (! empty($testimonial_image['url'] ?? $testimonial_image['ID'] ?? null));
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="lead-form">
    <div class="container">
        <div class="lead-form-layout">
            <div class="lead-form-main content" data-aos="fade-up">
                @if($back_url !== '')
                <p class="lead-form-back-wrap">
                    <a
                        href="{{ esc_url($back_url) }}"
                        class="lead-form-back"
                        target="{{ esc_attr($back_target) }}"
                        @if($back_target === '_blank') rel="noopener noreferrer" @endif>
                        <span class="lead-form-back-icon" aria-hidden="true">←</span>
                        {{ esc_html($back_title) }}
                    </a>
                </p>
                @endif

                @if($title)
                <h2 class="lead-form-title">{{ $title }}</h2>
                @endif

                @if($lead_intro)
                <div class="lead-form-intro">{!! wp_kses_post($lead_intro) !!}</div>
                @endif

                @if(count($steps))
                <div class="lead-form-steps">
                    <h3 class="lead-form-steps-title h4">{{ esc_html($steps_heading !== '' ? $steps_heading : __('Wat gaat er gebeuren?', 'sage')) }}</h3>
                    @foreach($steps as $row)
                    @php
                    $step_text = $row['text'] ?? null;
                    $step_text = is_string($step_text) ? trim($step_text) : '';
                    @endphp
                    @if($step_text !== '')
                    <div class="lead-form-step">
                        <span class="lead-form-step-num" aria-hidden="true">{{ $loop->iteration }}</span>
                        <div class="lead-form-step-body">{!! nl2br(e($step_text)) !!}</div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @endif

                @if(count($features))
                <div class="lead-form-features">
                    <h3 class="lead-form-features-title h4">{{ esc_html($features_title) }}</h3>
                    <ul class="lead-form-feature-list">
                        @foreach($features as $row)
                        @php
                        $line = $row['text'] ?? null;
                        $line = is_string($line) ? trim(wp_strip_all_tags($line)) : '';
                        @endphp
                        @if($line !== '')
                        <li class="lead-form-feature">
                            <i class="fa-solid fa-check lead-form-feature-icon" aria-hidden="true"></i>
                            <span>{{ esc_html($line) }}</span>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($has_testimonial)
                <div class="lead-form-testimonial">
                    @if(! empty($testimonial_image['url'] ?? '') || ! empty($testimonial_image['ID'] ?? null))
                    <div class="lead-form-testimonial-media">
                        <img
                            src="{{ esc_url($testimonial_image['sizes']['medium_large'] ?? $testimonial_image['sizes']['medium'] ?? $testimonial_image['url'] ?? '') }}"
                            alt="{{ esc_attr($testimonial_image['alt'] ?? '') }}"
                            loading="lazy"
                            decoding="async">
                    </div>
                    @endif
                    <div class="lead-form-testimonial-bubble">
                        @if(is_string($testimonial_quote) && trim($testimonial_quote) !== '')
                        <blockquote class="lead-form-testimonial-quote">
                            <p>{{ esc_html(trim($testimonial_quote)) }}</p>
                        </blockquote>
                        @endif
                        @if((is_string($testimonial_name) && trim($testimonial_name) !== '') || (is_string($testimonial_role) && trim($testimonial_role) !== ''))
                        <p class="lead-form-testimonial-meta">
                            @if(is_string($testimonial_name) && trim($testimonial_name) !== '')
                            <span class="lead-form-testimonial-name">{{ esc_html(trim($testimonial_name)) }}</span>
                            @endif
                            @if(is_string($testimonial_role) && trim($testimonial_role) !== '')
                            <span class="lead-form-testimonial-role">{{ esc_html(trim($testimonial_role)) }}</span>
                            @endif
                        </p>
                        @endif
                    </div>
                </div>
                @endif

                @if($footer_note)
                <div class="lead-form-footer-note">{!! wp_kses_post($footer_note) !!}</div>
                @endif
            </div>

            <aside class="lead-form-aside" data-aos="fade-up">
                <div class="lead-form-panel">
                    @if($form_shortcode !== '')
                    <div class="lead-form-shortcode">
                        {!! do_shortcode($form_shortcode) !!}
                    </div>
                    @elseif($is_preview)
                    <p class="lead-form-shortcode-placeholder">{{ __('Add a form shortcode in the block fields.', 'sage') }}</p>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>

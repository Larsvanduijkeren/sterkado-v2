@php
$id = $block['anchor'] ?? null;
$title = $fields['title'] ?? null;
$intro = $fields['intro'] ?? null;
$form_shortcode = $fields['form_shortcode'] ?? null;
$form_shortcode = is_string($form_shortcode) ? trim($form_shortcode) : '';
$contact_methods = isset($contact_methods) && is_array($contact_methods) ? $contact_methods : [];
$is_preview = $is_preview ?? false;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="contact">
    <div class="container">
        <div class="contact-layout">
            <div class="contact-main content" data-aos="fade-up">
                @if($title)
                <h2 class="contact-title">{{ $title }}</h2>
                @endif
                @if($intro)
                <div class="contact-intro">{!! wp_kses_post($intro) !!}</div>
                @endif

                @if(count($contact_methods))
                <ul class="contact-methods">
                    @foreach($contact_methods as $method)
                    @php
                    $icon_class = isset($method['icon_class']) ? (string) $method['icon_class'] : 'fa-solid fa-circle-info';
                    $line_primary = isset($method['line_primary']) ? (string) $method['line_primary'] : '';
                    $line_secondary = isset($method['line_secondary']) ? (string) $method['line_secondary'] : '';
                    $link = isset($method['link']) && is_array($method['link']) ? $method['link'] : [];
                    $link_url = isset($link['url']) ? trim((string) $link['url']) : '';
                    $link_target = isset($link['target']) ? (string) $link['target'] : '_self';
                    $aria = $line_primary !== '' ? $line_primary : ($link['title'] ?? '');
                    @endphp
                    <li class="contact-method">
                        @if($link_url !== '')
                        <a
                            class="contact-method-inner contact-method-inner--link"
                            href="{{ esc_url($link_url) }}"
                            target="{{ esc_attr($link_target) }}"
                            @if($link_target === '_blank') rel="noopener noreferrer" @endif
                            @if($aria !== '') aria-label="{{ esc_attr($aria) }}" @endif>
                            <span class="contact-method-icon" aria-hidden="true">
                                <i class="{{ esc_attr($icon_class) }}"></i>
                            </span>
                            <span class="contact-method-body">
                                @if($line_primary !== '')
                                <span class="contact-method-primary">{{ esc_html($line_primary) }}</span>
                                @endif
                                @if($line_secondary !== '')
                                <span class="contact-method-secondary">{{ esc_html($line_secondary) }}</span>
                                @endif
                            </span>
                        </a>
                        @else
                        <div class="contact-method-inner">
                            <span class="contact-method-icon" aria-hidden="true">
                                <i class="{{ esc_attr($icon_class) }}"></i>
                            </span>
                            <span class="contact-method-body">
                                @if($line_primary !== '')
                                <span class="contact-method-primary">{{ esc_html($line_primary) }}</span>
                                @endif
                                @if($line_secondary !== '')
                                <span class="contact-method-secondary">{{ esc_html($line_secondary) }}</span>
                                @endif
                            </span>
                        </div>
                        @endif
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>

            <aside class="contact-aside" data-aos="fade-up">
                <div class="contact-panel">
                    @if($form_shortcode !== '')
                    <div class="contact-shortcode">
                        {!! do_shortcode($form_shortcode) !!}
                    </div>
                    @elseif($is_preview)
                    <p class="contact-shortcode-placeholder">{{ __('Add a form shortcode in the block fields.', 'sage') }}</p>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>

@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$features = $fields['features'] ?? null;
$buttons = $fields['buttons'] ?? null;
$form_title = $fields['form_title'] ?? null;
$form_text = $fields['form_text'] ?? null;
$form_shortcode = $fields['form_shortcode'] ?? null;
$contact_cards = $fields['contact_cards'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = $fields['add_waves'] ?? false;

$id = $block['anchor'] ?? null;
@endphp

<section
    @if($id) id="{{ $id }}" @endif
    class="contact bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        <div class="card">
            <div class="contact-grid" data-aos="fade-up">
                <div class="contact-content">
                    @if($label)
                    <span class="label">{{ $label }}</span>
                    @endif

                    @if($title)
                    <h2>{{ $title }}</h2>
                    @endif

                    @if($text)
                    <div class="contact-text">
                        {!! $text !!}
                    </div>
                    @endif

                    @if($features)
                    <div class="features">
                        @foreach($features as $feature)
                        @php
                        $feat_icon = $feature['icon'] ?? null;
                        $feat_title = $feature['title'] ?? null;
                        $feat_text = $feature['text'] ?? null;
                        @endphp
                        @if($feat_title || $feat_text)
                        <div class="feature-item">
                            @if($feat_icon)
                            <span class="feature-item-icon" aria-hidden="true">
                                <i class="fa-solid {{ $feat_icon }}"></i>
                            </span>
                            @endif
                            <div class="feature-item-body">
                                @if($feat_title)
                                <h3 class="feature-item-title">{{ $feat_title }}</h3>
                                @endif
                                @if($feat_text)
                                <div class="feature-item-text">{!! $feat_text !!}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    @if($buttons && count($buttons) > 0)
                    <div class="buttons">
                        @foreach($buttons as $button)
                        @php
                        $button_obj = $button['button'] ?? $button;
                        $url = $button_obj['url'] ?? null;
                        $button_title = $button_obj['title'] ?? null;
                        $target = $button_obj['target'] ?? '_self';
                        @endphp
                        @if($url && $button_title)
                        <a
                            href="{{ esc_url($url) }}"
                            target="{{ esc_attr($target) }}"
                            class="{{ $loop->first ? 'btn' : 'btn btn-ghost' }}">
                            {{ $button_title }}
                        </a>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                @if(!is_admin() && $form_shortcode)
                <div class="contact-form">
                    @if($form_title)
                    <h3 class="h4">{{ $form_title }}</h3>
                    @endif
                    @if($form_text)
                    <div class="form-text">
                        {!! $form_text !!}
                    </div>
                    @endif
                    {!! do_shortcode($form_shortcode) !!}
                </div>
                @endif
            </div>

            @if($contact_cards && count($contact_cards) > 0)
            <div class="contact-cards">
                @foreach($contact_cards as $card)
                @php
                $card_icon = $card['icon'] ?? null;
                $card_title = $card['title'] ?? null;
                $card_text = $card['text'] ?? null;
                $card_btn = $card['button'] ?? null;
                $card_url = $card_btn['url'] ?? null;
                $card_btn_title = $card_btn['title'] ?? null;
                $card_target = $card_btn['target'] ?? '_self';
                @endphp
                <div class="contact-card" data-aos="fade-up">
                    @if($card_icon)
                    <span class="contact-card-icon" aria-hidden="true">
                        <i class="fa-solid {{ $card_icon }}"></i>
                    </span>
                    @endif
                    @if($card_title)
                    <h3 class="contact-card-title">{{ $card_title }}</h3>
                    @endif
                    @if($card_text)
                    <div class="contact-card-text">{!! nl2br(esc_html($card_text)) !!}</div>
                    @endif
                    @if($card_url && $card_btn_title)
                    <a href="{{ esc_url($card_url) }}" target="{{ $card_target }}" class="contact-card-button btn btn-ghost">{{ $card_btn_title }}</a>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>

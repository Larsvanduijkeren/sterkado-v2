@php
$intro_title = $fields['intro_title'] ?? null;
$intro_text = $fields['intro_text'] ?? null;
$id = $block['anchor'] ?? null;
$is_preview = $is_preview ?? false;
$product_cards = is_array($product_cards ?? null) ? $product_cards : [];
$product_secondary_button = is_array($product_secondary_button ?? null) ? $product_secondary_button : [];
$product_primary_label = isset($product_primary_label) ? (string) $product_primary_label : '';
$sec_url = $product_secondary_button['url'] ?? '';
$sec_title = $product_secondary_button['title'] ?? '';
$sec_target = $product_secondary_button['target'] ?? '_self';
$has_secondary = $sec_url !== '' && $sec_title !== '';
$productPrimaryBtnClass = \App\acf_button_style_class(
    isset($fields['primary_button_style']) && is_string($fields['primary_button_style']) && $fields['primary_button_style'] !== ''
        ? trim($fields['primary_button_style'])
        : null,
    'primary'
);
$productSecondaryBtnClass = \App\acf_button_style_class(
    isset($fields['secondary_button_style']) && is_string($fields['secondary_button_style']) && $fields['secondary_button_style'] !== ''
        ? trim($fields['secondary_button_style'])
        : null,
    'tertiary'
);
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="product-selection">
    <div class="container">
        @if($intro_title || $intro_text)
        <div class="product-selection-intro content" data-aos="fade-up">
            @if($intro_title)
            <h2>{{ $intro_title }}</h2>
            @endif
            @if($intro_text)
            <div class="product-selection-intro-text">{!! wp_kses_post($intro_text) !!}</div>
            @endif
        </div>
        @endif

        @if(count($product_cards))
        <div class="product-selection-grid" data-aos="fade-up">
            @foreach($product_cards as $card)
            <article class="product-selection-card">
                @if(!empty($card['thumb_id']))
                <div class="product-selection-card-media">
                    {!! get_the_post_thumbnail((int) $card['id'], 'large', ['loading' => 'lazy', 'decoding' => 'async', 'alt' => $card['title']]) !!}
                </div>
                @endif
                <div class="product-selection-card-body">
                    @if($card['title'] !== '')
                    <h3 class="product-selection-card-title">{{ $card['title'] }}</h3>
                    @endif
                    @if($card['short_description'] !== '')
                    <div class="product-selection-card-text">
                        {!! nl2br(e($card['short_description'])) !!}
                    </div>
                    @endif
                    @if($card['price'] !== '')
                    <p class="product-selection-card-price">{{ esc_html($card['price']) }}</p>
                    @endif
                    <div class="buttons">
                        @if($card['permalink'] !== '' && $product_primary_label !== '')
                        <a href="{{ esc_url($card['permalink']) }}" class="{{ esc_attr($productPrimaryBtnClass) }}">{{ $product_primary_label }}</a>
                        @endif
                        @if($has_secondary)
                        <a
                            href="{{ esc_url($sec_url) }}"
                            class="{{ esc_attr($productSecondaryBtnClass) }}"
                            target="{{ esc_attr($sec_target) }}"
                            @if($sec_target === '_blank') rel="noopener noreferrer" @endif>{{ $sec_title }}</a>
                        @endif
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        @elseif($is_preview)
        <p class="product-selection-empty">{{ __('Choose “Manual selection” and pick products, or “Category”, a product category, and “Maximum products”.', 'sage') }}</p>
        @endif
    </div>
</section>

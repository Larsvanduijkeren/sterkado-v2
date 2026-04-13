@php
$id = $block['anchor'] ?? null;
$title = $fields['title'] ?? null;
$address_heading = $fields['address_heading'] ?? null;
$address_heading = is_string($address_heading) ? trim($address_heading) : '';
$gegevens_heading = $fields['gegevens_heading'] ?? null;
$gegevens_heading = is_string($gegevens_heading) ? trim($gegevens_heading) : '';
$company_name = $fields['company_name'] ?? null;
$address_line_1 = $fields['address_line_1'] ?? null;
$address_line_2 = $fields['address_line_2'] ?? null;
$address_note = $fields['address_note'] ?? null;
$gegevens = $fields['gegevens'] ?? null;
$map_embed = $fields['map_embed'] ?? null;
$map_embed = is_string($map_embed) ? trim($map_embed) : '';
$office_map_social_rows = isset($office_map_social_rows) && is_array($office_map_social_rows) ? $office_map_social_rows : [];
$is_preview = $is_preview ?? false;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="office-map">
    <div class="container">
        <div class="office-map-layout">
            <div class="office-map-main content" data-aos="fade-up">
                @if($title)
                <h2 class="office-map-title">{{ $title }}</h2>
                @endif

                <div class="office-map-block">
                    <h3 class="office-map-block-heading">
                        <i class="fa-solid fa-location-dot office-map-block-icon" aria-hidden="true"></i>
                        <span>{{ esc_html($address_heading !== '' ? $address_heading : __('Adresgegevens', 'sage')) }}</span>
                    </h3>
                    <div class="office-map-block-body">
                        @if(is_string($company_name) && trim($company_name) !== '')
                        <p class="office-map-company">{{ esc_html(trim($company_name)) }}</p>
                        @endif
                        @if(is_string($address_line_1) && trim($address_line_1) !== '')
                        <p class="office-map-line">{{ esc_html(trim($address_line_1)) }}</p>
                        @endif
                        @if(is_string($address_line_2) && trim($address_line_2) !== '')
                        <p class="office-map-line">{{ esc_html(trim($address_line_2)) }}</p>
                        @endif
                        @if(is_string($address_note) && trim($address_note) !== '')
                        <p class="office-map-note">{{ esc_html(trim($address_note)) }}</p>
                        @endif
                    </div>
                </div>

                @if(is_string($gegevens) && trim($gegevens) !== '')
                <div class="office-map-block office-map-block--gegevens">
                    <h3 class="office-map-block-heading">
                        <i class="fa-solid fa-id-card office-map-block-icon" aria-hidden="true"></i>
                        <span>{{ esc_html($gegevens_heading !== '' ? $gegevens_heading : __('Gegevens', 'sage')) }}</span>
                    </h3>
                    <div class="office-map-gegevens">{!! wp_kses_post($gegevens) !!}</div>
                </div>
                @endif

                @if(count($office_map_social_rows))
                <div class="office-map-social" aria-label="{{ esc_attr(__('Social media', 'sage')) }}">
                    @foreach($office_map_social_rows as $row)
                    <a
                        href="{{ esc_url($row['url']) }}"
                        class="office-map-social-link"
                        target="{{ esc_attr($row['target']) }}"
                        rel="noopener noreferrer"
                        aria-label="{{ esc_attr($row['aria_label']) }}">
                        <i class="{{ esc_attr($row['icon_class']) }}" aria-hidden="true"></i>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="office-map-map" data-aos="fade-up">
                @if($map_embed !== '')
                <div class="office-map-embed">
                    {!! \App\office_map_sanitize_embed($map_embed) !!}
                </div>
                @elseif($is_preview)
                <p class="office-map-placeholder">{{ __('Paste the Google Maps iframe embed in the block fields.', 'sage') }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

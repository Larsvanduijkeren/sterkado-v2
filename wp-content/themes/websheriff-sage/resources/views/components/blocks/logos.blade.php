@php
$heading = $fields['heading'] ?? null;
$logos = $fields['logos'] ?? null;
$id = $block['anchor'] ?? null;
$logoRows = is_array($logos) ? $logos : [];
@endphp
<section @if($id) id="{{ $id }}" @endif class="logos">
    <div class="container">
        <div class="logos__card" data-aos="fade-up">
            @if($heading)
            <h2 class="logos__heading">{{ e($heading) }}</h2>
            @endif
            @if(!empty($logoRows))
            <ul class="logos__grid">
                @foreach($logoRows as $item)
                @php
                $image = $item['image'] ?? null;
                $link = $item['link'] ?? null;
                $url = is_array($link) ? ($link['url'] ?? null) : null;
                $target = is_array($link) ? ($link['target'] ?? '_self') : '_self';
                $imgW = isset($image['width']) ? (int) $image['width'] : 0;
                $imgH = isset($image['height']) ? (int) $image['height'] : 0;
                @endphp
                @if($image)
                <li class="logos__cell">
                    @if($url)
                    <a href="{{ esc_url($url) }}" class="logos__link" target="{{ esc_attr($target) }}" rel="{{ $target === '_blank' ? 'noopener noreferrer' : '' }}">
                        <img src="{{ esc_url($image['sizes']['medium'] ?? $image['url'] ?? '') }}" alt="{{ esc_attr($image['alt'] ?? '') }}" class="logos__img" loading="lazy" decoding="async" @if($imgW > 0) width="{{ $imgW }}" @endif @if($imgH > 0) height="{{ $imgH }}" @endif>
                    </a>
                    @else
                    <span class="logos__figure">
                        <img src="{{ esc_url($image['sizes']['medium'] ?? $image['url'] ?? '') }}" alt="{{ esc_attr($image['alt'] ?? '') }}" class="logos__img" loading="lazy" decoding="async" @if($imgW > 0) width="{{ $imgW }}" @endif @if($imgH > 0) height="{{ $imgH }}" @endif>
                    </span>
                    @endif
                </li>
                @endif
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</section>

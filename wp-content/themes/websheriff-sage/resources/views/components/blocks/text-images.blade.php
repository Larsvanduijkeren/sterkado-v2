@php
$order = $fields['order'] ?? null;
$small_images = $fields['small_images'] ?? null;
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$images = $fields['images'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = $fields['add_waves'] ?? false;

$id = $block['anchor'] ?? null;
$is_preview = $is_preview ?? false;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="text-images {{ $order }} {{ $small_images ? 'small-images' : '' }} bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        <div class="flex-wrapper">
            <div class="content" data-aos="fade-up">
                @if($label)
                <span class="label">{{$label}}</span>
                @endif

                @if($title)
                <h2>{{$title}}</h2>
                @endif

                @if($text)
                {!! $text !!}
                @endif

                @if($buttons)
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
                        href="{{ $url }}"
                        target="{{ $target }}"
                        class="{{ $loop->first ? 'btn' : 'btn btn-ghost' }}">
                        {{ $button_title }}
                    </a>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>

            @if($images)
            <div class="images" data-aos="fade-up">
                @php
                $show_slider = !$is_preview && count($images) > 1;
                $display_images = $is_preview ? array_slice($images, 0, 1) : $images;
                @endphp
                @if($show_slider)
                <div class="swiper text-images-swiper">
                    <div class="swiper-wrapper">
                        @foreach($display_images as $image)
                        <div class="swiper-slide">
                            <img src="{{ $image['sizes']['large'] }}" alt="{{ $image['alt'] ?? '' }}">
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-scrollbar"></div>
                </div>
                @else
                <img src="{{ $display_images[0]['sizes']['large'] }}" alt="{{ $display_images[0]['alt'] ?? '' }}">
                @endif
            </div>
            @endif
        </div>
    </div>
</section>
@php
$layout = $fields['layout'] ?? 'stacked';
$alignment = $fields['alignment'] ?? null;
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = filter_var($fields['add_waves'] ?? false, FILTER_VALIDATE_BOOLEAN);
$id = $block['anchor'] ?? null;
$is_split = $layout === 'split';
$alignment_class = $is_split ? 'normal' : ($alignment ?? 'normal');
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="text {{ $alignment_class }} bg-{{ $background_color }}{{ $is_split ? ' layout-split' : '' }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        @if($is_split)
        @if($label)
        <div class="text-split-label-wrap" data-aos="fade-up">
            <span class="label">{{ $label }}</span>
        </div>
        @endif
        <div class="flex-wrapper text-split" data-aos="fade-up">
            <div class="text-split-title-col">
                @if($title)
                <h2>{{ $title }}</h2>
                @endif
            </div>
            <div class="text-split-body-col">
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
        </div>
        @else
        <div class="content" data-aos="fade-up">
            @if($label)
            <span class="label">{{ $label }}</span>
            @endif

            @if($title)
            <h2>{{ $title }}</h2>
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
        @endif
    </div>
</section>

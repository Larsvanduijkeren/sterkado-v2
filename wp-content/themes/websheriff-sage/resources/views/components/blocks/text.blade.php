@php
$alignment = $fields['alignment'] ?? null;
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = $fields['add_waves'] ?? false;

$id = $block['anchor'] ?? null;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="text {{ $alignment ?? 'normal' }} bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
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
    </div>
</section>
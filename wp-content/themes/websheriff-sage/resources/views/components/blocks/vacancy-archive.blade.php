@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = filter_var($fields['add_waves'] ?? false, FILTER_VALIDATE_BOOLEAN);

$id = $block['anchor'] ?? null;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="vacancy-archive bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        <div class="intro" data-aos="fade-up">
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

        @if(isset($query) && $query && $query->have_posts())
        <div class="cards" data-aos="fade-up">
            @while($query->have_posts())
            @php $query->the_post(); $vacancy = get_post(); @endphp
            @include('components.vacancy-card', ['vacancy' => $vacancy])
            @endwhile
        </div>
        @include('partials.pagination', ['query' => $query])
        @endif
    </div>
</section>

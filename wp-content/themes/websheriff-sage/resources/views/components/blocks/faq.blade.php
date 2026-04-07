@php
$label = $fields['label'] ?? null;
$title = $fields['title'] ?? null;
$text = $fields['text'] ?? null;
$buttons = $fields['buttons'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = $fields['add_waves'] ?? false;
$questions = $questions ?? [];

$id = $block['anchor'] ?? null;

// FAQ rich results (JSON-LD)
$faq_schema = null;
if (!empty($questions)) {
    $main_entity = [];
    foreach ($questions as $q) {
        $answer_content = apply_filters('the_content', $q->post_content);
        $answer_text = wp_strip_all_tags($answer_content);
        if ($answer_text === '') {
            continue;
        }
        $main_entity[] = [
            '@type' => 'Question',
            'name' => get_the_title($q),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $answer_text,
            ],
        ];
    }
    if (!empty($main_entity)) {
        $faq_schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $main_entity,
        ];
    }
}
@endphp

@if($faq_schema)
<script type="application/ld+json">{!! json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif

<section
    id="@if($id) {{ $id }} @endif"
    class="faq bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        <div class="flex-wrapper">
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

            @if(!empty($questions))
            <div class="questions accordion" data-aos="fade-up">
                @foreach($questions as $question)
                <div class="question">
                    <h4 class="h5">{{ get_the_title($question) }}</h4>
                    @php $answer = apply_filters('the_content', $question->post_content); @endphp
                    @if(!empty($answer))
                    <div class="answer">{!! $answer !!}</div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>

@php
$title = $fields['title'] ?? null;
$id = $block['anchor'] ?? null;
$faq_selection_questions = isset($faq_selection_questions) && is_array($faq_selection_questions) ? $faq_selection_questions : [];
$is_preview = $is_preview ?? false;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="faq-selection">
    <div class="container">
        @if($title)
        <div class="faq-selection-intro content" data-aos="fade-up">
            <h2>{{ $title }}</h2>
        </div>
        @endif

        @if(count($faq_selection_questions))
        <div class="accordion faq-selection-accordion" data-aos="fade-up">
            @foreach($faq_selection_questions as $questionPost)
            @if($questionPost instanceof \WP_Post)
            <div class="question">
                <h4>{{ get_the_title($questionPost) }}</h4>
                <div class="answer">
                    {!! apply_filters('the_content', $questionPost->post_content) !!}
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @elseif($is_preview)
        <p class="faq-selection-empty">{{ __('Choose one or more question categories to show the accordion.', 'sage') }}</p>
        @endif
    </div>
</section>

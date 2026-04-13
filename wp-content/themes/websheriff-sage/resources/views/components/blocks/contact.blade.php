@php
$id = $block['anchor'] ?? null;
@endphp
<section
    @if($id) id="{{ $id }}" @endif
    class="contact">
    <div class="container">
        <h2>{{ __('Contact', 'sage') }}</h2>
    </div>
</section>

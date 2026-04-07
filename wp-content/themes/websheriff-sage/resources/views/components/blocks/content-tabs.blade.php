@php
$tabs = $fields['tabs'] ?? null;
$background_color = $fields['background_color'] ?? 'white';
$add_waves = $fields['add_waves'] ?? false;

$id = $block['anchor'] ?? null;
@endphp

<section
    id="@if($id) {{ $id }} @endif"
    class="content-tabs bg-{{ $background_color }} {{ $add_waves ? 'has-waves' : '' }}">
    <div class="container">
        @if($tabs)
        <div class="tabs-wrapper" data-aos="fade-up">
            <div class="tabs-list" role="tablist">
                @foreach($tabs as $index => $tab)
                @php
                $tab_label = $tab['tab_label'] ?? null;
                $tab_id = 'tab-' . $block['id'] . '-' . $index;
                $panel_id = 'panel-' . $block['id'] . '-' . $index;
                @endphp
                @if($tab_label)
                <button
                    type="button"
                    role="tab"
                    id="{{ $tab_id }}"
                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                    aria-controls="{{ $panel_id }}"
                    class="tab-trigger {{ $index === 0 ? 'is-active' : '' }}">
                    {{ $tab_label }}
                </button>
                @endif
                @endforeach
            </div>

            <div class="tabs-panels" >
                @foreach($tabs as $index => $tab)
                @php
                $tab_title = $tab['title'] ?? null;
                $tab_text = $tab['text'] ?? null;
                $tab_buttons = $tab['buttons'] ?? null;
                $tab_image = $tab['image'] ?? null;
                $panel_id = 'panel-' . $block['id'] . '-' . $index;
                $tab_id = 'tab-' . $block['id'] . '-' . $index;
                @endphp
                <div
                    role="tabpanel"
                    id="{{ $panel_id }}"
                    aria-labelledby="{{ $tab_id }}"
                    class="tab-panel {{ $index === 0 ? 'is-active' : '' }}"
                    @if($index !==0) hidden @endif>
                    <div class="flex-wrapper">
                        @if($tab_image)
                        <div class="image-wrapper">
                            <div class="image">
                                <img src="{{ $tab_image['sizes']['large'] ?? $tab_image['url'] }}" alt="{{ $tab_image['alt'] ?? '' }}">
                            </div>
                        </div>
                        @endif
                        <div class="content">
                            @if($tab_title)
                            <h3>{{ $tab_title }}</h3>
                            @endif
                            @if($tab_text)
                            {!! $tab_text !!}
                            @endif
                            @if($tab_buttons)
                            <div class="buttons">
                                @foreach($tab_buttons as $button)
                                @php
                                $button_obj = $button['button'] ?? $button;
                                $url = $button_obj['url'] ?? null;
                                $button_title = $button_obj['title'] ?? null;
                                $target = $button_obj['target'] ?? '_self';
                                @endphp
                                @if($url && $button_title)
                                <a href="{{ $url }}" target="{{ $target }}" class="{{ $loop->first ? 'btn' : 'btn btn-ghost' }}">{{ $button_title }}</a>
                                @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>
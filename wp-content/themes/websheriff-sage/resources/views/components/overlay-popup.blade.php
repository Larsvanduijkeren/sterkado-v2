@php
$p = $overlayPopup ?? null;
if (! is_array($p)) {
    return;
}
$title = isset($p['title']) ? (string) $p['title'] : '';
$textHtml = isset($p['text_html']) ? (string) $p['text_html'] : '';
$buttons = isset($p['buttons']) && is_array($p['buttons']) ? $p['buttons'] : [];
$cookieName = isset($p['cookie_name']) ? (string) $p['cookie_name'] : 'ws_overlay_popup_dismiss';
$cookieMaxAge = isset($p['cookie_max_age']) ? (int) $p['cookie_max_age'] : 604800;
$headingId = 'ws-overlay-popup-title';
@endphp
<div
    id="ws-overlay-popup"
    class="ws-overlay-popup"
    role="dialog"
    aria-modal="true"
    @if($title !== '') aria-labelledby="{{ $headingId }}" @else aria-label="{{ esc_attr(__('Melding', 'sage')) }}" @endif
    hidden
    data-cookie-name="{{ esc_attr($cookieName) }}"
    data-cookie-max-age="{{ (int) $cookieMaxAge }}">
    <div class="ws-overlay-popup-scrim" data-ws-overlay-close tabindex="-1"></div>
    <div class="ws-overlay-popup-panel">
        <button type="button" class="ws-overlay-popup-close" data-ws-overlay-close aria-label="{{ esc_attr(__('Sluiten', 'sage')) }}">
            <span aria-hidden="true">&times;</span>
        </button>
        @if($title !== '')
        <h2 id="{{ $headingId }}" class="ws-overlay-popup-title h2">{{ $title }}</h2>
        @endif
        @if($textHtml !== '')
        <div class="ws-overlay-popup-text">{!! $textHtml !!}</div>
        @endif
        @if(count($buttons))
        <div class="ws-overlay-popup-actions">
            @foreach($buttons as $idx => $btn)
            @php
            $btn = is_array($btn) ? $btn : [];
            $u = isset($btn['url']) ? trim((string) $btn['url']) : '';
            $t = isset($btn['title']) ? trim((string) $btn['title']) : '';
            $tg = isset($btn['target']) ? (string) $btn['target'] : '_self';
            @endphp
            @if($u !== '' && $t !== '')
            <a
                href="{{ esc_url($u) }}"
                class="{{ $idx === 0 ? 'btn' : 'btn-ghost' }}"
                @if($tg !== '') target="{{ esc_attr($tg) }}" @endif
                @if($tg === '_blank') rel="noopener noreferrer" @endif>{{ $t }}</a>
            @endif
            @endforeach
        </div>
        @endif
    </div>
</div>

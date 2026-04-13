@php
$layout = $mega['layout'] ?? '';
$panelId = 'mega-panel-' . preg_replace('/[^a-z0-9\-]+/i', '-', (string) $trigger);
@endphp
@if($layout === 'card_grid')
<div class="mega-menu mega-menu-grid" id="{{ $panelId }}" role="region" aria-label="{{ esc_attr(__('Submenu', 'sage')) }}">
    <div class="mega-menu-inner container">
        <div class="mega-menu-grid-layout">
            <div class="mega-menu-grid-intro content">
                @if(!empty($mega['intro_title']))
                <h2 class="mega-menu-heading">{{ $mega['intro_title'] }}</h2>
                @endif
                @if(!empty($mega['intro_text']))
                <p class="mega-menu-intro-text">{{ $mega['intro_text'] }}</p>
                @endif
            </div>
            <div class="mega-menu-grid-main">
                @if(!empty($mega['section_title']))
                <p class="mega-menu-heading mega-menu-heading-sub">{{ $mega['section_title'] }}</p>
                @endif
                @if(!empty($mega['cards']))
                <div class="mega-menu-card-grid">
                    @foreach($mega['cards'] as $card)
                    @if(!empty($card['url']))
                    <a
                        href="{{ esc_url($card['url']) }}"
                        class="mega-menu-card"
                        target="{{ esc_attr($card['target'] ?? '_self') }}"
                        @if(($card['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif>
                        @php
                        $img = $card['image'] ?? [];
                        $src = is_array($img) ? ($img['sizes']['large'] ?? $img['url'] ?? '') : '';
                        @endphp
                        @if($src !== '')
                        <span class="mega-menu-card-media">
                            <img src="{{ esc_url($src) }}" alt="{{ esc_attr($card['title'] !== '' ? $card['title'] : ($card['link_title'] ?? '')) }}" loading="lazy" decoding="async">
                        </span>
                        @endif
                        <span class="mega-menu-card-body">
                            @if($card['title'] !== '')
                            <span class="mega-menu-card-title">{{ $card['title'] }}</span>
                            @endif
                            <span class="mega-menu-card-arrow" aria-hidden="true">→</span>
                        </span>
                    </a>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@elseif($layout === 'featured_links')
@php
$hl = $mega['highlight_link'] ?? [];
$hl = is_array($hl) ? $hl : [];
@endphp
<div class="mega-menu mega-menu-featured" id="{{ $panelId }}" role="region" aria-label="{{ esc_attr(__('Submenu', 'sage')) }}">
    <div class="mega-menu-inner container">
        <div class="mega-menu-featured-layout">
            <div class="mega-menu-featured-columns">
                <div class="mega-menu-featured-col">
                    @if(!empty($mega['col1_title']))
                    <h2 class="mega-menu-heading">{{ $mega['col1_title'] }}</h2>
                    @endif
                    @if(!empty($mega['col1_text']))
                    <div class="mega-menu-rich-text">{!! wp_kses_post($mega['col1_text']) !!}</div>
                    @endif
                    @if(!empty($mega['social_heading']) || !empty($mega['social']))
                    <div class="mega-menu-social">
                        @if(!empty($mega['social_heading']))
                        <p class="mega-menu-social-heading">{{ $mega['social_heading'] }}</p>
                        @endif
                        @if(!empty($mega['social']))
                        <ul class="mega-menu-social-list">
                            @foreach($mega['social'] as $soc)
                            <li>
                                <a
                                    href="{{ esc_url($soc['url'] ?? '') }}"
                                    class="mega-menu-social-link"
                                    target="{{ esc_attr($soc['target'] ?? '_self') }}"
                                    @if(($soc['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif
                                    aria-label="{{ esc_attr($soc['aria_label'] ?? '') }}">
                                    <i class="{{ esc_attr($soc['icon_class'] ?? 'fa-solid fa-link') }}" aria-hidden="true"></i>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="mega-menu-featured-col">
                    @if(!empty($mega['col2_title']))
                    <h2 class="mega-menu-heading">{{ $mega['col2_title'] }}</h2>
                    @endif
                    @if(!empty($mega['column_links']))
                    <ul class="mega-menu-link-list">
                        @foreach($mega['column_links'] as $lnk)
                        <li>
                            <a
                                href="{{ esc_url($lnk['url']) }}"
                                target="{{ esc_attr($lnk['target'] ?? '_self') }}"
                                @if(($lnk['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif>{{ $lnk['title'] !== '' ? $lnk['title'] : $lnk['url'] }}</a>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            <div class="mega-menu-featured-aside">
                <div class="mega-menu-featured-panel">
                    @if(!empty($mega['highlight_title']))
                    <h3 class="mega-menu-featured-title">{{ $mega['highlight_title'] }}</h3>
                    @endif
                    @if(!empty($mega['highlight_meta']))
                    <p class="mega-menu-featured-meta">
                        <i class="fa-regular fa-calendar-days" aria-hidden="true"></i>
                        <span>{{ $mega['highlight_meta'] }}</span>
                    </p>
                    @endif
                    @if(!empty($mega['highlight_excerpt']))
                    <p class="mega-menu-featured-excerpt">{!! nl2br(e($mega['highlight_excerpt'])) !!}</p>
                    @endif
                    @if(!empty($hl['url']) && !empty($hl['title']))
                    <a
                        href="{{ esc_url($hl['url']) }}"
                        class="mega-menu-read-more"
                        target="{{ esc_attr($hl['target'] ?? '_self') }}"
                        @if(($hl['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif>
                        <span>{{ $hl['title'] }}</span>
                        <span class="mega-menu-read-more-arrow" aria-hidden="true">→</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

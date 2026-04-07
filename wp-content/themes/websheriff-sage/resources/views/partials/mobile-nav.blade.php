<div class="mobile-nav">
    <div class="content">
        <div class="nav">
            <div class="flex-wrapper">
                {!! wp_nav_menu(['theme_location' => 'header-nav', 'echo' => false]) !!}

                @if(!empty($phone))
                <a class="phone" href="tel:{{ $phone }}">{{ $phone }}</a>
                @endif

                @if(!empty($email))
                <a class="mail" href="mailto:{{ $email }}">{{ $email }}</a>
                @endif

                @if(!empty($headerCta['url']) && !empty($headerCta['title']))
                <div class="header-buttons">
                    <a
                        class="btn btn-accent small"
                        href="{{ esc_url($headerCta['url']) }}"
                        target="{{ esc_attr($headerCta['target'] ?? '_self') }}"
                    >{{ $headerCta['title'] }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
export default function initTheme({$, AOS, Lenis, Swiper, Scrollbar, EffectFade, Autoplay, Thumbs}) {
    if (window.__wsThemeInitialized) return;
    window.__wsThemeInitialized = true;

    $(function () {
        smoothScroll($);
        menu($);
        accordion($);
        headerController($);
        stickyFeatures($);
        postIndex($);

        postSlider(Swiper, Scrollbar);
        singleGallerySlider(Swiper, Thumbs);
        textImagesSlider(Swiper, Scrollbar, EffectFade);
        heroGallerySlider($, Swiper, EffectFade, Autoplay);
        initAosAndLenis($, AOS, Lenis);
    });
}

function postIndex($) {
    const $indexItems = $(".post-content .content h2, .post-content .content h3");
    const $indexContainer = $(".post-content .index");
    if (!$indexItems.length || !$indexContainer.length) return;

    $indexItems.each(function (index) {
        const title = $(this).text();
        const slug = "item-" + index;
        $(this).attr("id", slug);
        $indexContainer.append("<a data-ref=\"" + slug + "\" href=\"#" + slug + "\">" + title + "</a>");
    });

    const $indexLinks = $indexContainer.find("a");
    const activeOffset = 150;

    const updateActiveIndex = () => {
        const scrollTop = $(window).scrollTop();
        const centerline = scrollTop + activeOffset;

        let $activeHeading = null;
        $indexItems.each(function () {
            const top = $(this).offset().top;
            if (top <= centerline) {
                $activeHeading = $(this);
            }
        });

        const activeSlug = $activeHeading ? $activeHeading.attr("id") : $indexItems.first().attr("id");
        $indexLinks.removeClass("is-active");
        $indexLinks.filter("[data-ref=\"" + activeSlug + "\"]").addClass("is-active");
    };

    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            updateActiveIndex();
            ticking = false;
        });
    };

    $(window).on("scroll", onScroll);
    updateActiveIndex();
}

function initSwiperSliders({
                               Swiper,
                               modules = [],
                               selector,
                               defaults = {},
                               getOptions = null,
                               scrollbar = true,
                           }) {
    const $els = $(selector);
    if (!$els.length || !Swiper) return;

    $els.each(function () {
        const el = this;
        if (el.swiper) return;

        const $el = $(el);
        const perElOptions = typeof getOptions === 'function' ? (getOptions(el) || {}) : {};

        const scrollbarEl = scrollbar ? $el.find('.swiper-scrollbar')[0] : null;

        const options = {
            modules: modules.filter(Boolean),
            ...defaults,
            ...perElOptions,
            ...(scrollbarEl
                ? {
                    scrollbar: {
                        el: scrollbarEl,
                        draggable: true,
                        hide: false,
                    },
                }
                : {}),
            on: {
                ...(defaults.on || {}),
                ...(perElOptions.on || {}),
            },
        };

        new Swiper(el, options);
    });
}

const slickLikeDefaults = {
    loop: false,
    autoplay: false,
    slidesPerView: 'auto',
    slidesPerGroup: 1,
    spaceBetween: 20,
    speed: 400,
    watchOverflow: true,
    resistanceRatio: 0,
    grabCursor: true,
    pagination: false,
    navigation: false,
};

function stickyFeatures($) {
    const $container = $('.sticky-features');
    if (!$container.length) return;

    let ticking = false;
    let centerline = 0;

    const update = () => {
        centerline = $(window).scrollTop() + $(window).height() / 2;

        $container.find('.feature').each(function () {
            const $feature = $(this);
            const top = $feature.offset().top;
            const bottom = top + $feature.outerHeight(true);

            if (top < centerline && bottom > centerline) {
                const dataFeature = $feature.attr('data-feature');
                $container.find('.feature').removeClass('active');
                $feature.addClass('active');
                $container.find('.image, .dot').removeClass('active');
                $container.find(`.image[data-image="${dataFeature}"], .dot[data-dot="${dataFeature}"]`).addClass('active');
            }
        });

        ticking = false;
    };

    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(update);
    };

    $(window).on('scroll', onScroll);
    update();
}

function postSlider(Swiper, Scrollbar) {
    initSwiperSliders({
        Swiper,
        selector: '.post-slider .swiper',
        modules: [Scrollbar],
        defaults: slickLikeDefaults,
    });
}

function singleGallerySlider(Swiper, Thumbs) {
    const $galleries = $('.single-gallery');
    if (!$galleries.length || !Swiper || !Thumbs) return;

    $galleries.each(function () {
        const $block = $(this);
        const mainEl = $block.find('.main')[0];
        const thumbsEl = $block.find('.thumbs')[0];
        if (!mainEl || !thumbsEl || mainEl.swiper) return;

        const thumbsSwiper = new Swiper(thumbsEl, {
            modules: [Thumbs],
            spaceBetween: 8,
            slidesPerView: 2,
            watchSlidesProgress: true,
            breakpoints: {
                576: { slidesPerView: 4 },
            },
        });

        new Swiper(mainEl, {
            modules: [Thumbs],
            spaceBetween: 0,
            thumbs: { swiper: thumbsSwiper },
            speed: 400,
        });
    });
}

function textImagesSlider(Swiper, Scrollbar, EffectFade) {
    initSwiperSliders({
        Swiper,
        selector: '.text-images .text-images-swiper',
        modules: [Scrollbar, EffectFade],
        defaults: {
            effect: 'fade',
            fadeEffect: { crossFade: true },
            slidesPerView: 1,
            slidesPerGroup: 1,
            spaceBetween: 0,
            loop: true,
            speed: 500,
            watchOverflow: true,
            resistanceRatio: 0,
            pagination: false,
            navigation: false,
        },
    });
}

function heroGallerySlider($, Swiper, EffectFade, Autoplay) {
    const selector = '.hero .hero-gallery-swiper';
    const $els = $(selector);
    if (!$els.length || !Swiper || !EffectFade || !Autoplay) {
        return;
    }

    $els.each(function () {
        const el = this;
        if (el.swiper) {
            return;
        }
        const slideCount = $(el).find('.swiper-slide').length;
        if (slideCount < 2) {
            return;
        }

        new Swiper(el, {
            modules: [EffectFade, Autoplay],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            speed: 900,
            allowTouchMove: false,
            simulateTouch: false,
            grabCursor: false,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: false,
            navigation: false,
            scrollbar: false,
        });
    });
}

function initAosAndLenis($, AOS, Lenis) {
    const isDesktop = $(window).width() > 991;

    if (isDesktop && Lenis) {
        const lenis = new Lenis();

        const raf = (time) => {
            lenis.raf(time);
            requestAnimationFrame(raf);
        };

        requestAnimationFrame(raf);
    }

    if (AOS) {
        AOS.init({
            offset: 50, duration: isDesktop ? 1000 : 600,
        });
    }
}

function initAccordion($, $scope) {
    const $accordions = $scope ? $scope.find('.accordion') : $('.accordion');
    const duration = 350;

    $accordions.each(function () {
        const $acc = $(this);
        if ($acc.data('accordion-initialized')) return;
        $acc.data('accordion-initialized', true);

        const $questions = $acc.find('.question');

        $questions.removeClass('open').find('.answer').hide();

        $questions.each(function () {
            const $q = $(this);
            const $header = $q.find('h4');
            const $answer = $q.find('.answer');
            if (!$header.length || !$answer.length) return;

            $header.attr({ role: 'button', tabindex: '0', 'aria-expanded': 'false' });

            const toggle = () => {
                const isOpen = $q.hasClass('open');

                $questions.not($q).removeClass('open').find('.answer').slideUp(duration);
                $questions.not($q).find('h4').attr('aria-expanded', 'false');

                if (isOpen) {
                    $answer.slideUp(duration, function () {
                        $q.removeClass('open');
                        $header.attr('aria-expanded', 'false');
                    });
                } else {
                    $q.addClass('open');
                    $header.attr('aria-expanded', 'true');
                    $answer.slideDown(duration);
                }
            };

            $header.on('click', toggle);
            $header.on('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggle();
                }
            });
        });
    });
}

function headerController($) {
    const scrollWrapper = $(window);
    const body = $('body');

    const setScrolled = () => body.toggleClass('scrolled', scrollWrapper.scrollTop() > 10);

    setScrolled();
    scrollWrapper.on('scroll', setScrolled);
}

function accordion($) {
    initAccordion($);
}

function smoothScroll($) {
    $(document).on('click', 'a[href^="#"]', function (event) {
        const href = $(this).attr('href');
        if (!href || href === '#') return;

        const target = $(href);
        if (!target.length) return;

        event.preventDefault();
        $('html, body').animate({scrollTop: target.offset().top - 120}, 500);
    });
}

function menu($) {
    $(document).on('click', '.mobile-nav .menu-item-has-children > a', function (e) {
        e.preventDefault();
        $(this).toggleClass('open');
    });

    $(document).on('click', '.hamburger', function () {
        $('body').toggleClass('mobile-nav-open');
        setTimeout(() => $('body, html').toggleClass('no-scroll'), 500);
    });
}

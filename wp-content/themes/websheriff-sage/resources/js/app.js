import $ from 'jquery';

window.$ = window.jQuery = $;

// Pull in static assets for dev/build (fonts/images)
import.meta.glob(
    [
        '../images/**',
        '../fonts/icomoon.eot',
        '../fonts/icomoon.woff',
        '../fonts/icomoon.ttf',
        '../fonts/icomoon.svg',
    ],
    { eager: true },
);

// Core features load immediately (lightweight)
import initTheme from './main';

(async () => {
    const $ = window.jQuery;
    const hasSliders = $('.swiper').length || $('.single-gallery').length;
    const hasAos = $('[data-aos]').length;
    const isDesktop = $(window).width() > 991;
    const hasLenis = isDesktop; // Lenis only on desktop

    let Swiper = null;
    let Scrollbar = null;
    let EffectFade = null;
    let Autoplay = null;
    let Thumbs = null;
    let Navigation = null;
    let Pagination = null;
    let AOS = null;
    let Lenis = null;

    if (hasSliders) {
        const swiperModule = await import('swiper');
        const swiperMods = await import('swiper/modules');
        Swiper = swiperModule.default;
        Scrollbar = swiperMods.Scrollbar;
        EffectFade = swiperMods.EffectFade;
        Autoplay = swiperMods.Autoplay;
        Thumbs = swiperMods.Thumbs;
        Navigation = swiperMods.Navigation;
        Pagination = swiperMods.Pagination;
        await import('swiper/css');
        await import('swiper/css/effect-fade');
        await import('swiper/css/scrollbar');
        await import('swiper/css/navigation');
        await import('swiper/css/pagination');
    }

    if (hasAos) {
        AOS = (await import('aos')).default;
        await import('aos/dist/aos.css');
    }

    if (hasLenis) {
        Lenis = (await import('lenis')).default;
    }

    initTheme({ $, AOS, Lenis, Swiper, Scrollbar, EffectFade, Autoplay, Thumbs, Navigation, Pagination });

    $(function () {
        if (!$('.request-quote').length) {
            return;
        }
        void import('./request-quote.js').then((rq) => {
            rq.initRequestQuote();
        });
    });
})();

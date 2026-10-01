import './bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';

import { createApp } from 'vue/dist/vue.esm-bundler';
import ProductSearch from './vue/components/ProductSearch.vue';

const app = createApp({});

app.component('product-search', ProductSearch);

// Mount the Vue app
app.mount('#app');

function initProductCardSwipers(root = document) {
    if (typeof window.Swiper === 'undefined') {
        return;
    }

    root.querySelectorAll('.product-card-swiper:not([data-swiper-initialized])').forEach((element) => {
        element.dataset.swiperInitialized = 'true';

        new window.Swiper(element, {
            effect: 'cards',
            direction: 'horizontal',
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: 1,
            mousewheel: false,
            pagination: {
                el: element.querySelector('.swiper-pagination'),
                clickable: true,
                dynamicBullets: true,
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            speed: 800,
            breakpoints: {
                640: {
                    effect: 'creative',
                    creativeEffect: {
                        prev: { translate: [0, 0, -400] },
                        next: { translate: ['100%', 0, 0] },
                    },
                },
                768: {
                    effect: 'flip',
                    flipEffect: {
                        slideShadows: true,
                        limitRotation: true,
                    },
                    mousewheel: true,
                },
                1024: {
                    effect: 'cube',
                    direction: 'vertical',
                    mousewheel: true,
                    cubeEffect: {
                        shadow: true,
                        slideShadows: true,
                        shadowOffset: 20,
                        shadowScale: 0.94,
                    },
                    zoom: true,
                },
                1280: {
                    effect: 'cube',
                    direction: 'vertical',
                    mousewheel: true,
                    zoom: {
                        maxRatio: 1.5,
                        minRatio: 1,
                    },
                    cubeEffect: {
                        shadow: true,
                        slideShadows: true,
                        shadowOffset: 20,
                        shadowScale: 0.94,
                    },
                },
            },
            a11y: {
                enabled: true,
                prevSlideMessage: 'Önceki görsel',
                nextSlideMessage: 'Sonraki görsel',
                firstSlideMessage: 'İlk görsel',
                lastSlideMessage: 'Son görsel',
            },
        });
    });
}

document.addEventListener('DOMContentLoaded', () => initProductCardSwipers());

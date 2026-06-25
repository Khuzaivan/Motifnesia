import './bootstrap';
import { gsap } from 'gsap';
import * as THREE from 'three';
import Lenis from 'lenis';
import anime from 'animejs/lib/anime.es.js';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { animate as motionAnimate, stagger as motionStagger } from 'motion';

window.gsap = window.gsap || gsap;
window.THREE = window.THREE || THREE;
window.Lenis = window.Lenis || Lenis;
window.anime = window.anime || anime;
window.motionAnimate = window.motionAnimate || motionAnimate;

document.addEventListener('DOMContentLoaded', () => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    AOS.init({
        duration: reduceMotion ? 0 : 650,
        easing: 'ease-out-cubic',
        once: true,
        offset: 48,
    });

    if (reduceMotion) {
        return;
    }

    const scrollRoot = document.querySelector('main.overflow-y-auto');
    if (scrollRoot) {
        const lenis = new Lenis({
            wrapper: scrollRoot,
            content: scrollRoot.firstElementChild || undefined,
            duration: 1.05,
            smoothWheel: true,
            wheelMultiplier: 0.85,
            touchMultiplier: 1.4,
        });

        const raf = (time) => {
            lenis.raf(time);
            requestAnimationFrame(raf);
        };
        requestAnimationFrame(raf);
    }

    anime({
        targets: '[data-supply-animate]',
        translateY: [18, 0],
        opacity: [0, 1],
        delay: anime.stagger(36),
        duration: 620,
        easing: 'easeOutCubic',
    });

    motionAnimate(
        '.motion-pop',
        { opacity: [0, 1], scale: [0.98, 1], y: [12, 0] },
        { duration: 0.45, delay: motionStagger(0.04), easing: [0.22, 1, 0.36, 1] }
    );

    document.querySelectorAll('.supply-tilt').forEach((card) => {
        card.addEventListener('mousemove', (event) => {
            const rect = card.getBoundingClientRect();
            const x = event.clientX - rect.left - rect.width / 2;
            const y = event.clientY - rect.top - rect.height / 2;

            gsap.to(card, {
                rotationY: x * 0.01,
                rotationX: -y * 0.01,
                transformPerspective: 900,
                duration: 0.3,
                ease: 'power2.out',
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                rotationY: 0,
                rotationX: 0,
                duration: 0.45,
                ease: 'elastic.out(1, 0.45)',
            });
        });
    });
});

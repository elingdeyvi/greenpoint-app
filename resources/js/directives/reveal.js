/**
 * Reveal on scroll (reemplazo ligero de WOW.js del sitio de producción).
 */
function parseDelay(value) {
    if (!value) {
        return 0;
    }
    if (typeof value === 'number') {
        return value;
    }
    const match = String(value).match(/([\d.]+)\s*(ms|s)?/i);
    if (!match) {
        return 0;
    }
    const amount = Number(match[1]);
    return match[2] === 's' ? amount * 1000 : amount;
}

export const revealDirective = {
    mounted(el, binding) {
        const delay = parseDelay(binding.value?.delay ?? binding.arg ?? el.dataset.revealDelay);
        const effect = binding.value?.effect || el.dataset.reveal || 'fade-up';

        el.classList.add('gp-reveal', `gp-reveal-${effect}`);
        if (delay) {
            el.style.transitionDelay = `${delay}ms`;
        }

        const reveal = () => {
            el.classList.add('is-visible');
            observer.unobserve(el);
        };

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        reveal();
                    }
                });
            },
            /* threshold bajo: la franja de servicios solo asoma ~70px al cargar */
            { threshold: 0.01, rootMargin: '0px' },
        );

        observer.observe(el);
        el._gpRevealObserver = observer;

        // Si ya está (parcialmente) en viewport al montar, revelar ya
        requestAnimationFrame(() => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                reveal();
            }
        });
    },
    unmounted(el) {
        el._gpRevealObserver?.disconnect();
    },
};

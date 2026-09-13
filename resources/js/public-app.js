/**
 * Entry del sitio público — sin AdminLTE ni estilos de login/admin.
 */
import '../css/public-bundle.css';
import './bootstrap';

import 'bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { revealDirective } from './directives/reveal';

const appName = import.meta.env.VITE_APP_NAME || 'GreenPoint';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .directive('reveal', revealDirective)
            .mount(el);
    },
    progress: {
        color: '#f3663f',
    },
});

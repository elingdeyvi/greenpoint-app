/**
 * GreenPoint — Vue 3 + Inertia.js (Composition API) en todo el front.
 * Admin (AdminLTE) y sitio público comparten el mismo runtime Vue 3.
 */
import '../css/app.css';
import '../css/public.css';
import './bootstrap';

import 'bootstrap';
import 'admin-lte';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

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
            .mount(el);
    },
    progress: {
        color: '#f3663f',
    },
});

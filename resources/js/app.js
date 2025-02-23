import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js'; // Importa Ziggy
import { Ziggy } from './ziggy'; // Importa il file generato da Ziggy
import '../css/app.css';

createInertiaApp({
    resolve: name => import(`./Pages/${name}.vue`),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy) 
            .mount(el);
    },
});

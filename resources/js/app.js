import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js'; // Importa Ziggy
import { Ziggy } from './ziggy'; // Importa il file generato da Ziggy
import '../css/app.css';

createInertiaApp({
    resolve: name => import(`./Pages/${name}.vue`).then(module => module.default),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy) 
            .mount(el);
    },
});

const loadGoogleMapsScript = () => {
    if (!window.google && !window.location.pathname.includes('/map')) { 
        const script = document.createElement("script");
        script.src = `https://maps.googleapis.com/maps/api/js?key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}&libraries=places`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
};

loadGoogleMapsScript();
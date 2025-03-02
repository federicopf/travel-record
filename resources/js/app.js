import { createApp, h, computed } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';
import { Ziggy } from './ziggy';
import '../css/app.css';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        const isAuthPage = computed(() => props.initialPage.component.startsWith('Auth/'));

        if (!isAuthPage.value) {
            const colorSchemeComputed = computed(() => 
                props.initialPage.props.auth?.user?.color_scheme || 'blue'
            );
            
            app.config.globalProperties.$colorScheme = colorSchemeComputed.value;
        }

        app.use(plugin)
           .use(ZiggyVue, Ziggy)
           .mount(el);

        loadGoogleMapsScript();
    },
});

const loadGoogleMapsScript = () => {
    const currentPath = window.location.pathname;

    if (currentPath.includes('/new-trip') && !window.google) {
        const script = document.createElement("script");
        script.src = `https://maps.googleapis.com/maps/api/js?key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}&libraries=places`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
};

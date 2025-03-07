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
        
        const colorSchemeComputed = computed(() => 
            props.initialPage.props.auth?.user?.color_scheme || 'blue'
        );
        
        app.config.globalProperties.$colorScheme = colorSchemeComputed.value;

        app.use(plugin)
           .use(ZiggyVue, Ziggy)
           .mount(el);
    },
});

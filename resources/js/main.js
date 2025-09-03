import { createApp } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'

// Import global styles
import '@/Assets/Styles/Layout/base.css'
import '@/Assets/Styles/Components/common.css'

// Import global components
import AppLayout from '@/Layouts/AppLayout.vue'

// Import stores
import { useAuthStore } from '@/Stores/Auth/User'
import { useTripStore } from '@/Stores/Trip/Trips'
import { useProfileStore } from '@/Stores/Profile/Data'
import { useFriendsStore } from '@/Stores/Friends/Friends'

createInertiaApp({
  title: (title) => `${title} - Travel Record`,
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout || AppLayout
    return page
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    const pinia = createPinia()
    
    app.use(pinia)
    app.use(plugin)
    app.use(ZiggyVue)
    
    app.mount(el)
  },
})

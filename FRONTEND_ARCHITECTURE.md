# Frontend Architecture - Travel Record

## 🏗️ Struttura Organizzata

Il frontend è stato completamente ristrutturato con un'architettura modulare e scalabile.

## 📁 Struttura delle Cartelle

```
resources/js/
├── App.vue                    # Componente principale
├── main.js                    # Entry point
├── vite.config.js             # Configurazione Vite
├── Pages/                     # Pagine dell'applicazione
│   ├── Auth/                  # Pagine di autenticazione
│   │   ├── Login.vue
│   │   └── Register.vue
│   ├── Trip/                  # Pagine dei viaggi
│   │   ├── Show/
│   │   ├── Edit/
│   │   ├── Create/
│   │   └── Place/
│   │       ├── Show/
│   │       └── Edit/
│   ├── Profile/               # Pagine del profilo
│   │   ├── Index/
│   │   ├── Settings/
│   │   └── Public/
│   │       ├── Index/
│   │       ├── Trip/
│   │       └── Place/
│   ├── Friends/               # Pagine amici
│   │   ├── Index/
│   │   ├── Search/
│   │   └── Requests/
│   └── Map/                   # Pagina mappa
├── Components/                # Componenti riutilizzabili
│   ├── UI/                    # Componenti UI base
│   │   ├── Buttons/
│   │   ├── Inputs/
│   │   ├── Cards/
│   │   ├── Modals/
│   │   ├── Alerts/
│   │   └── Navigation/
│   ├── Forms/                 # Componenti form
│   │   ├── Auth/
│   │   ├── Trip/
│   │   ├── Profile/
│   │   └── Friends/
│   ├── Layout/                # Componenti layout
│   │   ├── Header/
│   │   ├── Footer/
│   │   ├── Sidebar/
│   │   └── Navigation/
│   ├── Features/              # Componenti funzionali
│   │   ├── Map/
│   │   ├── Trip/
│   │   ├── Profile/
│   │   └── Friends/
│   └── Modals/                # Modali specifici
│       ├── Trip/
│       ├── Profile/
│       └── Friends/
├── Layouts/                   # Layout dell'applicazione
│   └── AppLayout.vue
├── Composables/               # Composables Vue 3
│   ├── Auth/
│   │   ├── Login/
│   │   └── Register/
│   ├── Trip/
│   │   ├── Create/
│   │   ├── Edit/
│   │   ├── Show/
│   │   └── Delete/
│   ├── Profile/
│   │   ├── Settings/
│   │   └── Photo/
│   ├── Friends/
│   │   ├── Search/
│   │   ├── Follow/
│   │   └── Requests/
│   └── Map/
│       ├── Data/
│       └── Markers/
├── Utils/                     # Utility e helper
│   ├── Validation/
│   │   ├── Auth/
│   │   ├── Trip/
│   │   ├── Profile/
│   │   └── Friends/
│   ├── Formatting/
│   │   ├── Dates/
│   │   ├── Images/
│   │   └── Text/
│   └── Helpers/
│       ├── Storage/
│       ├── API/
│       └── Constants.js
├── Stores/                    # Store Pinia
│   ├── Auth/
│   │   ├── User/
│   │   └── Permissions/
│   ├── Trip/
│   │   ├── Trips/
│   │   └── Places/
│   ├── Profile/
│   │   ├── Settings/
│   │   └── Data/
│   └── Friends/
│       ├── Friends/
│       └── Requests/
├── Types/                     # Definizioni TypeScript
│   ├── Auth/
│   │   ├── User/
│   │   └── Login/
│   ├── Trip/
│   │   ├── Trip/
│   │   ├── Place/
│   │   └── Photo/
│   ├── Profile/
│   │   ├── Profile/
│   │   └── Settings/
│   ├── Friends/
│   │   ├── Friend/
│   │   └── Request/
│   └── Map/
│       ├── Marker/
│       └── Location/
└── Assets/                    # Asset statici
    ├── Icons/
    │   ├── UI/
    │   └── Navigation/
    ├── Images/
    │   ├── UI/
    │   └── Content/
    └── Styles/
        ├── Components/
        ├── Pages/
        └── Layout/
```

## 🎯 Principi di Organizzazione

### 1. **Separazione delle Responsabilità**
- **Pages**: Solo logica di presentazione e routing
- **Components**: Componenti riutilizzabili e modulari
- **Composables**: Logica di business riutilizzabile
- **Stores**: Gestione stato globale
- **Utils**: Funzioni helper e utility

### 2. **Modularità**
- Ogni feature ha la sua cartella dedicata
- Componenti organizzati per tipo e funzionalità
- Import/export puliti e organizzati

### 3. **Scalabilità**
- Struttura che cresce con l'applicazione
- Facile aggiungere nuove feature
- Pattern consistenti

## 🚀 Configurazione

### Vite Configuration
```javascript
// vite.config.js
export default defineConfig({
  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources/js'),
      '@Pages': resolve(__dirname, 'resources/js/Pages'),
      '@Components': resolve(__dirname, 'resources/js/Components'),
      '@Composables': resolve(__dirname, 'resources/js/Composables'),
      '@Utils': resolve(__dirname, 'resources/js/Utils'),
      '@Stores': resolve(__dirname, 'resources/js/Stores'),
      '@Types': resolve(__dirname, 'resources/js/Types'),
      '@Assets': resolve(__dirname, 'resources/js/Assets'),
    },
  },
})
```

### Main Entry Point
```javascript
// main.js
import { createApp } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createPinia } from 'pinia'

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    const pinia = createPinia()
    
    app.use(pinia)
    app.use(plugin)
    app.mount(el)
  },
})
```

## 📦 Componenti UI

### Struttura Componenti
```vue
<!-- Components/UI/Buttons/PrimaryButton.vue -->
<template>
  <button 
    :class="buttonClasses" 
    :disabled="disabled"
    @click="$emit('click')"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'success', 'error'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const buttonClasses = computed(() => [
  'btn',
  `btn-${props.variant}`,
  `btn-${props.size}`
])

defineEmits(['click'])
</script>
```

## 🔧 Composables

### Esempio Composable
```javascript
// Composables/Trip/Create.js
import { ref, reactive } from 'vue'
import { apiClient } from '@/Utils/Helpers/API'
import { SUCCESS_MESSAGES, ERROR_MESSAGES } from '@/Utils/Helpers/Constants'

export function useTripCreate() {
  const loading = ref(false)
  const errors = reactive({})
  const form = reactive({
    title: '',
    start_date: '',
    end_date: '',
    places: []
  })

  const createTrip = async () => {
    loading.value = true
    errors.value = {}

    try {
      const response = await apiClient.createTrip(form)
      
      if (response.success) {
        // Handle success
        return { success: true, message: SUCCESS_MESSAGES.TRIP_CREATED }
      } else {
        errors.value = response.errors
        return { success: false, message: ERROR_MESSAGES.VALIDATION_FAILED }
      }
    } catch (error) {
      return { success: false, message: ERROR_MESSAGES.SERVER_ERROR }
    } finally {
      loading.value = false
    }
  }

  return {
    form,
    loading,
    errors,
    createTrip
  }
}
```

## 🗃️ Store Pinia

### Esempio Store
```javascript
// Stores/Trip/Trips.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { apiClient } from '@/Utils/Helpers/API'

export const useTripStore = defineStore('trips', () => {
  const trips = ref([])
  const loading = ref(false)
  const error = ref(null)

  const sortedTrips = computed(() => {
    return trips.value.sort((a, b) => new Date(b.start_date) - new Date(a.start_date))
  })

  const fetchTrips = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await apiClient.getTrips()
      if (response.success) {
        trips.value = response.data.trips
      } else {
        error.value = response.error
      }
    } catch (err) {
      error.value = 'Errore nel caricamento dei viaggi'
    } finally {
      loading.value = false
    }
  }

  const addTrip = (trip) => {
    trips.value.push(trip)
  }

  const removeTrip = (tripId) => {
    trips.value = trips.value.filter(trip => trip.id !== tripId)
  }

  return {
    trips,
    loading,
    error,
    sortedTrips,
    fetchTrips,
    addTrip,
    removeTrip
  }
})
```

## 🎨 Sistema di Styling

### CSS Variables
```css
/* Assets/Styles/Layout/base.css */
:root {
  --primary-color: #3b82f6;
  --primary-hover: #2563eb;
  --bg-primary: #ffffff;
  --text-primary: #1e293b;
  --spacing-md: 1rem;
  --radius-md: 0.375rem;
  --transition-fast: 150ms ease-in-out;
}

[data-theme="dark"] {
  --bg-primary: #1e293b;
  --text-primary: #f8fafc;
}
```

### Utility Classes
```css
/* Assets/Styles/Components/common.css */
.btn {
  display: inline-flex;
  align-items: center;
  padding: var(--spacing-sm) var(--spacing-md);
  border-radius: var(--radius-md);
  transition: all var(--transition-fast);
}

.card {
  background-color: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
}
```

## 📝 Best Practices

### 1. **Naming Conventions**
- Componenti: PascalCase (`PrimaryButton.vue`)
- Composables: camelCase (`useTripCreate.js`)
- Store: camelCase (`useTripStore.js`)
- Utils: camelCase (`formatDate.js`)

### 2. **File Organization**
- Un componente per file
- Import/export espliciti
- Index files per barrel exports

### 3. **Type Safety**
- Definizioni TypeScript per tutti i tipi
- Props validation nei componenti
- Type checking per API responses

### 4. **Performance**
- Lazy loading per componenti grandi
- Memoization dove necessario
- Ottimizzazione bundle con Vite

## 🔄 Migrazione

### Da Vecchia Struttura
```javascript
// Prima
import TripCard from '@/Components/Trip/TripCard.vue'

// Dopo
import TripCard from '@/Components/Features/Trip/TripCard.vue'
```

### Import Paths
```javascript
// Nuovi alias disponibili
import { useTripStore } from '@/Stores/Trip/Trips'
import { formatDate } from '@/Utils/Formatting/Dates'
import { API_ENDPOINTS } from '@/Utils/Helpers/Constants'
import type { Trip } from '@/Types/Trip/Trip'
```

## 📈 Vantaggi

✅ **Organizzazione Chiara**
- Struttura logica e intuitiva
- Facile trovare file e componenti

✅ **Riutilizzabilità**
- Componenti modulari
- Composables condivisi

✅ **Manutenibilità**
- Separazione delle responsabilità
- Codice pulito e organizzato

✅ **Scalabilità**
- Facile aggiungere nuove feature
- Pattern consistenti

✅ **Performance**
- Bundle ottimizzato
- Lazy loading
- Tree shaking

✅ **Developer Experience**
- Autocomplete migliorato
- Type safety
- Hot reload veloce

## 🚀 Prossimi Passi

1. **Migrare componenti esistenti** nella nuova struttura
2. **Implementare TypeScript** per type safety
3. **Aggiungere test unitari** per componenti e composables
4. **Ottimizzare bundle** con code splitting
5. **Implementare PWA** features
6. **Aggiungere Storybook** per componenti UI

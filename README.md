# 🗺️ Travel Record

Un'applicazione web moderna per registrare e condividere i tuoi viaggi con foto, luoghi e hashtags personalizzati.

## ✨ Caratteristiche

- **📸 Gestione Viaggi** - Crea e organizza i tuoi viaggi con foto e dettagli
- **🗺️ Mappa Interattiva** - Visualizza tutti i luoghi visitati su una mappa globale
- **👥 Social Network** - Segui altri viaggiatori e condividi i tuoi viaggi
- **🏷️ Hashtags Personalizzati** - Organizza i contenuti con tag colorati
- **📱 Design Responsive** - Funziona perfettamente su desktop e mobile
- **🎨 Temi Personalizzabili** - Scegli il tuo colore preferito

## 🚀 Tecnologie

- **Backend**: Laravel 10 (PHP)
- **Frontend**: Vue.js 3 + Inertia.js
- **Styling**: Tailwind CSS
- **Maps**: Google Maps API
- **Database**: MySQL/PostgreSQL

## 📦 Installazione

1. **Clona il repository**
   ```bash
   git clone [repository-url]
   cd travel-record
   ```

2. **Installa le dipendenze**
   ```bash
   composer install
   npm install
   ```

3. **Configura l'ambiente**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configura il database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Avvia l'applicazione**
   ```bash
   npm run dev
   php artisan serve
   ```

## 🏗️ Struttura Frontend

```
resources/js/
├── Components/          # Componenti riutilizzabili
│   ├── UI/             # Componenti UI base
│   ├── Trip/           # Componenti per viaggi
│   ├── Friends/        # Componenti per amici
│   └── Profile/        # Componenti per profilo
├── Pages/              # Pagine dell'applicazione
│   ├── Home.vue        # Dashboard principale
│   ├── NewTrip.vue     # Creazione viaggio
│   ├── Map.vue         # Mappa globale
│   └── [altri]         # Altre pagine
└── Layouts/            # Layout dell'applicazione
```

## 🎯 Funzionalità Principali

### Viaggi
- ✅ Creazione viaggi con date e destinazioni
- ✅ Aggiunta luoghi tramite Google Maps
- ✅ Upload e gestione foto
- ✅ Hashtags personalizzati

### Social
- ✅ Sistema di follow/unfollow
- ✅ Profili pubblici e privati
- ✅ Ricerca utenti
- ✅ Richieste di amicizia

### Mappa
- ✅ Visualizzazione globale dei luoghi
- ✅ Marker personalizzati
- ✅ Info window con dettagli
- ✅ Navigazione ai viaggi

## 🔧 Configurazione

### Variabili d'Ambiente Necessarie

```env
GOOGLE_MAPS_API_KEY=your_google_maps_api_key
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travel_record
DB_USERNAME=root
DB_PASSWORD=
```

## 📱 Screenshots

*[Screenshots dell'applicazione]*

## 🤝 Contribuire

1. Fork il progetto
2. Crea un branch per la feature (`git checkout -b feature/AmazingFeature`)
3. Commit le modifiche (`git commit -m 'Add some AmazingFeature'`)
4. Push al branch (`git push origin feature/AmazingFeature`)
5. Apri una Pull Request

## 📄 Licenza

Questo progetto è sotto licenza MIT. Vedi il file `LICENSE` per maggiori dettagli.

## 👨‍💻 Autore

**Travel Record Team**

---

⭐ Se ti piace questo progetto, lascia una stella!

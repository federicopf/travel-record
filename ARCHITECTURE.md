# Architettura a Servizi - Travel Record

## Panoramica

Il backend è stato ristrutturato per utilizzare un'architettura a servizi che separa la logica di business dai controller. Questo rende il codice più mantenibile, testabile e organizzato.

## Struttura dei Servizi

### 1. TripService (`app/Services/TripService.php`)
Gestisce tutta la logica relativa ai viaggi:
- Creazione, lettura, aggiornamento e cancellazione dei viaggi
- Gestione delle autorizzazioni
- Formattazione dei dati per le viste
- Elaborazione delle immagini

**Metodi principali:**
- `getUserTrips(int $userId)` - Recupera tutti i viaggi di un utente
- `createTrip(array $data)` - Crea un nuovo viaggio
- `getTripWithDetails(Trip $trip)` - Recupera un viaggio con tutti i dettagli
- `updateTrip(Trip $trip, array $data)` - Aggiorna un viaggio
- `deleteTrip(Trip $trip)` - Elimina un viaggio
- `authorizeTrip(int $tripId)` - Verifica l'autorizzazione

### 2. PlaceService (`app/Services/PlaceService.php`)
Gestisce tutta la logica relativa ai luoghi:
- Operazioni CRUD sui luoghi
- Upload e gestione delle foto
- Gestione degli hashtag
- Validazioni di sicurezza

**Metodi principali:**
- `getPlaceWithDetails(Trip $trip, Place $place)` - Recupera un luogo con dettagli
- `updatePlace(Trip $trip, Place $place, array $data)` - Aggiorna un luogo
- `deletePlace(Trip $trip, Place $place)` - Elimina un luogo
- `uploadPhotos(Trip $trip, Place $place, array $files)` - Carica foto
- `deletePhoto(Trip $trip, Place $place, Photo $photo)` - Elimina una foto
- `setFavoritePhoto(Trip $trip, Photo $photo)` - Imposta foto preferita
- `updateHashtags(Trip $trip, Place $place, array $hashtagIds)` - Aggiorna hashtag

### 3. SocialService (`app/Services/SocialService.php`)
Gestisce la logica sociale e i profili pubblici:
- Profili pubblici degli utenti
- Gestione della privacy
- Logica di following
- Formattazione dati per viste pubbliche

**Metodi principali:**
- `getPublicProfile(string $username)` - Recupera profilo pubblico
- `getPublicTrip(string $username, Trip $trip)` - Recupera viaggio pubblico
- `getPublicTripPlace(string $username, Trip $trip, $place)` - Recupera luogo pubblico
- `getProfilePhoto(string $username)` - Recupera foto profilo

### 4. ProfileService (`app/Services/ProfileService.php`)
Gestisce la logica del profilo utente:
- Aggiornamento dati personali
- Gestione password
- Impostazioni privacy
- Upload foto profilo

**Metodi principali:**
- `updateName(User $user, array $data)` - Aggiorna nome e partner
- `updatePassword(User $user, array $data)` - Cambia password
- `updatePrivacy(User $user, array $data)` - Aggiorna privacy
- `updatePhoto(User $user, $photoFile)` - Carica foto profilo
- `changePassword(User $user, array $data)` - Cambia password (alternativo)

### 5. FollowService (`app/Services/FollowService.php`)
Gestisce la logica di following e amicizie:
- Gestione richieste di follow
- Ricerca utenti
- Accettazione/rifiuto richieste
- Operazioni follow/unfollow

**Metodi principali:**
- `getUserFriends(User $user)` - Recupera amici e conteggio richieste
- `searchUsers(string $query, User $currentUser)` - Cerca utenti
- `getPendingRequests(User $user)` - Richieste in attesa
- `acceptRequest(User $follower, User $followed)` - Accetta richiesta
- `rejectRequest(User $follower, User $followed)` - Rifiuta richiesta
- `followUser(User $currentUser, User $targetUser)` - Segue utente
- `unfollowUser(User $currentUser, User $targetUser)` - Smette di seguire

### 6. AuthService (`app/Services/AuthService.php`)
Gestisce l'autenticazione e registrazione:
- Login utenti
- Registrazione nuovi utenti
- Logout
- Cambio password

**Metodi principali:**
- `login(array $credentials)` - Autentica utente
- `register(array $data)` - Registra nuovo utente
- `logout()` - Disconnette utente
- `changePassword(User $user, array $data)` - Cambia password

### 7. MapService (`app/Services/MapService.php`)
Gestisce la logica della mappa:
- Dati luoghi per mappa
- Formattazione coordinate
- Gestione immagini luoghi

**Metodi principali:**
- `getUserMapData(int $userId)` - Recupera dati mappa utente
- `getPlaceImages($place)` - Immagini per luogo

### 8. ThemeService (`app/Services/ThemeService.php`)
Gestisce temi e preferenze utente:
- Cambio tema
- Cambio segnaposto mappa

**Metodi principali:**
- `changeTheme(User $user, int $themeId)` - Cambia tema
- `changeMapPointer(User $user, int $mapPointerId)` - Cambia segnaposto

## Come Usare i Servizi

### Nei Controller

```php
// Inizializzazione semplice con new
$tripService = new TripService();
$trips = $tripService->getUserTrips(Auth::id());

// Oppure con facciata (se configurata)
$trips = app(TripService::class)->getUserTrips(Auth::id());
```

### Esempio di Controller Refactorizzato

```php
class TripController extends Controller
{
    public function index()
    {
        $tripService = new TripService();
        $trips = $tripService->getUserTrips(Auth::id());

        return Inertia::render('Home', [
            'trips' => $trips,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'places' => 'required|array|min:1',
            // ... altre validazioni
        ]);

        $tripService = new TripService();
        $trip = $tripService->createTrip($validated);

        return redirect()->route('trip.show', ['trip' => $trip->id]);
    }
}
```

## Vantaggi dell'Architettura a Servizi

### 1. Separazione delle Responsabilità
- **Controller**: Gestiscono solo le richieste HTTP, validazione e risposte
- **Service**: Contengono tutta la logica di business
- **Model**: Gestiscono solo l'accesso ai dati

### 2. Riutilizzabilità
- I servizi possono essere utilizzati da diversi controller
- La logica di business è centralizzata e non duplicata

### 3. Testabilità
- I servizi possono essere testati indipendentemente
- Mock più semplici per i test unitari

### 4. Manutenibilità
- Codice più organizzato e facile da capire
- Modifiche localizzate nei servizi appropriati

### 5. Scalabilità
- Facile aggiungere nuove funzionalità
- Struttura modulare per future estensioni

## Best Practices

### 1. Inizializzazione
```php
// ✅ Corretto - Inizializzazione semplice
$tripService = new TripService();

// ✅ Corretto - Con facciata (se configurata)
$tripService = app(TripService::class);
```

### 2. Gestione Errori
I servizi gestiscono le autorizzazioni e validazioni internamente:
```php
// Il servizio gestisce automaticamente l'autorizzazione
$tripService->deleteTrip($trip); // Lancia eccezione se non autorizzato
```

### 3. Formattazione Dati
I servizi si occupano della formattazione per le viste:
```php
// Il servizio restituisce già i dati formattati
$tripData = $tripService->getTripWithDetails($trip);
return Inertia::render('Trip/Show', ['trip' => $tripData]);
```

## Struttura delle Cartelle

```
app/
├── Services/
│   ├── TripService.php
│   ├── PlaceService.php
│   ├── SocialService.php
│   ├── ProfileService.php
│   ├── FollowService.php
│   ├── AuthService.php
│   ├── MapService.php
│   └── ThemeService.php
├── Http/
│   └── Controllers/
│       ├── TripController.php (refactorizzato)
│       ├── PlaceController.php (refactorizzato)
│       ├── SocialController.php (refactorizzato)
│       ├── ProfileController.php (refactorizzato)
│       ├── FollowController.php (refactorizzato)
│       ├── AuthController.php (refactorizzato)
│       ├── MapController.php (refactorizzato)
│       └── ThemeController.php (refactorizzato)
└── Models/
    ├── Trip.php
    ├── Place.php
    ├── User.php
    └── Follow.php
```

## Migrazione dal Codice Vecchio

### Prima (Controller Fat)
```php
public function index()
{
    $trips = Trip::with(['places.photos','places.hashtags'])
        ->where('user_id', Auth::id())
        ->orderBy('start_date', 'desc')
        ->get()
        ->map(function ($trip) {
            // Logica di formattazione nel controller
            return [
                'id' => $trip->id,
                'title' => $trip->title,
                'start_date' => Carbon::parse($trip->start_date)->format('d/m/Y'),
                // ...
            ];
        });
}
```

### Dopo (Controller Thin)
```php
public function index()
{
    $tripService = new TripService();
    $trips = $tripService->getUserTrips(Auth::id());
    
    return Inertia::render('Home', ['trips' => $trips]);
}
```

## Prossimi Passi

1. ✅ **Completare la refactorizzazione** di tutti i controller rimanenti
2. **Aggiungere test unitari** per i servizi
3. **Implementare cache** per migliorare le performance
4. **Aggiungere logging** per debugging
5. **Documentare** tutti i metodi dei servizi
6. **Ottimizzare** le query database nei servizi
7. **Aggiungere validazione** più robusta nei servizi

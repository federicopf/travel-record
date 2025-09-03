// API Endpoints
export const API_ENDPOINTS = {
  // Auth
  LOGIN: '/login',
  REGISTER: '/register',
  LOGOUT: '/logout',
  CHANGE_PASSWORD: '/password/change',
  
  // Trips
  TRIPS: '/',
  TRIP: (id) => `/trip/${id}`,
  TRIP_EDIT: (id) => `/trip/${id}/edit`,
  TRIP_UPDATE: (id) => `/trip/${id}`,
  TRIP_DELETE: (id) => `/trip/${id}`,
  NEW_TRIP: '/new-trip',
  
  // Places
  PLACE: (tripId, placeId) => `/trip/${tripId}/place/${placeId}`,
  PLACE_EDIT: (tripId, placeId) => `/trip/${tripId}/place/${placeId}/edit`,
  PLACE_UPDATE: (tripId, placeId) => `/trip/${tripId}/place/${placeId}`,
  PLACE_DELETE: (tripId, placeId) => `/trip/${tripId}/place/${placeId}`,
  PLACE_PHOTO_UPLOAD: (tripId, placeId) => `/trip/${tripId}/place/${placeId}/photo`,
  PLACE_PHOTO_DELETE: (tripId, placeId, photoId) => `/trip/${tripId}/place/${placeId}/photo/${photoId}`,
  PLACE_HASHTAGS: (tripId, placeId) => `/trip/${tripId}/place/${placeId}/hashtags`,
  PLACE_ADD: (tripId) => `/trip/${tripId}/place/add`,
  
  // Profile
  PROFILE: (userId) => `/user/${userId}/profile`,
  PROFILE_NAME: (userId) => `/user/${userId}/profile/name`,
  PROFILE_PASSWORD: (userId) => `/user/${userId}/profile/password`,
  PROFILE_PRIVACY: (userId) => `/user/${userId}/profile/privacy`,
  PROFILE_PHOTO: (userId) => `/user/${userId}/profile/photo`,
  
  // Friends
  FRIENDS: '/friends',
  FRIENDS_SEARCH: '/friends/search',
  FRIENDS_REQUESTS: '/friends/requests',
  FOLLOW: (userId) => `/friends/follow/${userId}`,
  UNFOLLOW: (userId) => `/friends/unfollow/${userId}`,
  ACCEPT_REQUEST: (userId) => `/friends/accept/${userId}`,
  REJECT_REQUEST: (userId) => `/friends/reject/${userId}`,
  
  // Social
  PUBLIC_PROFILE: (username) => `/profile/${username}`,
  PUBLIC_PROFILE_PHOTO: (username) => `/profile/${username}/photo`,
  PUBLIC_TRIP: (username, tripId) => `/profile/${username}/trip/${tripId}`,
  PUBLIC_TRIP_PLACE: (username, tripId, placeId) => `/profile/${username}/trip/${tripId}/place/${placeId}`,
  
  // Map
  MAP: '/map',
  
  // Theme
  CHANGE_THEME: '/change-theme',
  CHANGE_MAP_POINTER: '/change-map-pointer',
}

// File Upload
export const FILE_UPLOAD = {
  MAX_SIZE: 5 * 1024 * 1024, // 5MB
  ALLOWED_TYPES: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
  ALLOWED_EXTENSIONS: ['.jpg', '.jpeg', '.png', '.webp', '.gif'],
  MAX_FILES: 30,
}

// Validation Rules
export const VALIDATION_RULES = {
  USERNAME: {
    min: 5,
    max: 255,
    pattern: /^[a-zA-Z0-9]+$/,
  },
  PASSWORD: {
    min: 8,
  },
  EMAIL: {
    max: 255,
  },
  NAME: {
    max: 255,
  },
  TRIP_TITLE: {
    max: 255,
  },
  PLACE_NAME: {
    max: 255,
  },
}

// Theme Constants
export const THEMES = {
  LIGHT: 1,
  DARK: 2,
  AUTO: 3,
}

// Map Pointer Constants
export const MAP_POINTERS = {
  DEFAULT: 0,
  CUSTOM_1: 1,
  CUSTOM_2: 2,
  CUSTOM_3: 3,
}

// Status Constants
export const STATUS = {
  PENDING: 'pending',
  ACCEPTED: 'accepted',
  REJECTED: 'rejected',
}

// User Types
export const USER_TYPES = {
  INDIVIDUAL: 'individual',
  COUPLE: 'couple',
}

// Error Messages
export const ERROR_MESSAGES = {
  VALIDATION_FAILED: 'I dati inseriti non sono validi',
  UNAUTHORIZED: 'Non sei autorizzato a eseguire questa azione',
  NOT_FOUND: 'Risorsa non trovata',
  SERVER_ERROR: 'Errore del server',
  NETWORK_ERROR: 'Errore di connessione',
  FILE_TOO_LARGE: 'Il file è troppo grande',
  INVALID_FILE_TYPE: 'Tipo di file non supportato',
  PASSWORD_MISMATCH: 'Le password non coincidono',
  CURRENT_PASSWORD_INCORRECT: 'La password attuale non è corretta',
}

// Success Messages
export const SUCCESS_MESSAGES = {
  TRIP_CREATED: 'Viaggio creato con successo!',
  TRIP_UPDATED: 'Viaggio aggiornato con successo!',
  TRIP_DELETED: 'Viaggio eliminato con successo!',
  PLACE_CREATED: 'Luogo aggiunto con successo!',
  PLACE_UPDATED: 'Luogo aggiornato con successo!',
  PLACE_DELETED: 'Luogo eliminato con successo!',
  PHOTO_UPLOADED: 'Foto caricata con successo!',
  PHOTO_DELETED: 'Foto eliminata con successo!',
  PROFILE_UPDATED: 'Profilo aggiornato con successo!',
  PASSWORD_CHANGED: 'Password cambiata con successo!',
  FOLLOW_REQUEST_SENT: 'Richiesta di follow inviata!',
  FOLLOW_REQUEST_ACCEPTED: 'Richiesta di follow accettata!',
  FOLLOW_REQUEST_REJECTED: 'Richiesta di follow rifiutata!',
  USER_UNFOLLOWED: 'Utente rimosso dai seguiti!',
  THEME_CHANGED: 'Tema cambiato con successo!',
  MAP_POINTER_CHANGED: 'Segnaposto mappa aggiornato!',
}

import { router } from '@inertiajs/vue3'
import { ERROR_MESSAGES } from '@/Utils/Helpers/Constants'

// Base API class
class ApiClient {
  constructor() {
    this.baseURL = ''
    this.defaultHeaders = {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    }
  }

  // GET request
  async get(url, params = {}) {
    try {
      const response = await router.get(url, params, {
        preserveState: true,
        preserveScroll: true,
      })
      return this.handleResponse(response)
    } catch (error) {
      return this.handleError(error)
    }
  }

  // POST request
  async post(url, data = {}) {
    try {
      const response = await router.post(url, data)
      return this.handleResponse(response)
    } catch (error) {
      return this.handleError(error)
    }
  }

  // PUT request
  async put(url, data = {}) {
    try {
      const response = await router.put(url, data)
      return this.handleResponse(response)
    } catch (error) {
      return this.handleError(error)
    }
  }

  // DELETE request
  async delete(url) {
    try {
      const response = await router.delete(url)
      return this.handleResponse(response)
    } catch (error) {
      return this.handleError(error)
    }
  }

  // File upload
  async upload(url, formData) {
    try {
      const response = await router.post(url, formData, {
        forceFormData: true,
      })
      return this.handleResponse(response)
    } catch (error) {
      return this.handleError(error)
    }
  }

  // Handle successful response
  handleResponse(response) {
    return {
      success: true,
      data: response,
      message: response?.props?.flash?.success || 'Operazione completata con successo',
    }
  }

  // Handle error response
  handleError(error) {
    console.error('API Error:', error)
    
    let message = ERROR_MESSAGES.SERVER_ERROR
    
    if (error.response) {
      const status = error.response.status
      
      switch (status) {
        case 401:
          message = ERROR_MESSAGES.UNAUTHORIZED
          break
        case 403:
          message = ERROR_MESSAGES.UNAUTHORIZED
          break
        case 404:
          message = ERROR_MESSAGES.NOT_FOUND
          break
        case 422:
          message = ERROR_MESSAGES.VALIDATION_FAILED
          break
        case 500:
          message = ERROR_MESSAGES.SERVER_ERROR
          break
        default:
          message = error.response.data?.message || ERROR_MESSAGES.SERVER_ERROR
      }
    } else if (error.request) {
      message = ERROR_MESSAGES.NETWORK_ERROR
    }

    return {
      success: false,
      error: message,
      details: error,
    }
  }
}

// Create API instance
const api = new ApiClient()

// Export API methods
export const apiClient = {
  // Auth
  login: (credentials) => api.post('/login', credentials),
  register: (data) => api.post('/register', data),
  logout: () => api.post('/logout'),
  changePassword: (data) => api.post('/password/change', data),

  // Trips
  getTrips: () => api.get('/'),
  getTrip: (id) => api.get(`/trip/${id}`),
  createTrip: (data) => api.post('/new-trip', data),
  updateTrip: (id, data) => api.put(`/trip/${id}`, data),
  deleteTrip: (id) => api.delete(`/trip/${id}`),

  // Places
  getPlace: (tripId, placeId) => api.get(`/trip/${tripId}/place/${placeId}`),
  updatePlace: (tripId, placeId, data) => api.put(`/trip/${tripId}/place/${placeId}`, data),
  deletePlace: (tripId, placeId) => api.delete(`/trip/${tripId}/place/${placeId}`),
  addPlace: (tripId, data) => api.post(`/trip/${tripId}/place/add`, data),
  uploadPhotos: (tripId, placeId, formData) => api.upload(`/trip/${tripId}/place/${placeId}/photo`, formData),
  deletePhoto: (tripId, placeId, photoId) => api.delete(`/trip/${tripId}/place/${placeId}/photo/${photoId}`),
  updateHashtags: (tripId, placeId, data) => api.post(`/trip/${tripId}/place/${placeId}/hashtags`, data),

  // Profile
  updateProfileName: (userId, data) => api.put(`/user/${userId}/profile/name`, data),
  updateProfilePassword: (userId, data) => api.put(`/user/${userId}/profile/password`, data),
  updateProfilePrivacy: (userId, data) => api.put(`/user/${userId}/profile/privacy`, data),
  updateProfilePhoto: (userId, formData) => api.upload(`/user/${userId}/profile/photo`, formData),

  // Friends
  getFriends: () => api.get('/friends'),
  searchFriends: (query) => api.get('/friends/search', { q: query }),
  getFriendRequests: () => api.get('/friends/requests'),
  followUser: (userId) => api.post(`/friends/follow/${userId}`),
  unfollowUser: (userId) => api.delete(`/friends/unfollow/${userId}`),
  acceptRequest: (userId) => api.put(`/friends/accept/${userId}`),
  rejectRequest: (userId) => api.delete(`/friends/reject/${userId}`),

  // Theme
  changeTheme: (data) => api.post('/change-theme', data),
  changeMapPointer: (data) => api.post('/change-map-pointer', data),
}

export default apiClient

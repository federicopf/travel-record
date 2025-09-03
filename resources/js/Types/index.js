// Auth Types
export interface User {
  id: number
  name: string
  email: string
  username: string
  partner_name?: string
  private_profile: boolean
  profile_photo?: string
  theme_id: number
  map_pointer_id: number
  created_at: string
  updated_at: string
}

export interface LoginCredentials {
  username: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  username: string
  password: string
  password_confirmation: string
  type: 'individual' | 'couple'
  partner_name?: string
}

// Trip Types
export interface Trip {
  id: number
  title: string
  start_date: string
  end_date: string
  image?: string
  user_id: number
  places: Place[]
  created_at: string
  updated_at: string
}

export interface Place {
  id: number
  name: string
  address?: string
  lat: number
  lng: number
  trip_id: number
  photos: Photo[]
  hashtags: Hashtag[]
  created_at: string
  updated_at: string
}

export interface Photo {
  id: number
  path: string
  place_id: number
  created_at: string
  updated_at: string
}

export interface Hashtag {
  id: number
  name: string
  color: string
  created_at: string
  updated_at: string
}

// Profile Types
export interface ProfileSettings {
  name: string
  partner_name?: string
  private_profile: boolean
}

export interface PasswordChange {
  current_password: string
  password: string
  password_confirmation: string
}

// Friends Types
export interface Friend {
  id: number
  name: string
  username: string
  profile_photo?: string
  private_profile: boolean
}

export interface FollowRequest {
  id: number
  follower: Friend
  followed_id: number
  status: 'pending' | 'accepted' | 'rejected'
  created_at: string
}

// Map Types
export interface MapMarker {
  id: number
  name: string
  lat: number
  lng: number
  trip: string
  trip_id: number
  start_date: string
  end_date: string
  images: string[]
}

// API Response Types
export interface ApiResponse<T> {
  data: T
  message?: string
  success: boolean
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

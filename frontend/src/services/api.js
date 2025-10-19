import axios from 'axios';

// Force the correct API URL for Cloudflare Tunnel
const API_BASE_URL = 'https://alloy-shaped-composed-southern.trycloudflare.com/api';

// Always log the API URL being used (for debugging)
console.log('🌐 FORCED API Base URL:', API_BASE_URL);
console.log('🔧 Environment Check:', {
  REACT_APP_API_BASE_URL: process.env.REACT_APP_API_BASE_URL,
  NODE_ENV: process.env.NODE_ENV,
  FORCED_URL: API_BASE_URL
});

// Create axios instance
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add token to requests if available
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle token expiration
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Auth API calls
export const authAPI = {
  register: (userData) => api.post('/register', userData),
  login: (credentials) => api.post('/login', credentials),
};

// Software API calls
export const softwareAPI = {
  getAll: () => {
    console.log('🔍 Making API call to:', `${API_BASE_URL}/software`);
    return api.get('/software').catch(error => {
      console.error('❌ API Error:', error.message);
      console.error('❌ Full Error:', error);
      throw error;
    });
  },
  download: (softwareIds) => api.post('/download', { software_ids: softwareIds }),
};

export default api;
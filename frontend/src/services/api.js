import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

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
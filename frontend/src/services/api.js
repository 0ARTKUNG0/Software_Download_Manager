import axios from 'axios';

// const API_BASE_URL = process.env.REACT_APP_API_BASE_URL;
const API_BASE_URL = 'https://app-clearly-screening-halifax.trycloudflare.com/api';

console.log('🌐 API Base URL:', API_BASE_URL);
console.log('🔧 Environment:', process.env.NODE_ENV);

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

// ========================================
// File Download Helper
// ========================================
export async function downloadFile(url, method = 'GET', body = null) {
  try {
    const res = await api.request({
      url,
      method,
      data: body,
      responseType: 'blob',
    });

    // Extract filename from Content-Disposition header
    let filename = 'download.bin';
    const cd = res.headers['content-disposition'];
    if (cd) {
      const match = cd.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
      if (match && match[1]) {
        filename = match[1].replace(/['"]/g, '');
        filename = decodeURIComponent(filename);
      }
    }

    // Create blob URL and trigger download
    const blobUrl = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = blobUrl;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(blobUrl);

    return { success: true, filename };
  } catch (error) {
    console.error('❌ Download Error:', error.message);
    throw error;
  }
}

// ========================================
// Auth API
// ========================================
export const authAPI = {
  register: (userData) => api.post('/register', userData),
  login: (credentials) => api.post('/login', credentials),
  logout: () => api.post('/logout'),
  me: () => api.get('/me'),
};

// ========================================
// Software API
// ========================================
export const softwareAPI = {
  getAll: () => {
    console.log('🔍 Fetching software list from:', `${API_BASE_URL}/software`);
    return api.get('/software').catch(error => {
      console.error('❌ API Error:', error.message);
      throw error;
    });
  },
  download: (softwareIds) => api.post('/download', { software_ids: softwareIds }),
};

// ========================================
// Bundle API
// ========================================
export const bundleAPI = {
  // List all user bundles
  list: () => api.get('/bundles').then(r => r.data),
  
  // Create new bundle
  create: (name, software_ids, is_default = false) => 
    api.post('/bundles', { name, software_ids, is_default }).then(r => r.data),
  
  // Get single bundle details
  get: (id) => api.get(`/bundles/${id}`).then(r => r.data),
  
  // Update bundle
  update: (id, payload) => api.put(`/bundles/${id}`, payload).then(r => r.data),
  
  // Delete bundle
  remove: (id) => api.delete(`/bundles/${id}`).then(r => r.data),
  
  // Download bundle as ZIP
  downloadZip: (id) => downloadFile(`/bundles/${id}/download`, 'POST'),
  
  // Export PowerShell script
  exportScript: (id) => downloadFile(`/bundles/${id}/export-script`, 'POST'),
};

// ========================================
// Download API
// ========================================
export const downloadAPI = {
  // Download single file
  single: (id) => downloadFile(`/download-file/${id}`, 'GET'),
  
  // Download multiple files as ZIP
  multiple: (software_ids) => downloadFile('/download-multiple', 'POST', { software_ids }),
};

export default api;
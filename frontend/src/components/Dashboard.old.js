import React, { useState, useEffect, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import { softwareAPI } from '../services/api';

const Dashboard = () => {
  const [software, setSoftware] = useState([]);
  const [filteredSoftware, setFilteredSoftware] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('All');
  const [selectedItems, setSelectedItems] = useState([]);
  const [downloading, setDownloading] = useState(false);
  const hasFetched = useRef(false);

  const navigate = useNavigate();
  const categories = ['All', 'Browsers', 'Development', 'Utilities', 'Media', 'Gaming'];

  useEffect(() => {
    fetchSoftware();
  }, []);

  useEffect(() => {
    if (!Array.isArray(software) || software.length === 0) {
      setFilteredSoftware([]);
      return;
    }
    
    let filtered = [...software]; // Create a copy to avoid mutations
    
    if (selectedCategory !== 'All') {
      filtered = filtered.filter(
        (item) => item.category?.toLowerCase() === selectedCategory.toLowerCase()
      );
    }
    
    if (searchTerm) {
      filtered = filtered.filter(
        (item) =>
          item.name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
          item.description?.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }
    
    setFilteredSoftware(filtered);
  }, [software, searchTerm, selectedCategory]);

  const fetchSoftware = async () => {
    // Prevent multiple calls with hasFetched ref
    if (hasFetched.current) return;
    hasFetched.current = true;
    
    setLoading(true);
    
    try {
      const response = await softwareAPI.getAll();
      const softwareData = response.data.software || response.data || [];
      setSoftware(Array.isArray(softwareData) ? softwareData : []);
      setError('');
    } catch (err) {
      console.error('Error fetching software:', err);
      setSoftware([]);
      setError('Failed to connect to backend server. Please make sure the backend is running on http://localhost:8000');
    }
    
    setLoading(false);
  };

  const handleItemSelect = (id) => {
    setSelectedItems((prev) =>
      prev.includes(id) ? prev.filter((item) => item !== id) : [...prev, id]
    );
  };

  const handleDownload = async (softwareIds) => {
    setDownloading(true);
    const token = localStorage.getItem('token');
    // Force Cloudflare URL for downloads
    const BASE_URL = 'https://alloy-shaped-composed-southern.trycloudflare.com';
    
    console.log('🔗 Using download URL:', BASE_URL);
    
    try {
      const ids = Array.isArray(softwareIds) ? softwareIds : [softwareIds];
      
      if (ids.length === 1) {
        // Single file download
        const downloadUrl = `${BASE_URL}/api/download-file/${ids[0]}?token=${token}`;
        console.log('📥 Single download URL:', downloadUrl);
        window.open(downloadUrl, '_blank');
      } else {
        // Multiple files download as ZIP - show progress message
        console.log('📦 Starting ZIP creation for', ids.length, 'files...');
        
        const response = await fetch(`${BASE_URL}/api/download-multiple`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({
            software_ids: ids
          })
        });
        
        if (response.ok) {
          console.log('✅ ZIP created successfully, starting download...');
          // Get the blob and create download
          const blob = await response.blob();
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'software_bundle.zip'; // Updated ZIP name
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
          console.log('📥 Download started successfully');
        } else {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Download failed');
        }
      }
      
      // Clear selections if multiple items were downloaded
      if (Array.isArray(softwareIds)) {
        setSelectedItems([]);
      }
      
    } catch (err) {
      console.error('Download error:', err);
      alert('Download failed. Please make sure the backend server is running and try again.');
    } finally {
      setDownloading(false);
    }
  };

  const handleLogout = () => {
    localStorage.removeItem('token');
    navigate('/login');
  };

  const formatFileSize = (bytes) => {
    if (!bytes) return 'Unknown';
    const mb = bytes / (1024 * 1024);
    return `${mb.toFixed(1)} MB`;
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-lg text-gray-600">Loading software...</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-4">
            <h1 className="text-2xl font-bold text-gray-900">
              Software Download Manager
            </h1>
            <div className="flex items-center space-x-4">
              <span className="text-gray-600">Welcome back!</span>
              <button
                onClick={handleLogout}
                className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      </header>

      {/* Main content */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {error && (
          <div className="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <div className="flex items-center">
              <span className="font-medium">⚠️ {error}</span>
            </div>
            <p className="text-sm mt-1">
              You can still browse and download software using the demo data below.
            </p>
          </div>
        )}

        {/* Filters */}
        <div className="bg-white rounded-lg shadow-md p-6 mb-8">
          <div className="flex flex-col md:flex-row gap-4">
            <div className="flex-1">
              <input
                type="text"
                placeholder="Search software..."
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>
            <div>
              <select
                className="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={selectedCategory}
                onChange={(e) => setSelectedCategory(e.target.value)}
              >
                {categories.map((category) => (
                  <option key={category} value={category}>
                    {category}
                  </option>
                ))}
              </select>
            </div>
            {selectedItems.length > 0 && (
              <button
                onClick={() => handleDownload(selectedItems)}
                disabled={downloading}
                className="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium disabled:opacity-50"
              >
                {downloading
                  ? 'Downloading...'
                  : `Download Selected (${selectedItems.length})`}
              </button>
            )}
          </div>
        </div>

        {/* Software Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {filteredSoftware.map((item) => (
            <div
              key={item.id}
              className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
            >
              <div className="p-6">
                <div className="flex items-start justify-between mb-3">
                  <h3 className="text-lg font-semibold text-gray-900 line-clamp-2">
                    {item.name}
                  </h3>
                  <input
                    type="checkbox"
                    checked={selectedItems.includes(item.id)}
                    onChange={() => handleItemSelect(item.id)}
                    className="ml-2 mt-1 text-blue-600"
                  />
                </div>
                <p className="text-gray-600 text-sm mb-3 line-clamp-3">
                  {item.description}
                </p>
                <div className="mb-4">
                  <span className="text-sm text-gray-500">
                    Size: {formatFileSize(item.size)}
                  </span>
                  {item.category && (
                    <span className="ml-3 inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                      {item.category}
                    </span>
                  )}
                </div>
                <div className="flex space-x-2">
                  {item.link && (
                    <a
                      href={item.link}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-4 rounded text-sm font-medium"
                    >
                      Visit Site
                    </a>
                  )}
                  <button
                    onClick={() => handleDownload(item.id)}
                    disabled={downloading}
                    className="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded text-sm font-medium disabled:opacity-50"
                  >
                    Download
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Empty state */}
        {filteredSoftware.length === 0 && !loading && (
          <div className="text-center py-12">
            <p className="text-gray-500 text-lg">No software found matching your criteria.</p>
            {error && (
              <p className="text-gray-400 text-sm mt-2">
                Make sure the backend server is running and the database is properly configured.
              </p>
            )}
          </div>
        )}
      </main>
    </div>
  );
};

export default Dashboard;

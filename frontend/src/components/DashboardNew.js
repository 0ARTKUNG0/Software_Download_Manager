import React, { useState, useEffect, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import { softwareAPI, bundleAPI, downloadAPI } from '../services/api';

const Dashboard = () => {
  const [software, setSoftware] = useState([]);
  const [filteredSoftware, setFilteredSoftware] = useState([]);
  const [bundles, setBundles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('All');
  const [selectedIds, setSelectedIds] = useState(new Set());
  const [downloading, setDownloading] = useState(false);
  
  // Bundle UI state
  const [showBundleModal, setShowBundleModal] = useState(false);
  const [showManageBundles, setShowManageBundles] = useState(false);
  const [bundleName, setBundleName] = useState('');
  const [savingBundle, setSavingBundle] = useState(false);
  const [toast, setToast] = useState(null);
  
  const hasFetched = useRef(false);
  const navigate = useNavigate();
  const categories = ['All', 'Browser', 'Development', 'Utility', 'Media', 'Gaming', 'Document', 'Productivity', 'Communication'];

  useEffect(() => {
    fetchData();
  }, []);

  useEffect(() => {
    if (!Array.isArray(software) || software.length === 0) {
      setFilteredSoftware([]);
      return;
    }
    
    let filtered = [...software];
    
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

  const fetchData = async () => {
    if (hasFetched.current) return;
    hasFetched.current = true;
    
    setLoading(true);
    
    try {
      const [softwareRes, bundlesRes] = await Promise.all([
        softwareAPI.getAll(),
        bundleAPI.list().catch(() => ({ bundles: [] }))
      ]);
      
      const softwareData = softwareRes.data.software || softwareRes.data || [];
      setSoftware(Array.isArray(softwareData) ? softwareData : []);
      setBundles(bundlesRes.bundles || []);
      setError('');
    } catch (err) {
      console.error('Error fetching data:', err);
      setSoftware([]);
      setError('Failed to connect to backend. Make sure server is running on http://localhost:8000');
    }
    
    setLoading(false);
  };

  const showToast = (message, type = 'success') => {
    setToast({ message, type });
    setTimeout(() => setToast(null), 3000);
  };

  const handleItemSelect = (id) => {
    setSelectedIds((prev) => {
      const newSet = new Set(prev);
      if (newSet.has(id)) {
        newSet.delete(id);
      } else {
        newSet.add(id);
      }
      return newSet;
    });
  };

  const handleDownloadSelected = async () => {
    if (selectedIds.size === 0) return;
    
    setDownloading(true);
    try {
      const idsArray = Array.from(selectedIds);
      
      if (idsArray.length === 1) {
        await downloadAPI.single(idsArray[0]);
        showToast('Download started!');
      } else {
        await downloadAPI.multiple(idsArray);
        showToast(`Downloading ${idsArray.length} files as ZIP`);
      }
      
      setSelectedIds(new Set());
    } catch (err) {
      console.error('Download error:', err);
      showToast('Download failed. Please try again.', 'error');
    } finally {
      setDownloading(false);
    }
  };

  const handleSaveBundle = async () => {
    if (!bundleName.trim()) {
      showToast('Please enter a bundle name', 'error');
      return;
    }
    
    if (selectedIds.size === 0) {
      showToast('Please select at least one software', 'error');
      return;
    }
    
    setSavingBundle(true);
    try {
      const result = await bundleAPI.create(bundleName, Array.from(selectedIds));
      setBundles((prev) => [result.bundle, ...prev]);
      showToast('Bundle saved successfully!');
      setShowBundleModal(false);
      setBundleName('');
    } catch (err) {
      console.error('Save bundle error:', err);
      showToast('Failed to save bundle', 'error');
    } finally {
      setSavingBundle(false);
    }
  };

  const handleApplyBundle = (bundle) => {
    setSelectedIds(new Set(bundle.software_ids));
    showToast(`Applied bundle: ${bundle.name}`);
  };

  const handleDownloadBundle = async (bundle) => {
    setDownloading(true);
    try {
      await bundleAPI.downloadZip(bundle.id);
      showToast(`Downloading bundle: ${bundle.name}`);
    } catch (err) {
      console.error('Download bundle error:', err);
      showToast('Failed to download bundle', 'error');
    } finally {
      setDownloading(false);
    }
  };

  const handleDeleteBundle = async (bundleId) => {
    if (!window.confirm('Are you sure you want to delete this bundle?')) return;
    
    try {
      await bundleAPI.remove(bundleId);
      setBundles((prev) => prev.filter((b) => b.id !== bundleId));
      showToast('Bundle deleted');
    } catch (err) {
      console.error('Delete bundle error:', err);
      showToast('Failed to delete bundle', 'error');
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
      <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
        <div className="flex flex-col items-center space-y-4">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
          <div className="text-lg text-gray-600">Loading...</div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
      {/* Toast Notification */}
      {toast && (
        <div className={`fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
          toast.type === 'error' ? 'bg-red-500' : 'bg-green-500'
        } text-white animate-fade-in`}>
          {toast.message}
        </div>
      )}

      {/* Header */}
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-4">
            <h1 className="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
              📦 Software Download Manager
            </h1>
            <div className="flex items-center space-x-4">
              <button
                onClick={() => setShowManageBundles(!showManageBundles)}
                className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition"
              >
                {showManageBundles ? 'Hide' : 'Manage'} Bundles ({bundles.length})
              </button>
              <button
                onClick={handleLogout}
                className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition"
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
          <div className="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-6">
            <div className="flex">
              <div className="flex-shrink-0">
                <svg className="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                </svg>
              </div>
              <div className="ml-3">
                <p className="text-sm text-yellow-700">{error}</p>
              </div>
            </div>
          </div>
        )}

        {/* Manage Bundles Panel */}
        {showManageBundles && (
          <div className="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 className="text-xl font-bold mb-4">My Bundles</h2>
            {bundles.length === 0 ? (
              <p className="text-gray-500 text-center py-4">No bundles yet. Select software and save as bundle!</p>
            ) : (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {bundles.map((bundle) => (
                  <div key={bundle.id} className="border rounded-lg p-4 hover:shadow-md transition">
                    <div className="flex justify-between items-start mb-2">
                      <h3 className="font-semibold text-gray-900">{bundle.name}</h3>
                      {bundle.is_default && (
                        <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Default</span>
                      )}
                    </div>
                    <p className="text-sm text-gray-500 mb-3">{bundle.item_count} items</p>
                    <div className="flex space-x-2">
                      <button
                        onClick={() => handleApplyBundle(bundle)}
                        className="flex-1 bg-blue-100 hover:bg-blue-200 text-blue-700 py-1 px-2 rounded text-sm transition"
                      >
                        Apply
                      </button>
                      <button
                        onClick={() => handleDownloadBundle(bundle)}
                        className="flex-1 bg-green-100 hover:bg-green-200 text-green-700 py-1 px-2 rounded text-sm transition"
                      >
                        Download
                      </button>
                      <button
                        onClick={() => handleDeleteBundle(bundle.id)}
                        className="bg-red-100 hover:bg-red-200 text-red-700 py-1 px-2 rounded text-sm transition"
                      >
                        🗑️
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        )}

        {/* Filters & Actions */}
        <div className="bg-white rounded-lg shadow-md p-6 mb-8">
          <div className="flex flex-col lg:flex-row gap-4">
            <div className="flex-1">
              <input
                type="text"
                placeholder="🔍 Search software..."
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
            {selectedIds.size > 0 && (
              <>
                <button
                  onClick={() => setShowBundleModal(true)}
                  className="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium transition"
                >
                  💾 Save as Bundle
                </button>
                <button
                  onClick={handleDownloadSelected}
                  disabled={downloading}
                  className="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium disabled:opacity-50 transition"
                >
                  {downloading ? '⏳ Downloading...' : `📥 Download (${selectedIds.size})`}
                </button>
              </>
            )}
          </div>
          
          {selectedIds.size > 0 && (
            <div className="mt-4 flex items-center justify-between bg-blue-50 px-4 py-2 rounded-md">
              <span className="text-blue-700 font-medium">{selectedIds.size} selected</span>
              <button
                onClick={() => setSelectedIds(new Set())}
                className="text-blue-600 hover:text-blue-800 text-sm font-medium"
              >
                Clear all
              </button>
            </div>
          )}
        </div>

        {/* Software Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {filteredSoftware.map((item) => (
            <div
              key={item.id}
              className={`bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all transform hover:-translate-y-1 ${
                selectedIds.has(item.id) ? 'ring-2 ring-blue-500' : ''
              }`}
            >
              <div className="p-6">
                <div className="flex items-start justify-between mb-3">
                  <h3 className="text-lg font-semibold text-gray-900 line-clamp-2 flex-1">
                    {item.name}
                  </h3>
                  <input
                    type="checkbox"
                    checked={selectedIds.has(item.id)}
                    onChange={() => handleItemSelect(item.id)}
                    className="ml-2 mt-1 h-5 w-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <p className="text-gray-600 text-sm mb-3 line-clamp-3">
                  {item.description}
                </p>
                <div className="mb-4 flex items-center justify-between">
                  <span className="text-sm text-gray-500">
                    📦 {formatFileSize(item.file_size)}
                  </span>
                  {item.category && (
                    <span className="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                      {item.category}
                    </span>
                  )}
                </div>
                <button
                  onClick={() => downloadAPI.single(item.id)}
                  disabled={downloading}
                  className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded text-sm font-medium disabled:opacity-50 transition"
                >
                  Download
                </button>
              </div>
            </div>
          ))}
        </div>

        {/* Empty state */}
        {filteredSoftware.length === 0 && !loading && (
          <div className="text-center py-12 bg-white rounded-lg shadow-md">
            <p className="text-gray-500 text-lg">No software found matching your criteria.</p>
          </div>
        )}
      </main>

      {/* Bundle Save Modal */}
      {showBundleModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 className="text-xl font-bold mb-4">Save as Bundle</h2>
            <p className="text-gray-600 mb-4">
              Save {selectedIds.size} selected software as a bundle for quick access later.
            </p>
            <input
              type="text"
              placeholder="Bundle name (e.g., 'Developer Setup')"
              className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4"
              value={bundleName}
              onChange={(e) => setBundleName(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && handleSaveBundle()}
            />
            <div className="flex space-x-3">
              <button
                onClick={() => setShowBundleModal(false)}
                className="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded font-medium transition"
              >
                Cancel
              </button>
              <button
                onClick={handleSaveBundle}
                disabled={savingBundle}
                className="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded font-medium disabled:opacity-50 transition"
              >
                {savingBundle ? 'Saving...' : 'Save Bundle'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default Dashboard;

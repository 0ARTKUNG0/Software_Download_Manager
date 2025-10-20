# 🚀 Software Download Manager

A modern, full-stack web application for managing and distributing software installers with secure authentication, bulk downloads, and customizable setup bundles.

![Tech Stack](https://img.shields.io/badge/Laravel-10-red)
![Tech Stack](https://img.shields.io/badge/React-18-blue)
![Tech Stack](https://img.shields.io/badge/PHP-8.1+-purple)
![Tech Stack](https://img.shields.io/badge/MySQL-8.0+-orange)

## 📋 Table of Contents
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Quick Start](#-quick-start)
- [Manual Setup](#-manual-setup)
- [API Documentation](#-api-documentation)
- [Usage Guide](#-usage-guide)
- [Troubleshooting](#-troubleshooting)

## ✨ Features

### 🔐 Authentication System
- **User Registration & Login** with JWT tokens
- **Protected Routes** - Dashboard access requires authentication
- **Auto Token Refresh** - Seamless user experience
- **Secure Logout** - Proper token cleanup

### 📦 Software Management
- **Software Catalog** - Browse available installers with rich metadata
- **Smart Search** - Find software by name or description
- **Category Filtering** - Filter by Browsers, Development, Media, Gaming, Utilities
- **File Information** - Display file sizes, categories, and descriptions

### 🎁 Setup Bundles (NEW!)
- **Create Custom Bundles** - Save frequently used software combinations
- **Quick Apply** - Load saved bundles with one click
- **Bundle Management** - Edit, delete, and organize your setup profiles
- **Download Bundles** - Get all bundle software as single ZIP file
- **Export Scripts** - Generate PowerShell installation scripts for bundles

### 📥 Download System
- **Single Downloads** - Click to download individual installers
- **Bulk ZIP Downloads** - Select multiple files, get "SDM.zip" bundle
- **Streaming ZIP Creation** - Fast downloads without temporary files
- **Clean Filenames** - Downloads show only installer names, no server paths
- **Real File Storage** - Actual installer files stored securely in backend
- **Progress Indicators** - Visual feedback during downloads

### 🎨 Modern UI/UX
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Gradient Design** - Modern blue-to-indigo gradient interface
- **Toast Notifications** - Real-time feedback for all actions
- **Loading States** - Smooth user experience
- **Error Handling** - User-friendly error messages
- **Interactive Cards** - Hover effects and smooth transitions
- **Selection State** - Visual indicators for selected software

## 🛠 Tech Stack

### Backend
- **Framework:** Laravel 10 (PHP 8.1+)
- **Database:** MySQL 8.0+
- **Authentication:** tymon/jwt-auth (JWT tokens)
- **ZIP Streaming:** stechstudio/laravel-zipstream v5.7
- **Storage:** Local file system
- **API:** RESTful JSON API

### Frontend
- **Framework:** React 18
- **Routing:** React Router v7
- **Styling:** Tailwind CSS 3
- **HTTP Client:** Axios
- **Build Tool:** Create React App

### Development Tools
- **Package Managers:** Composer, npm
- **Automation:** Windows Batch scripts
- **Version Control:** Git

## 📁 Project Structure

```
Software_Download_Manager/
├── 📂 backend/                          # Laravel API Backend
│   ├── 📂 app/
│   │   ├── 📂 Http/Controllers/
│   │   │   ├── AuthController.php       # User authentication
│   │   │   ├── SoftwareController.php   # Software CRUD
│   │   │   ├── BundleController.php     # Bundle management (NEW)
│   │   │   └── DownloadController.php   # File downloads & ZIP streaming
│   │   └── 📂 Models/
│   │       ├── User.php                 # User model
│   │       ├── Software.php             # Software model
│   │       ├── Bundle.php               # Bundle model (NEW)
│   │       └── BundleItem.php           # Bundle items model (NEW)
│   ├── 📂 database/
│   │   ├── 📂 migrations/               # Database schema
│   │   └── 📂 seeders/                  # Sample data
│   ├── 📂 storage/app/public/downloads/ # Installer files
│   ├── 📂 routes/api.php               # API routes
│   └── 📂 config/cors.php              # CORS configuration
│
├── 📂 frontend/                         # React Frontend
│   ├── 📂 src/
│   │   ├── 📂 components/
│   │   │   ├── Dashboard.js             # Main dashboard with bundles
│   │   │   ├── Login.js                 # Login form
│   │   │   ├── Register.js              # Registration form
│   │   │   └── ProtectedRoute.js        # Route protection
│   │   ├── 📂 services/
│   │   │   └── api.js                   # API client with download helpers
│   │   └── App.js                       # Main app component
│   ├── 📂 public/                       # Static assets
│   └── .env                             # Environment variables
│
├── 🔧 setup-backend.bat                 # Backend setup automation
├── 🔧 setup-frontend.bat                # Frontend setup automation
├── 🔧 run-migrations.bat                # Database setup automation
├── 📖 README.md                         # This file
├── 📖 BUNDLES_FEATURE_GUIDE.md         # Bundles feature documentation
└── 📖 IMPLEMENTATION_SUMMARY.md        # Implementation details
```
└── 📖 README.md                         # This file
```

## 🚀 Quick Start

### Prerequisites
- **PHP 8.1+** with extensions: bcmath, ctype, fileinfo, json, mbstring, openssl, pdo, tokenizer, xml
- **Composer** (latest version)
- **Node.js 16+** and npm
- **MySQL 8.0+** or MariaDB
- **Git** (for cloning)

### Installation Steps

1️⃣ **Clone Repository**
```bash
git clone <repository-url>
cd Software_Download_Manager
```

2️⃣ **Setup Backend** (Automated)
```cmd
setup-backend.bat
```
This will:
- Install Composer dependencies
- Copy `.env.example` to `.env`
- Generate application key
- Generate JWT secret

3️⃣ **Configure Database**

Edit `backend/.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=software_download_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

4️⃣ **Setup Database** (Automated)
```cmd
run-migrations.bat
```
This will:
- Run all migrations (users, software, bundles tables)
- Seed sample software data
- Create storage symlink

5️⃣ **Setup Frontend** (Automated)
```cmd
setup-frontend.bat
```
This will:
- Install npm dependencies
- Check environment configuration

6️⃣ **Start Servers**
```cmd
# Terminal 1: Backend
cd backend
php artisan serve

# Terminal 2: Frontend  
cd frontend
npm start
```

7️⃣ **Access Application**
- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8000/api

### First Time Usage
1. Register a new account at http://localhost:3000
2. Login with your credentials
3. Browse the software catalog
4. Try creating a bundle with multiple software selections

## 🔧 Manual Setup

### Backend Setup
```bash
cd backend

# Install dependencies
composer install

# Environment setup
cp .env.example .env
# Edit .env file with your database credentials

# Generate keys
php artisan key:generate
php artisan jwt:secret

# Database setup
php artisan migrate
php artisan db:seed

# Create storage symlink
php artisan storage:link

# Start server
php artisan serve
```

### Frontend Setup
```bash
cd frontend

# Install dependencies
npm install

# Configure environment (if needed)
# Edit .env file to set API URL

# Start development server
npm start
```

### Environment Configuration

**Backend** (`backend/.env`):
```env
APP_NAME="Software Download Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=software_download_manager
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=(auto-generated)
JWT_TTL=60
```

**Frontend** (`frontend/.env`):
```env
REACT_APP_API_BASE_URL=http://localhost:8000/api
REACT_APP_NAME="Software Download Manager"
```

**Note:** React requires restart after `.env` changes!
php artisan migrate
php artisan db:seed

# Create storage symlink
php artisan storage:link

# Start server
php artisan serve
```

## 📚 API Documentation

### Authentication Endpoints

**Register**
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "user@example.com", 
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Login**
```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}

Response:
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": { "id": 1, "name": "John Doe", "email": "user@example.com" }
}
```

**Logout**
```http
POST /api/logout
Authorization: Bearer {token}
```

### Software Endpoints

**List All Software**
```http
GET /api/software
Authorization: Bearer {token}
```

**Get Single Software**
```http
GET /api/software/{id}
Authorization: Bearer {token}
```

### Bundle Endpoints (NEW)

**List User Bundles**
```http
GET /api/bundles
Authorization: Bearer {token}
```

**Create Bundle**
```http
POST /api/bundles
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Web Development Setup",
  "description": "Essential tools for web dev",
  "software_ids": [1, 3, 5, 8]
}
```

**Get Bundle Details**
```http
GET /api/bundles/{id}
Authorization: Bearer {token}
```

**Update Bundle**
```http
PUT /api/bundles/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Bundle Name",
  "software_ids": [1, 2, 3]
}
```

**Delete Bundle**
```http
DELETE /api/bundles/{id}
Authorization: Bearer {token}
```

### Download Endpoints

**Download Single File**
```http
GET /api/download-file/{id}?token={jwt_token}
```

**Download Multiple Files (ZIP)**
```http
POST /api/download-multiple
Authorization: Bearer {token}
Content-Type: application/json

{
  "software_ids": [1, 2, 3]
}

Response: Streams "SDM.zip" file
```

**Download Bundle as ZIP**
```http
GET /api/bundles/{id}/download
Authorization: Bearer {token}

Response: Streams "SDM-{bundle-name}.zip" file
```

**Export Bundle PowerShell Script**
```http
GET /api/bundles/{id}/export-script
Authorization: Bearer {token}

Response: Downloads .ps1 installation script
```

### Response Formats

**Success Response**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

**Error Response**
```json
{
  "success": false,
  "message": "Error description",
  "errors": { ... }
}
```

**Validation Error (422)**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

## 👥 Usage Guide

### Getting Started

**1. Registration**
- Visit the application homepage
- Click "Register" 
- Fill in your name, email, and password
- Submit to create your account

**2. Login**
- Enter your email and password
- Click "Login"
- You'll be redirected to the dashboard

### Using the Dashboard

**Browse Software**
- View all available software in card format
- Each card shows:
  - Software name and description
  - Category badge
  - File size
  - Download button

**Search & Filter**
- **Search Bar:** Type to find software by name or description
- **Category Filter:** Select category (All, Browser, Development, Utilities, Media, Gaming, Document, Productivity, Communication)

**Download Software**

*Single Download:*
1. Click the "Download" button on any software card
2. File downloads immediately with clean filename

*Multiple Downloads:*
1. Check the boxes on multiple software cards
2. Click "Download Selected (X files)" button at top
3. Receive "SDM.zip" containing all selected installers
4. Files inside ZIP have clean names (no server paths)

### Working with Bundles

**Create a Bundle**
1. Select software using checkboxes
2. Enter bundle name (e.g., "Web Development Setup")
3. Optionally add description
4. Click "Save Bundle"
5. Toast notification confirms creation

**Manage Bundles**
- View all your saved bundles in the "My Bundles" section
- Each bundle shows:
  - Bundle name and description
  - Number of software items
  - Action buttons (Apply, Download, Export, Delete)

**Apply a Bundle**
1. Click "Apply" button on any bundle
2. Dashboard automatically selects all software in that bundle
3. You can then add/remove selections before downloading

**Download Bundle**
1. Click "Download Bundle" button
2. Receive ZIP file named "SDM-{bundle-name}.zip"
3. Contains all software from the bundle

**Export PowerShell Script**
1. Click "Export Script" button
2. Downloads .ps1 file
3. Run script to automatically download and install all bundle software

**Delete Bundle**
1. Click "Delete" button (trash icon)
2. Confirm deletion
3. Bundle is permanently removed

### Tips & Best Practices

- **Save Common Setups:** Create bundles for frequent use cases (e.g., "New PC Setup", "Gaming Setup")
- **Use Search Efficiently:** Type partial names to quickly find software
- **Check File Sizes:** Consider bandwidth before downloading large bundles
- **Keep Bundles Updated:** Edit bundles when your needs change

## 🐛 Troubleshooting

### Common Issues

**"Backend connection failed"**
- ✅ Ensure backend server is running (`php artisan serve`)
- ✅ Check database connection in `.env`
- ✅ Verify MySQL/MariaDB service is running
- ✅ Test API endpoint: `http://localhost:8000/api/software`

**"JWT Secret not found"** 
- ✅ Run `php artisan jwt:secret`
- ✅ Check `.env` has `JWT_SECRET=...` entry
- ✅ Clear config cache: `php artisan config:clear`
- ✅ Restart backend server

**"CORS errors in browser"**
- ✅ Verify `config/cors.php` allows your frontend URL
- ✅ Check `Access-Control-Allow-Origin` header in browser DevTools
- ✅ Clear browser cache and cookies
- ✅ Restart both backend and frontend servers

**"Files not downloading"**
- ✅ Run `php artisan storage:link` to create symlink
- ✅ Check file permissions on `storage/` directory
- ✅ Verify files exist in `storage/app/public/downloads/`
- ✅ Check browser console for download errors

**"Frontend won't start"**
- ✅ Delete `node_modules` and run `npm install`
- ✅ Check Node.js version: `node --version` (16+ required)
- ✅ Clear React cache: `npm start -- --reset-cache`
- ✅ Check port 3000 is not already in use

**"Frontend can't connect after using Cloudflare Tunnel"**
- ✅ Update `frontend/.env` with tunnel URL: `REACT_APP_API_BASE_URL=https://your-tunnel-url.trycloudflare.com/api`
- ✅ **IMPORTANT:** Restart React dev server after changing `.env` (React only reads environment variables at startup)
- ✅ Verify tunnel is still running (Cloudflare free tunnels expire)

**"ZIP downloads show full server paths"**
- ✅ This has been fixed - files should show clean names only
- ✅ If still occurring, clear Laravel cache: `php artisan config:clear`
- ✅ Verify you're using latest DownloadController code

**"Category filter doesn't work"**
- ✅ Database category names must match filter dropdown exactly
- ✅ Check for spelling: "Utilities" (plural) not "Utility" (singular)
- ✅ Category names are case-insensitive

### Debug Mode

Enable detailed logging in `backend/.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

View logs at: `backend/storage/logs/laravel.log`

Clear all caches:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Database Issues

**Reset database:**
```bash
php artisan migrate:fresh --seed
```
⚠️ Warning: This deletes all data!

**Check database connection:**
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

## 🌐 Deployment

### Production Checklist

**Backend:**
1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Run `composer install --optimize-autoloader --no-dev`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Set proper file permissions (755 for directories, 644 for files)
7. Ensure `storage/` and `bootstrap/cache/` are writable

**Frontend:**
1. Update `REACT_APP_API_BASE_URL` in `.env` to production API URL
2. Run `npm run build`
3. Serve the `build/` folder via web server (Nginx, Apache, etc.)
4. Configure HTTPS with SSL certificate

**Security:**
- Use strong database passwords
- Keep JWT secrets secure and unique
- Enable HTTPS for production
- Set proper CORS allowed origins
- Regularly update dependencies
- Monitor logs for suspicious activity

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 📚 Additional Documentation

- **[Bundles Feature Guide](BUNDLES_FEATURE_GUIDE.md)** - Detailed guide for the bundles/setup profiles feature
- **[Implementation Summary](IMPLEMENTATION_SUMMARY.md)** - Technical implementation details
- **[Download Optimizations](DOWNLOAD_OPTIMIZATIONS.md)** - Information about ZIP streaming performance

## 👨‍💻 Credits

NPRU Work Project

---

**Built with ❤️ using Laravel, React, and Tailwind CSS**

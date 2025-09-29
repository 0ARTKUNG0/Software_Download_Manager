# 🚀 Software Download Manager

A modern, full-stack web application for managing and distributing software installers with secure authentication and bulk download capabilities.

![Tech Stack](https://img.shields.io/badge/Laravel-10-red)
![Tech Stack](https://img.shields.io/badge/React-18-blue)
![Tech Stack](https://img.shields.io/badge/PHP-8.0+-purple)
![Tech Stack](https://img.shields.io/badge/MySQL-8.0+-orange)

## 📋 Table of Contents
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Quick Start](#-quick-start)
- [Manual Setup](#-manual-setup)
- [API Documentation](#-api-documentation)
- [Usage Guide](#-usage-guide)
- [File Storage](#-file-storage)
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

### 📥 Download System
- **Single Downloads** - Click to download individual installers
- **Bulk ZIP Downloads** - Select multiple files, get "sdm.zip" bundle
- **Real File Storage** - Actual installer files stored in backend
- **Progress Indicators** - Visual feedback during downloads
- **Download History** - Track what users download

### 🎨 Modern UI/UX
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Tailwind CSS** - Modern, clean interface
- **Loading States** - Smooth user experience
- **Error Handling** - User-friendly error messages
- **Interactive Cards** - Hover effects and smooth transitions

## 🛠 Tech Stack

### Backend
- **Framework:** Laravel 10 (PHP 8.0+)
- **Database:** MySQL 8.0+
- **Authentication:** tymon/jwt-auth
- **Storage:** Local file system
- **API:** RESTful JSON API

### Frontend
- **Framework:** React 18
- **Routing:** React Router v6
- **Styling:** Tailwind CSS 3
- **HTTP Client:** Axios
- **Build Tool:** Create React App

### Development Tools
- **Package Managers:** Composer, npm
- **Automation:** Windows Batch scripts
- **Version Control:** Git
- **Environment:** XAMPP, Node.js

## 📁 Project Structure

```
Software_Download_Manager/
├── 📂 backend/                          # Laravel API Backend
│   ├── 📂 app/Http/Controllers/         # API Controllers
│   │   ├── AuthController.php           # User authentication
│   │   ├── SoftwareController.php       # Software CRUD
│   │   └── DownloadController.php       # File downloads
│   ├── 📂 database/
│   │   ├── 📂 migrations/               # Database schema
│   │   └── 📂 seeders/                  # Sample data
│   ├── 📂 storage/app/public/downloads/ # Installer files
│   └── 📂 routes/api.php               # API routes
│
├── 📂 frontend/                         # React Frontend
│   ├── 📂 src/
│   │   ├── 📂 components/
│   │   │   ├── Dashboard.js             # Main dashboard
│   │   │   ├── Login.js                 # Login form
│   │   │   ├── Register.js              # Registration form
│   │   │   └── ProtectedRoute.js        # Route protection
│   │   ├── 📂 services/
│   │   │   └── api.js                   # API client
│   │   └── App.js                       # Main app component
│   └── 📂 public/                       # Static assets
│
├── 🔧 setup-backend.bat                 # Backend setup automation
├── 🔧 setup-frontend.bat                # Frontend setup automation
├── 🔧 run-migrations.bat                # Database setup automation
└── 📖 README.md                         # This file
```

## 🚀 Quick Start

### Prerequisites
- **PHP 8.0+** with extensions: bcmath, ctype, fileinfo, json, mbstring, openssl, pdo, tokenizer, xml
- **Composer** (latest version)
- **Node.js 16+** and npm
- **MySQL 8.0+** (XAMPP recommended)
- **Git** (for cloning)

### 1️⃣ Clone Repository
```bash
git clone <your-repository-url>
cd Software_Download_Manager
```

### 2️⃣ Automated Setup (Recommended)
```cmd
# 1. Setup backend
setup-backend.bat

# 2. Setup database
run-migrations.bat

# 3. Setup frontend
setup-frontend.bat
```

### 3️⃣ Start Servers
```cmd
# Terminal 1: Backend
cd backend
php artisan serve

# Terminal 2: Frontend  
cd frontend
npm start
```

### 4️⃣ Access Application
- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8000/api

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

# Start development server
npm start
```

### Database Configuration
Update `backend/.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=software_download_manager
DB_USERNAME=root
DB_PASSWORD=your_password

JWT_SECRET=your_generated_secret
```

## 📚 API Documentation

### Authentication Endpoints
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com", 
  "password": "password123",
  "password_confirmation": "password123"
}
```

```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

### Protected Endpoints (Require JWT Token)
```http
GET /api/software
Authorization: Bearer {your_jwt_token}
```

```http
GET /api/download-file/{id}?token={your_jwt_token}
```

```http
POST /api/download-multiple
Authorization: Bearer {your_jwt_token}
Content-Type: application/json

{
  "software_ids": [1, 2, 3]
}
```

### Response Formats
```json
// Success Response
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}

// Error Response  
{
  "success": false,
  "message": "Error description",
  "errors": { ... }
}
```

## 👥 Usage Guide

### For End Users

1. **Registration**
   - Visit http://localhost:3000
   - Click "Register" and fill in your details
   - Verify your account is created

2. **Login**
   - Enter your email and password
   - You'll be redirected to the dashboard

3. **Browse Software**
   - View all available software in card format
   - Use search to find specific software
   - Filter by categories (Browsers, Development, etc.)

4. **Download Software**
   - **Single Download:** Click "Download" on any software card
   - **Bulk Download:** Select multiple checkboxes, click "Download Selected"
   - **ZIP Downloads:** Multiple selections create "sdm.zip" bundle

### For Administrators

1. **Add New Software**
   - Place installer files in `backend/storage/app/public/downloads/`
   - Update database with software metadata
   - Files become available immediately

2. **Manage Users**
   - View user activity in database
   - Monitor download patterns
   - Manage access permissions

## 💾 File Storage

### Current Software Catalog
The system includes these pre-configured installers:

**🌐 Browsers**
- Google Chrome (`chrome_installer.exe`)
- Mozilla Firefox (`firefox_installer.exe`)

**⚒️ Development**  
- Visual Studio Code (`vscode_installer.exe`)
- Git (`git_installer.exe`)
- Notepad++ (`notepadpp_installer.exe`)

**🎵 Media**
- Spotify (`spotify_installer.exe`)
- VLC Media Player (`vlc_installer.exe`)
- OBS Studio (`obs_installer.exe`)

**🎮 Gaming**
- Discord (`discord_installer.exe`)
- Steam (`steam_installer.exe`)
- Epic Games Launcher (`epicgames_installer.msi`)
- Minecraft (`minecraft_installer.msi`)

**🔧 Utilities**
- 7-Zip (`7zip_installer.exe`)
- Java Runtime (`java_installer.exe`)
- WhatsApp Desktop (`whatsapp_installer.exe`)
- Zoom (`zoom_installer.exe`)

### Adding New Software
1. Place installer file in `backend/storage/app/public/downloads/`
2. Add database entry with metadata:
   ```sql
   INSERT INTO software (name, description, size, category, file_name, link) 
   VALUES ('Software Name', 'Description', file_size_in_bytes, 'Category', 'filename.exe', 'https://website.com');
   ```

## 🐛 Troubleshooting

### Common Issues

**"Backend connection failed"**
- ✅ Ensure backend server is running (`php artisan serve`)
- ✅ Check database connection in `.env`
- ✅ Verify API endpoints are accessible

**"JWT Secret not found"** 
- ✅ Run `php artisan jwt:secret`
- ✅ Check `.env` has `JWT_SECRET=...`
- ✅ Restart backend server

**"CORS errors in browser"**
- ✅ Verify `config/cors.php` allows `http://localhost:3000`
- ✅ Clear browser cache
- ✅ Check network tab for specific error

**"Files not downloading"**
- ✅ Run `php artisan storage:link` 
- ✅ Check file permissions on `storage/` directory
- ✅ Verify files exist in `storage/app/public/downloads/`

**"Frontend won't start"**
- ✅ Run `npm install` to update dependencies
- ✅ Check Node.js version (16+ required)
- ✅ Try `npm start -- --reset-cache`

### Debug Mode
Enable detailed logging in `backend/.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

View logs: `backend/storage/logs/laravel.log`

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**จารนรพร 3_68 เทอม 1**  
NPRU Work Project  

---

**Built with ❤️ using Laravel, React, and lots of ☕**

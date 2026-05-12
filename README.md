# CI4 Recruitment Test

Recruitment test project built using:

- CodeIgniter 4
- MySQL
- Vite
- Bootstrap 5
- DataTables v2
- jQuery

---

# Features

- Authentication
- Dashboard statistics
- User management
- Employee management
- AJAX CRUD
- Soft delete
- Restore & permanent delete
- Employee photo upload
- Server-side DataTables

---

# Requirements

- PHP 8.1+
- Composer
- Node.js
- MySQL

---

# Installation

## Clone Repository

```bash
git clone https://github.com/your-username/ci4_recruitment.git
```

```bash
cd ci4_recruitment
```

---

## Install Dependencies

### Composer

```bash
composer install
```

### NPM

```bash
npm install
```

---

# Environment Setup

Rename:

```txt
env
```

to:

```txt
.env
```

Then update the configuration:

```env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = ci4_recruitment
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

encryption.key = 

session.driver = CodeIgniter\Session\Handlers\FileHandler
sessionCookieName = ci_session
sessionExpiration = 7200
```

---

# Generate Application Key

```bash
php spark key:generate
```

---

# Run Migration

```bash
php spark migrate
```

---

# Run Seeder

```bash
php spark db:seed UserSeeder
```

---

# Build Assets

```bash
npm run build
```

---

# Run Project

```bash
php spark serve
```

Application URL:

```txt
http://localhost:8080
```

---

# Default Login

```txt
Email    : admin@mail.com
Password : password
```

---

# Upload Directory

Make sure this directory exists:

```txt
public/uploads/employees
```

---

# Notes

- Soft delete implemented for users and employees
- Employee image upload supports JPG/JPEG only
- Maximum upload size: 300KB
- AJAX CRUD using DataTables modal workflow

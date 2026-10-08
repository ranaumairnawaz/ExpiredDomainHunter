# 🎯 ExpiredDomainHunter (AI Domain Finder)

An advanced **AI-powered Expired Domain Hunting & Discovery Engine** built with **Laravel 11, Livewire, and TailwindCSS**. Automatically discovers, monitors, and analyzes high-value expired, dropped, and auction domains with SEO metric tracking, keyword filtering, and automated background sync.

---

## 🚀 Key Features

- **⚡ Real-Time Domain Explorer (Livewire):** Search, filter, and paginate through thousands of expired domains with instantaneous live updates.
- **🧠 AI Valuation & Domain Scoring:** Evaluate domain names for brandability, commercial potential, and keyword strength.
- **📥 Bulk Domain Importer:** Import domain lists via CSV/text feeds with automated parsing and DNS validation.
- **⏱️ Automated Cron Sync:** Built-in web cron and queue runner (`/cron/update-domains`, `/cron/queue-work`) designed to work smoothly on both shared hosting (cPanel) and modern cloud VPS (Ubuntu/Nginx).
- **🎨 Modern Dark/Light Web Interface:** Built with TailwindCSS, dynamic sidebar navigation, and interactive SweetAlert2 alerts.
- **🔒 Authentication & Access Control:** Complete user management, profile security, and session management.

---

## 🛠️ Tech Stack

- **Framework:** Laravel 11.x
- **Frontend / Reactivity:** Laravel Livewire 3.x, Alpine.js, TailwindCSS
- **Database:** MySQL 8.0+ / MariaDB 10.4+
- **Background Jobs:** Laravel Queue System & Database Job Engine
- **Tooling:** Vite, Composer, SweetAlert2

---

## 📦 Getting Started

### 1. Prerequisites
- PHP 8.2 or 8.3+ with extensions: `pdo_mysql`, `curl`, `mbstring`, `openssl`, `xml`, `fileinfo`
- Composer 2.x
- Node.js 18+ & NPM
- MySQL or MariaDB

### 2. Clone & Install
```bash
git clone https://github.com/ranaumairnawaz/ExpiredDomainHunter.git
cd ExpiredDomainHunter

composer install
npm install && npm run build
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database connection in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expired_domain_hunter
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migrations & Seed
```bash
php artisan migrate
```

### 5. Launch Local Server
```bash
php artisan serve
```

---

## 🌐 Background Sync & Automation

Automated domain scrapers and parsers can be scheduled via CLI or triggered through web endpoints for shared hosting environments:
- `GET /cron/update-domains` — Pull latest expiring/dropped domain batches.
- `GET /cron/queue-work` — Process domain metric analysis jobs in the background.

---

## 👨‍💻 Author
**Rana Umair Nawaz**  
- Founder, [ZedX Solutions](https://github.com/ranaumairnawaz)  
- [LinkedIn Profile](https://linkedin.com/in/ranaumairnawaz)  
- [Personal Portfolio](https://umair.zedx-solutions.com)

# 🎯 ExpiredDomainHunter — AI-Powered Expired Domain Discovery & Valuation Engine

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire" alt="Livewire 3">
  <img src="https://img.shields.io/badge/TailwindCSS-v3-06B6D4?style=for-the-badge&logo=tailwindcss" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/AI%20Valuation-Enabled-8A2BE2?style=for-the-badge" alt="AI Appraisal">
</p>

---

## 📖 What is ExpiredDomainHunter?

**ExpiredDomainHunter** is an automated **domain research, monitoring, and valuation engine** built with **Laravel 11, Livewire 3, and Tailwind CSS**. 

It systematically crawls, ingests, and analyzes thousands of expiring, dropped, and auction domains across major registries (GoDaddy, Pool, WhoisFreaks). It enriches them with key SEO indicators (Moz metrics, backlinks, domain age, character length) and utilizes algorithmic and AI scoring to surface high-value digital real estate for investors and SEO professionals.

---

## 🎯 Why ExpiredDomainHunter? Problem & Solution

| The Challenge | How ExpiredDomainHunter Solves It |
| :--- | :--- |
| **Information Overload in Dropped Lists:** Registries drop tens of thousands of domains daily; manually filtering spam and low-quality domains takes hours. | Implements **Automated Quality Filtering & Scoring**, evaluating keyword quality, clean history, TLD value, and backlink authority automatically. |
| **Sluggish Search & Sorting:** Sifting through 100,000+ domain records on traditional web tables causes browser freezes. | Leverages **Laravel Livewire 3**, providing reactive, instant client-side search, filtering, and pagination with zero full-page reloads. |
| **Complex Server Requirements:** Scraping and metric calculations often require complex background workers that don't run on shared hosting. | Built with **Shared-Hosting-Friendly Web Cron Endpoints** (`/cron/update-domains`, `/cron/queue-work`) that can be triggered via standard cPanel cron or curl. |

---

## 🌟 Key Features

### ⚡ Real-Time Domain Explorer (Livewire)
- **Instant Search & Multi-Filter:** Filter by TLD (`.com`, `.net`, `.org`, country-code), character length, hyphens, numbers, or specific dictionary keywords.
- **SEO Metric Inspection:** Real-time visibility into Domain Authority (DA), Page Authority (PA), and historical backlink profiles.
- **Saved Searches & Watchlists:** Bookmark promising domains and configure alerts when matching high-score names drop.

### 📥 Bulk Ingestion & Parser Suite
- **Multi-Source Ingestion Pipeline:** Modular data source adapters for GoDaddy Auctions, Pool, WhoisFreaks, and raw CSV/text dumps.
- **Automated DNS & WHOIS Verification:** Checks whether domains are already registered, in redemption period, or immediately available for hand-registration.

### ⚙️ Automation & Shared Hosting Crons
- **Scheduled Synchronization:** Automatic background crons pull dropping domain feeds and calculate valuation scores without user interaction.
- **Queue Workers:** Processes intensive Moz metric and AI appraisal tasks in chunked background queues.

---

## 🛠️ Technical Stack

- **Backend Framework:** Laravel 11.x (PHP 8.2+)
- **Reactivity & Frontend:** Laravel Livewire 3.x, Alpine.js, Tailwind CSS
- **Database:** MySQL 8.0+ / MariaDB
- **Integrations:** Moz API, WhoisFreaks API, GoDaddy RSS, SweetAlert2
- **Build Tools:** Vite, PostCSS, Composer

---

## 📦 Local Installation & Setup

### 1. Prerequisites
- PHP 8.2 or 8.3+ with `pdo_mysql`, `curl`, `mbstring`, `fileinfo` extensions
- Composer 2.x
- Node.js 18+ & NPM
- MySQL 8.0+

### 2. Clone & Setup
```bash
git clone https://github.com/ranaumairnawaz/ExpiredDomainHunter.git
cd ExpiredDomainHunter

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

### 3. Database Migration
```bash
php artisan migrate --seed
php artisan serve
```

### 4. Background Sync Configuration
To schedule continuous domain updates:
```bash
# Add to server crontab
*/30 * * * * curl -s https://yourdomain.com/cron/update-domains >/dev/null 2>&1
* * * * * curl -s https://yourdomain.com/cron/queue-work >/dev/null 2>&1
```

---

## 👨‍💻 Author
**Rana Umair Nawaz**  
- [GitHub Profile](https://github.com/ranaumairnawaz)  
- [LinkedIn Profile](https://linkedin.com/in/ranaumairnawaz)

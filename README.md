# WaliKelas Pro

<p align="center">
  <img src="public/icon.svg" alt="WaliKelas Pro" width="120">
</p>

<p align="center">
  <strong>Aplikasi SaaS multi-tenant untuk administrasi wali kelas di Indonesia</strong>
</p>

<p align="center">
  <a href="https://github.com/walaskelas/pro/actions"><img src="https://github.com/walaskelas/pro/workflows/Tests/badge.svg" alt="Tests"></a>
  <a href="https://packagist.org/packages/walaskelas/pro"><img src="https://img.shields.io/packagist/v/walaskelas/pro.svg" alt="Latest Version"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License"></a>
</p>

---

## 🎯 Fitur Utama

### Multi-Tenancy
- Setiap wali kelas hanya bisa melihat data miliknya sendiri
- Isolasi data berdasarkan `user_id`
- Authorization policies untuk setiap resource

### Sistem Absensi Anti-Curang
- **Magic Link** dengan PIN harian
- Link otomatis kedaluwarsa
- Notifikasi WhatsApp otomatis via n8n

### Manajemen Kelas
| Modul | Deskripsi |
|-------|-----------|
| Siswa | CRUD profil, import/export |
| Struktur Organisasi | Ketua kelas, seksi, dll |
| Jadwal Pelajaran | Manajemen jadwal mingguan |
| Poin Kedisiplinan | Pelanggaran & poin otomatis |
| Buku Kas | Income/expense tracker |
| Denah Tempat Duduk | Layout visual |
| Journal BK | Catatan konseling |

### Integrasi
- REST API untuk CBT/ExamBrowser
- WhatsApp Gateway via n8n/Fonnte
- Webhook support
- Scheduled automation

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer 2+
- Node.js 18+
- PostgreSQL 15+ atau MySQL 8+

### Installation

```bash
# Clone repository
git clone https://github.com/walaskelas/pro.git
cd walas-pro

# Install dependencies
composer install
npm install

# Copy environment
cp .env.example .env

# Generate keys
php artisan key:generate

# Configure database in .env
php artisan migrate --seed

# Start dev server
php artisan serve
```

### Docker Development

```bash
# Start all services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --seed
```

### Demo Credentials

```
Email: budi@sekolah.sch.id
Password: password
```

---

## 📁 Project Structure

```
app/
├── Console/Commands/        # CLI commands (wa:process, scheduler:run, dll)
├── Http/
│   ├── Controllers/
│   │   └── Api/v1/        # REST API controllers
│   ├── Middleware/         # tenant, subscription, api.token
│   └── Requests/           # Form validation
├── Jobs/                    # Queue jobs (SendWhatsApp, etc)
├── Models/                  # 20+ Eloquent models
├── Policies/                # 12 authorization policies
├── Providers/               # Service providers
├── Scopes/                  # TenantScope for multi-tenancy
├── Services/                 # Business logic services
│   ├── AttendanceService.php
│   ├── ReportService.php
│   ├── StudentService.php
│   ├── WhatsAppService.php
│   └── ExportService.php
└── Helpers/                 # Helper classes
    ├── DateHelper.php
    ├── StringHelper.php
    └── AttendanceHelper.php

config/
├── walas.php               # App configuration
└── l5-swagger.php          # API docs

database/
├── factories/              # 6 model factories
├── migrations/             # Database schema
└── seeders/               # Sample data

docs/                       # Documentation
n8n/                        # n8n workflows (7 workflows)
resources/views/             # Blade templates (25+ views)
routes/                      # Route definitions
scripts/                     # Deployment scripts
tests/                      # Unit & Feature tests (60+ tests)
```

---

## 🔐 Authentication

### API Tokens (Pro users)

Generate tokens di dashboard atau CLI:

```bash
php artisan token:create "CBT Integration" \
    --abilities=read,attendance,exam_monitor
```

Use in requests:

```bash
curl -H "Authorization: Bearer your_token_here" \
     https://api.walaskelas.pro/v1/students/class/1
```

---

## 📱 WhatsApp Integration

### n8n Setup

1. Start n8n: `cd n8n && docker-compose up -d`
2. Import workflows dari `n8n/workflows/`
3. Activate workflows

### Available Workflows

| # | Workflow | Description |
|---|----------|-------------|
| 01 | WhatsApp Sender | Send message via Fonnte |
| 02 | Attendance Magic Link | Create session & send link |
| 03 | Bulk Attendance Notify | Parent notifications |
| 04 | Violation Warning | Pelanggaran alerts |
| 05 | Scheduled Attendance | Auto-generate daily links |
| 06 | WhatsApp Incoming | Handle commands |
| 07 | Exam Monitor | CBT integration |

---

## 🧪 Testing

```bash
# All tests
php artisan test

# With coverage
php artisan test --coverage

# Specific file
php artisan test tests/Feature/ClassControllerTest.php
```

---

## 📦 Deployment

### Manual Deployment

```bash
ssh user@your-server.com
cd /var/www/walas-pro
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache route:cache view:cache
php artisan queue:restart
```

### Docker Production

```bash
docker-compose -f docker-compose.prod.yml up -d --build
```

### Useful Commands

```bash
# Process WhatsApp queue
php artisan wa:process --limit=50

# Clean old sessions
php artisan sessions:clean --days=90

# Generate daily attendance
php artisan scheduler:run --daily

# Health check
./scripts/healthcheck.sh
```

---

## 📚 API Endpoints

### Authentication
```
POST /api/v1/auth/login
POST /api/v1/auth/logout
```

### Attendance
```
GET    /api/v1/attendance
POST   /api/v1/attendance
GET    /api/v1/attendance/summary
```

### Students
```
GET    /api/v1/students/class/{id}
GET    /api/v1/students/{id}
GET    /api/v1/students/{id}/photo
```

### Exam Monitoring
```
POST   /api/v1/exam/start
POST   /api/v1/exam/log
POST   /api/v1/exam/end
GET    /api/v1/exam/status/{id}
```

### Reports
```
GET    /api/v1/reports/student/{id}/attendance
GET    /api/v1/reports/class/{id}/attendance
GET    /api/v1/reports/student/{id}/violations
```

Lihat [docs/API.md](docs/API.md) untuk dokumentasi lengkap.

---

## 🔧 Configuration

### Environment Variables

```env
# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_DATABASE=walas_pro

# Queue
QUEUE_CONNECTION=redis

# WhatsApp
N8N_WEBHOOK_URL=https://n8n.your-domain.com/webhook
N8N_SECRET_TOKEN=your-secret
FONNTE_API_KEY=your-fonnte-key

# App
ATTENDANCE_EXPIRES_AT=15:00
```

---

## 📄 License

MIT License - lihat [LICENSE](LICENSE)

---

## 🙏 Credits

- [Laravel](https://laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- [n8n](https://n8n.io)

---

<p align="center">
  Built with ❤️ for Indonesian educators
</p>

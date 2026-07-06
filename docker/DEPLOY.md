# Docker Deployment Walas Pro

## Quick Start

```bash
# Copy environment file
cp .env.docker .env

# Start containers
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --seed

# View logs
docker-compose logs -f
```

## Services

| Container | Port | Description |
|-----------|------|-------------|
| app | 9000 | Laravel PHP-FPM |
| nginx | 80, 443 | Reverse Proxy |
| mysql | 3306 | MySQL Database |
| redis | 6379 | Cache & Queue |
| n8n | 5678 | Workflow Automation |
| certbot | - | SSL Certificates |

## Access

| Service | URL | Credentials |
|---------|-----|------------|
| App | https://walas.my.id | admin@walas.my.id / admin123 |
| N8N | https://walas.my.id/n8n/ | admin / n8n_secure_password |
| PhpMyAdmin | http://localhost:8080 | root / rootpass123 |
| Portainer | http://localhost:9000 | - | - |

## Commands

```bash
# Build & Start
docker-compose up -d --build

# Restart services
docker-compose restart app

# View logs
docker-compose logs -f app

# SSH into container
docker exec -it walas-pro-app bash

# Run migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed

# Backup database
docker-compose exec db sh -c "mysqldump -u root -p$MYSQL_ROOT_PASSWORD walas_pro > /dump.sql"
```

## Environment Variables

```bash
# .env.docker
APP_ENV=production
DB_HOST=db
DB_DATABASE=walas_pro
REDIS_HOST=redis
N8N_WEBHOOK_URL=https://n8n.walas.my.id
```

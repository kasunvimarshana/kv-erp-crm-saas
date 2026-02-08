# Deployment Guide

## Overview

This guide covers deploying KV-ERP-CRM-SaaS across different environments, from local development to production Kubernetes clusters.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Environment Configuration](#environment-configuration)
3. [Local Development](#local-development)
4. [Docker Deployment](#docker-deployment)
5. [Production Deployment](#production-deployment)
6. [Kubernetes Deployment](#kubernetes-deployment)
7. [Database Management](#database-management)
8. [Monitoring & Maintenance](#monitoring--maintenance)
9. [Troubleshooting](#troubleshooting)

## Prerequisites

### System Requirements

**Minimum Requirements:**
- CPU: 2 cores
- RAM: 4GB
- Storage: 20GB
- PHP 8.2+
- PostgreSQL 15+ / MySQL 8+
- Redis 7+
- Node.js 18 LTS+

**Recommended for Production:**
- CPU: 4+ cores
- RAM: 8GB+
- Storage: 100GB+ SSD
- Load balancer
- Database replication
- Redis cluster

### Software Dependencies

```bash
# PHP 8.2+
php -v

# Composer 2.x
composer --version

# Node.js 18+
node --version

# PostgreSQL 15+
psql --version

# Redis 7+
redis-cli --version

# Docker (optional)
docker --version
docker-compose --version
```

## Environment Configuration

### Environment Variables

Create `.env` file from `.env.example`:

```bash
cp .env.example .env
```

### Core Configuration

```env
# Application
APP_NAME="KV ERP CRM SaaS"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kv_erp_crm_saas
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Multi-Tenancy
TENANT_IDENTIFICATION=domain  # domain or subdomain
CENTRAL_DOMAIN=your-domain.com

# File Storage
FILESYSTEM_DISK=s3  # local for dev, s3 for prod
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket

# Search
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=your_master_key

# Monitoring
SENTRY_LARAVEL_DSN=https://your-sentry-dsn@sentry.io/project-id
```

### Security Configuration

```env
# Generate new application key
php artisan key:generate

# JWT secret (if using JWT)
JWT_SECRET=your_jwt_secret_here

# Encryption
# Use strong passwords for production
# Enable two-factor authentication
# Configure HTTPS/SSL
```

## Local Development

### Using Laravel Sail (Recommended)

```bash
# Install Sail
composer require laravel/sail --dev

# Publish Sail configuration
php artisan sail:install

# Start services
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

# Seed database
./vendor/bin/sail artisan db:seed

# Access application
open http://localhost
```

### Traditional Setup

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Build assets
npm run dev

# Start servers
php artisan serve  # Terminal 1
php artisan queue:work  # Terminal 2
npm run dev  # Terminal 3 (optional, for HMR)
```

## Docker Deployment

### Docker Compose (Development/Staging)

Create `docker-compose.yml`:

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
    container_name: kv-erp-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - kv-erp-network
    depends_on:
      - db
      - redis

  nginx:
    image: nginx:alpine
    container_name: kv-erp-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
      - ./docker/nginx/ssl:/etc/nginx/ssl
    networks:
      - kv-erp-network
    depends_on:
      - app

  db:
    image: postgres:15-alpine
    container_name: kv-erp-db
    restart: unless-stopped
    environment:
      POSTGRES_DB: ${DB_DATABASE}
      POSTGRES_USER: ${DB_USERNAME}
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - db-data:/var/lib/postgresql/data
    networks:
      - kv-erp-network
    ports:
      - "5432:5432"

  redis:
    image: redis:7-alpine
    container_name: kv-erp-redis
    restart: unless-stopped
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD}
    volumes:
      - redis-data:/data
    networks:
      - kv-erp-network
    ports:
      - "6379:6379"

  queue:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
    container_name: kv-erp-queue
    restart: unless-stopped
    working_dir: /var/www
    command: php artisan queue:work --tries=3 --timeout=300
    volumes:
      - ./:/var/www
    networks:
      - kv-erp-network
    depends_on:
      - app
      - redis

networks:
  kv-erp-network:
    driver: bridge

volumes:
  db-data:
  redis-data:
```

### Docker Commands

```bash
# Build and start services
docker-compose up -d --build

# View logs
docker-compose logs -f

# Run migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed

# Access container shell
docker-compose exec app bash

# Stop services
docker-compose down

# Stop and remove volumes
docker-compose down -v
```

## Production Deployment

### Pre-Deployment Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Configure database with strong passwords
- [ ] Setup Redis with authentication
- [ ] Configure mail settings
- [ ] Setup file storage (S3)
- [ ] Configure SSL/TLS certificates
- [ ] Setup monitoring (Sentry, New Relic)
- [ ] Configure backups
- [ ] Setup firewall rules
- [ ] Review security headers
- [ ] Configure CORS settings

### Server Setup (Ubuntu 22.04)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-pgsql php8.2-redis php8.2-mbstring php8.2-xml \
    php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# Install Redis
sudo apt install -y redis-server

# Install Nginx
sudo apt install -y nginx

# Install Certbot for SSL
sudo apt install -y certbot python3-certbot-nginx
```

### Application Deployment

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/kasunvimarshana/kv-erp-crm-saas.git
cd kv-erp-crm-saas

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install --production

# Setup environment
cp .env.example .env
nano .env  # Edit configuration

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Build frontend
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/kv-erp-crm-saas
sudo chmod -R 755 /var/www/kv-erp-crm-saas
sudo chmod -R 775 /var/www/kv-erp-crm-saas/storage
sudo chmod -R 775 /var/www/kv-erp-crm-saas/bootstrap/cache
```

### Nginx Configuration

Create `/etc/nginx/sites-available/kv-erp-crm-saas`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com;
    root /var/www/kv-erp-crm-saas/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    index index.php;
    charset utf-8;

    # Logs
    access_log /var/log/nginx/kv-erp-access.log;
    error_log /var/log/nginx/kv-erp-error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/kv-erp-crm-saas /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### SSL Certificate

```bash
# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal is configured by default
# Test renewal
sudo certbot renew --dry-run
```

### Queue Worker Setup

Create `/etc/systemd/system/kv-erp-queue.service`:

```ini
[Unit]
Description=KV ERP Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/kv-erp-crm-saas
ExecStart=/usr/bin/php /var/www/kv-erp-crm-saas/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Enable and start:

```bash
sudo systemctl enable kv-erp-queue
sudo systemctl start kv-erp-queue
sudo systemctl status kv-erp-queue
```

### Task Scheduler

Add to crontab:

```bash
sudo crontab -e -u www-data
```

Add line:

```
* * * * * cd /var/www/kv-erp-crm-saas && php artisan schedule:run >> /dev/null 2>&1
```

## Kubernetes Deployment

### Prerequisites

- Kubernetes cluster (1.24+)
- kubectl configured
- Helm 3+ (optional)
- Ingress controller (nginx-ingress)
- Cert-manager for SSL

### Kubernetes Resources

Create `k8s/` directory with following files:

**namespace.yaml**:
```yaml
apiVersion: v1
kind: Namespace
metadata:
  name: kv-erp
```

**configmap.yaml**:
```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: kv-erp-config
  namespace: kv-erp
data:
  APP_ENV: production
  APP_DEBUG: "false"
  DB_CONNECTION: pgsql
  CACHE_DRIVER: redis
  QUEUE_CONNECTION: redis
```

**secret.yaml**:
```yaml
apiVersion: v1
kind: Secret
metadata:
  name: kv-erp-secrets
  namespace: kv-erp
type: Opaque
stringData:
  APP_KEY: base64:YOUR_APP_KEY
  DB_PASSWORD: your_db_password
  REDIS_PASSWORD: your_redis_password
```

**deployment.yaml**:
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: kv-erp-app
  namespace: kv-erp
spec:
  replicas: 3
  selector:
    matchLabels:
      app: kv-erp
  template:
    metadata:
      labels:
        app: kv-erp
    spec:
      containers:
      - name: app
        image: your-registry/kv-erp-crm-saas:latest
        ports:
        - containerPort: 9000
        envFrom:
        - configMapRef:
            name: kv-erp-config
        - secretRef:
            name: kv-erp-secrets
        resources:
          requests:
            memory: "512Mi"
            cpu: "500m"
          limits:
            memory: "1Gi"
            cpu: "1000m"
        livenessProbe:
          httpGet:
            path: /health
            port: 8000
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /health
            port: 8000
          initialDelaySeconds: 5
          periodSeconds: 5
```

**service.yaml**:
```yaml
apiVersion: v1
kind: Service
metadata:
  name: kv-erp-service
  namespace: kv-erp
spec:
  selector:
    app: kv-erp
  ports:
  - protocol: TCP
    port: 80
    targetPort: 9000
  type: ClusterIP
```

**ingress.yaml**:
```yaml
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: kv-erp-ingress
  namespace: kv-erp
  annotations:
    cert-manager.io/cluster-issuer: letsencrypt-prod
    nginx.ingress.kubernetes.io/ssl-redirect: "true"
spec:
  ingressClassName: nginx
  tls:
  - hosts:
    - your-domain.com
    secretName: kv-erp-tls
  rules:
  - host: your-domain.com
    http:
      paths:
      - path: /
        pathType: Prefix
        backend:
          service:
            name: kv-erp-service
            port:
              number: 80
```

### Deploy to Kubernetes

```bash
# Apply all resources
kubectl apply -f k8s/

# Check deployment
kubectl get pods -n kv-erp
kubectl get services -n kv-erp
kubectl get ingress -n kv-erp

# View logs
kubectl logs -f deployment/kv-erp-app -n kv-erp

# Run migrations
kubectl exec -it deployment/kv-erp-app -n kv-erp -- php artisan migrate --force

# Scale deployment
kubectl scale deployment/kv-erp-app --replicas=5 -n kv-erp
```

## Database Management

### Backup Strategy

**Automated Daily Backups**:

```bash
#!/bin/bash
# /usr/local/bin/backup-kv-erp-db.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR=/var/backups/kv-erp
DB_NAME=kv_erp_crm_saas

mkdir -p $BACKUP_DIR

# PostgreSQL backup
pg_dump -U postgres $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Upload to S3
aws s3 cp $BACKUP_DIR/db_backup_$DATE.sql.gz s3://your-backup-bucket/database/

# Cleanup old backups (keep 30 days)
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete
```

Add to crontab:

```bash
0 2 * * * /usr/local/bin/backup-kv-erp-db.sh
```

### Restore from Backup

```bash
# Extract backup
gunzip db_backup_20240101_020000.sql.gz

# Restore
psql -U postgres kv_erp_crm_saas < db_backup_20240101_020000.sql
```

## Monitoring & Maintenance

### Health Check Endpoint

Create `/routes/web.php`:

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'services' => [
            'database' => DB::connection()->getPdo() ? 'up' : 'down',
            'redis' => Redis::connection()->ping() ? 'up' : 'down',
        ],
    ]);
});
```

### Log Rotation

Create `/etc/logrotate.d/kv-erp`:

```
/var/www/kv-erp-crm-saas/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    sharedscripts
}
```

### Monitoring with Prometheus

Export Laravel metrics using `spatie/laravel-prometheus` package.

### Performance Optimization

```bash
# Clear all caches
php artisan optimize:clear

# Optimize application
php artisan optimize

# Cache config, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Composer autoloader
composer dump-autoload --optimize
```

## Troubleshooting

### Common Issues

**Permission Issues**:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Queue not processing**:
```bash
# Check queue worker
sudo systemctl status kv-erp-queue
sudo systemctl restart kv-erp-queue

# Check failed jobs
php artisan queue:failed
php artisan queue:retry all
```

**Database connection issues**:
```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check PostgreSQL
sudo systemctl status postgresql
sudo -u postgres psql
```

**Redis connection issues**:
```bash
# Test Redis
redis-cli ping

# Check Redis
sudo systemctl status redis
```

**Clear all caches**:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Debug Mode

**Never enable in production!**

For debugging, temporarily:
```bash
APP_DEBUG=true
php artisan optimize:clear
# Test and gather logs
APP_DEBUG=false
php artisan optimize
```

### Log Analysis

```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View Nginx logs
tail -f /var/log/nginx/kv-erp-error.log

# View system logs
journalctl -u kv-erp-queue -f
```

## Conclusion

This deployment guide covers various deployment scenarios. Choose the approach that best fits your infrastructure and requirements. Always test deployments in staging environment before applying to production.

For additional help:
- Review Laravel deployment documentation
- Check module-specific deployment notes
- Consult infrastructure provider documentation
- Reach out to the development team

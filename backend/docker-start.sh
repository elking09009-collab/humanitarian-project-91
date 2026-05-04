#!/bin/bash
set -e

# Generate app key if not set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Seed database (admin user + demo data)
php artisan db:seed --force

# Cache config for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
apache2-foreground

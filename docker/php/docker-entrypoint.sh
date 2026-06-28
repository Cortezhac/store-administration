#!/bin/sh
set -e

# Create required storage directories in case they don't exist
# (named volume 'storage-data' starts empty on first run)
mkdir -p /var/www/app/storage/framework/views \
         /var/www/app/storage/framework/cache \
         /var/www/app/storage/framework/sessions \
         /var/www/app/storage/logs

# Run the original PHP entrypoint
exec docker-php-entrypoint "$@"

# syntax=docker/dockerfile:1
# =============================================================================
# Kielkraft - multi-stage image (Kirby K5)
#   target "runtime" -> PHP-FPM 8.2 application image
#   target "web"     -> nginx with baked static assets (public/)
# =============================================================================

# ---- Stage 1: Composer dependencies ----------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
# Copy only manifests first for better layer caching
COPY composer.json composer.lock* ./
# Kirby + its composer-installer relocate the CMS to /app/kirby.
# --no-dev: no CLI/dev tooling in the production image.
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --ignore-platform-reqs

# ---- Stage 2: Runtime (PHP-FPM application) ---------------------------------
FROM php:8.2-fpm-alpine AS runtime

# PHP extensions: gd (AVIF/WebP) for the image pipeline, intl, zip, mbstring ...
RUN set -eux; \
    apk add --no-cache \
        libpng libjpeg-turbo libwebp libavif freetype icu-libs libzip oniguruma \
        fcgi; \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS libpng-dev libjpeg-turbo-dev libwebp-dev libavif-dev \
        freetype-dev icu-dev libzip-dev oniguruma-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-avif; \
    docker-php-ext-install -j"$(nproc)" gd intl exif opcache zip mbstring; \
    apk del .build-deps; \
    rm -rf /tmp/*

# PHP + FPM config
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-kielkraft.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-kielkraft.conf

WORKDIR /var/www/html

# Application source + Composer dependencies
COPY --chown=www-data:www-data . /var/www/html
COPY --from=vendor --chown=www-data:www-data /app/vendor /var/www/html/vendor
COPY --from=vendor --chown=www-data:www-data /app/kirby  /var/www/html/kirby

# Writable runtime dirs (overlaid by persistent volumes in production)
RUN mkdir -p \
        storage/cache storage/sessions storage/accounts \
        storage/invoices storage/logs public/media; \
    chown -R www-data:www-data storage public/media

# Healthcheck (php-fpm ping on :9000)
COPY docker/php/fpm-healthcheck.sh /usr/local/bin/fpm-healthcheck
RUN chmod +x /usr/local/bin/fpm-healthcheck

USER www-data
EXPOSE 9000
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD ["fpm-healthcheck"]
CMD ["php-fpm"]

# ---- Stage 3: Web (nginx serving baked static assets) ----------------------
FROM nginx:1.31-alpine AS web
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --chown=nginx:nginx public /var/www/html/public
# content/, site/, kirby/, storage/ are intentionally NOT copied here:
# nginx only needs the public web root. PHP is handled by the "runtime" image.

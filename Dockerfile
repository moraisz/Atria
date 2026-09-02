FROM dunglas/frankenphp:1.12.7-builder-php8.5.6-bookworm AS extension

ARG PHP_VERSION=8.5.6

COPY --from=caddy:2-builder /usr/bin/xcaddy /usr/bin/xcaddy

RUN mkdir /usr/src/php-src && \
    curl -fsSL "https://www.php.net/distributions/php-${PHP_VERSION}.tar.xz" | tar -xJ --strip-components=1 -C /usr/src/php-src

WORKDIR /go/src/app/extensions

COPY --from=atria-core extensions/ ./

RUN GEN_STUB_SCRIPT=/usr/src/php-src/build/gen_stub.php \
    frankenphp extension-init stringext.go

RUN CGO_ENABLED=1 \
    XCADDY_GO_BUILD_FLAGS="-ldflags='-w -s' -tags=nobadger,nomysql,nopgx" \
    CGO_CFLAGS="$(php-config --includes)" \
    CGO_LDFLAGS="$(php-config --ldflags) $(php-config --libs)" \
    xcaddy build \
        --output /usr/local/bin/frankenphp \
        --with github.com/dunglas/frankenphp=/go/src/app \
        --with github.com/dunglas/frankenphp/caddy=/go/src/app/caddy \
        --with github.com/dunglas/caddy-cbrotli \
        --with github.com/dunglas/mercure/caddy \
        --with github.com/dunglas/vulcain/caddy \
        --with github.com/moraisz/atria-core/extensions=/go/src/app/extensions

RUN frankenphp php-cli -r 'if (!function_exists("repeat_this")) { exit(1); } if (repeat_this("abc", 2, false) !== "abcabc") { exit(1); } if (repeat_this("abc", 2, true) !== "cbacba") { exit(1); }'

# Stage 1: PHP Builder
FROM dunglas/frankenphp:1.12.7-builder-php8.5.6-bookworm AS builder

# Install system dependencies for PHP extensions
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libnss3-tools \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN install-php-extensions \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    sockets \
    opcache

# ---------------------------------------------------------------------

# Stage 2: Production Runner
FROM dunglas/frankenphp:1.12.7-php8.5.6-bookworm AS runner

ARG USER=appuser
ARG UID=1000
ARG GID=1000

# Copy the PHP runtime and the FrankenPHP binary built with Atria-Core extensions.
COPY --from=builder /usr/local/bin/frankenphp /usr/local/bin/frankenphp
COPY --from=extension /usr/local/bin/frankenphp /usr/local/bin/frankenphp
RUN setcap cap_net_bind_service=+ep /usr/local/bin/frankenphp

# Install system dependencies and clean up
RUN apt-get update && apt-get install -y --no-install-recommends \
    zip \
    unzip \
    libpq5 \
    libpng16-16 \
    libonig5 \
    libxml2 \
    libzip4 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy PHP extensions and configuration from builder stage
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copy configuration files
COPY docker/php/custom_prod.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/caddy/Caddyfile.prod /etc/caddy/Caddyfile

# Install Composer
COPY --from=composer:2.9.8 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

COPY --from=atria-core / /Atria-Core

# Create system user to run Composer and Artisan Commands
RUN groupadd -g ${GID} ${USER} && \
    useradd -m -u ${UID} -g ${USER} -d /home/${USER} ${USER} && \
    mkdir -p /home/${USER}/.composer && \
    chown -R ${USER}:${USER} /home/${USER} && \
    chown -R ${USER}:${USER} /app /config/caddy /data/caddy

# Copy dependencies files
COPY --chown=${USER}:${USER} composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copy application files
COPY --chown=${USER}:${USER} . .

RUN chmod +x /app/docker/start.sh

USER $USER

# Run shell file
CMD ["/app/docker/start.sh"]

FROM node:22-bookworm-slim AS node
FROM php:8.3-fpm-bookworm

# 必須パッケージとPostgreSQLドライバのインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libsqlite3-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_pgsql pgsql pdo_sqlite mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# Composerのインストール
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Vite 8の要件を満たすNode.js 22とnpmを同じDebian系イメージから導入
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

WORKDIR /var/www

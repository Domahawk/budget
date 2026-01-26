FROM php:8.5-cli

# ---- System deps ----
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    tesseract-ocr \
    tesseract-ocr-hrv \
    tesseract-ocr-eng \
    imagemagick \
    procps \
    && rm -rf /var/lib/apt/lists/*

# ---- Node.js (LTS) ----
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# ---- PHP extensions ----
RUN docker-php-ext-install \
    pdo \
    pdo_mysql

# ---- Create non-root user (UID/GID 1000) ----
RUN groupadd -g 1000 appuser \
    && useradd -u 1000 -g appuser -m appuser

# ---- Composer ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---- App directory ----
RUN mkdir -p /var/www/html \
    && chown -R appuser:appuser /var/www

WORKDIR /var/www/html

USER appuser

CMD ["php", "-a"]

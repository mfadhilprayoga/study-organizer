FROM php:8.4-apache

# Install ekstensi PHP yang dibutuhkan project
RUN docker-php-ext-install mysqli

# Aktifkan mod_rewrite Apache (jaga-jaga untuk kebutuhan routing nanti)
RUN a2enmod rewrite

# Salin semua file project ke folder web server di dalam container
COPY . /var/www/html/

# Set folder kerja
WORKDIR /var/www/html

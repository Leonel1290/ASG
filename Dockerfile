# Usa una imagen base oficial de PHP con Apache
# Puedes intentar cambiar la versión de PHP aquí si la construcción falla
# Por ejemplo: FROM php:8.1-apache o FROM php:8.3-apache
FROM php:8.2-apache

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Instala dependencias del sistema y extensiones de PHP comunes para CodeIgniter
# Las extensiones son necesarias para funcionalidades como base de datos, email, etc.
# Si la construcción falla aquí, puede ser un problema temporal con los repositorios de paquetes
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libxpm-dev \
    libgd-dev \
    libxml2-dev \
    zlib1g-dev \
    libicu-dev \
    libmariadb-dev-compat \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli pdo pdo_mysql zip mbstring exif pcntl bcmath ctype fileinfo json tokenizer xml intl

# Instala Composer
# CORRECCIÓN: Usamos la imagen composer:2 y copiamos desde /usr/bin/composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Copia todos los archivos de tu aplicación al directorio de trabajo
COPY . .

# Instala las dependencias de Composer
# El --no-dev es crucial para no incluir paquetes de desarrollo en producción
# El --optimize-autoloader mejora el rendimiento
RUN composer install --no-dev --optimize-autoloader

# Configura Apache:
# 1. Habilita el módulo rewrite (necesario para URLs amigables de CodeIgniter)
# 2. Sobrescribe la configuración por defecto para apuntar al directorio 'public'
#    como el DocumentRoot y permite la lectura de .htaccess
RUN a2enmod rewrite \
    && cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf.bak \
    && sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/>/a AllowOverride All' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's/<Directory \/var\/www\/html>/<Directory \/var\/www\/html\/public>/' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/html\/public>/i <Directory \/var\/www\/html>' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<\/Directory>/a <\/Directory>' /etc/apache2/sites-available/000-default.conf

# Asegura que el directorio 'writable' tenga los permisos correctos para Apache
# El usuario 'www-data' es el usuario por defecto de Apache en esta imagen
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

# Expone el puerto 80, que es el puerto por defecto de Apache
EXPOSE 80

# El comando por defecto de la imagen de Apache (que inicia Apache) es suficiente.
# CMD ["apache2-foreground"]

# Usa una imagen base con PHP y Apache
# Puedes elegir una versión específica de PHP si lo necesitas, por ejemplo php:8.1-apache
FROM php:8.2-apache

# Instala dependencias del sistema necesarias para las extensiones de PHP
# Actualiza la lista de paquetes e instala las dependencias
# Incluimos dependencias para gd, zip, mysql/mariadb, exif, oniguruma (para mbstring) y icu (para intl)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    libmariadb-dev-compat \
    libmariadb-dev \
    libexif-dev \
    libonig-dev \
    libicu-dev \
    # Añade aquí cualquier otra dependencia del sistema si tu proyecto la necesita
    && rm -rf /var/lib/apt/lists/*

# Instala extensiones de PHP necesarias para CodeIgniter 4
# Modifica esta lista según las necesidades específicas de tu aplicación
# Ahora incluimos las extensiones que requieren las dependencias instaladas arriba
# Descomentamos y corregimos la instalación de la extensión intl
# --- CORRECCIÓN: Añadimos 'mysqli' a la lista de extensiones a instalar ---
RUN docker-php-ext-install pdo pdo_mysql mbstring exif gd iconv zip intl mysqli \
    # Añade aquí cualquier otra extensión de PHP si tu proyecto la necesita
    && docker-php-ext-enable pdo_mysql # Asegura que la extensión pdo_mysql esté habilitada explícitamente
    # No es necesario habilitar mysqli explícitamente con docker-php-ext-enable, install ya lo hace.
# --- FIN CORRECCIÓN ---

# Configura PHP para mostrar errores y registrarlos en stderr (para que Render los capture)
# Para producción, deberías cambiar 'display_errors' a 'Off' en tu .env.
# La configuración en 99-custom.ini es para el entorno del contenedor.
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/99-custom.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/99-custom.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/99-custom.ini \
    && echo "log_errors = On" >> /usr/local/etc/php/conf.d/99-custom.ini \
    && echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/99-custom.ini

# Habilita el módulo rewrite de Apache (necesario para las URLs amigables de CodeIgniter)
RUN a2enmod rewrite

# Copia los archivos de tu aplicación CodeIgniter al directorio por defecto de Apache
# Asegúrate de que la estructura de directorios en tu proyecto local coincida con esto
# La carpeta 'public' de CodeIgniter debe ser el DocumentRoot de Apache
COPY . /var/www/html/

# Agrega este paso para asegurar permisos de escritura en la carpeta writable de CodeIgniter
# Esto es crucial para que CodeIgniter pueda escribir logs y caché de sesión
# www-data es el usuario bajo el que corre Apache/PHP en esta imagen base
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

# Configura Apache para que el DocumentRoot apunte a la carpeta 'public' de CodeIgniter
# Esto asegura que solo los archivos públicos sean accesibles directamente
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# Establece el directorio de trabajo
WORKDIR /var/www/html

# --- INSTALACIÓN DE COMPOSER ---
# Descarga el instalador de Composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    # Ejecuta el instalador para instalar Composer globalmente
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    # Verifica que Composer esté instalado y elimina el instalador
    && composer --version \
    && rm composer-setup.php
# --- FIN INSTALACIÓN DE COMPOSER ---

# Ejecuta composer install para instalar las dependencias del proyecto
# --no-dev: no instala dependencias de desarrollo (más ligero para producción)
# --optimize-autoloader: optimiza el autoloader para un mejor rendimiento
RUN composer install --no-dev --optimize-autoloader

# Expone el puerto 80, que es el puerto por defecto de Apache
EXPOSE 80

# El comando por defecto para iniciar Apache (ya viene configurado en la imagen base)
# CMD ["apache2-foreground"]

# Usa una imagen base con PHP y Apache
# Puedes elegir una versión específica de PHP si lo necesitas, por ejemplo php:8.1-apache
FROM php:8.2-apache

# Instala extensiones de PHP necesarias para CodeIgniter 4
# Modifica esta lista según las necesidades específicas de tu aplicación
RUN docker-php-ext-install pdo pdo_mysql mbstring exif gd iconv
# Instala la extensión intl si la necesitas (común para localización)
# RUN docker-php-ext_install intl

# Habilita el módulo rewrite de Apache (necesario para las URLs amigables de CodeIgniter)
RUN a2enmod rewrite

# Copia los archivos de tu aplicación CodeIgniter al directorio por defecto de Apache
# Asegúrate de que la estructura de directorios en tu proyecto local coincida con esto
# La carpeta 'public' de CodeIgniter debe ser el DocumentRoot de Apache
COPY . /var/www/html/

# Configura Apache para que el DocumentRoot apunte a la carpeta 'public' de CodeIgniter
# Esto asegura que solo los archivos públicos sean accesibles directamente
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Opcional: Si usas Composer para gestionar dependencias, puedes añadir estos pasos
# Instala Composer
# COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
# Ejecuta composer install para instalar las dependencias
# RUN composer install --no-dev --optimize-autoloader

# Expone el puerto 80, que es el puerto por defecto de Apache
EXPOSE 80

# El comando por defecto para iniciar Apache (ya viene configurado en la imagen base)
# CMD ["apache2-foreground"]

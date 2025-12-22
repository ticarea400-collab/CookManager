# 1. Usamos la imagen oficial de PHP con Apache ya configurado (corre sobre Linux)
FROM php:8.2-apache

# 2. Instalamos extensiones para conectar a la base de datos
RUN docker-php-ext-install pdo pdo_mysql

# 3. Copiamos los archivos de tu proyecto a la carpeta donde Apache busca la web
COPY . /var/www/html/CookManager

# 4. EXPOSE 80: Le avisamos a Docker que Apache escucha en el puerto 80
EXPOSE 80
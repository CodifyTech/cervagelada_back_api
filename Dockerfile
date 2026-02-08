# Usa a imagem oficial do PHP com FPM
FROM php:8.2-fpm

# Instala dependências do sistema e Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensões PHP necessárias para o Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuração do Nginx (Inline para evitar arquivos externos)
RUN echo 'server { \
    listen 80; \
    root /var/www/html/public; \
    index index.php index.html; \
    server_name _; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        include fastcgi_params; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
    } \
}' > /etc/nginx/sites-available/default

# Configuração do Supervisor (para rodar Nginx e PHP-FPM juntos)
RUN echo '[supervisord] \
nodaemon=true \
\
[program:nginx] \
command=nginx -g "daemon off;" \
stdout_logfile=/dev/stdout \
stdout_logfile_maxbytes=0 \
stderr_logfile=/dev/stderr \
stderr_logfile_maxbytes=0 \
\
[program:php-fpm] \
command=docker-php-entrypoint php-fpm \
stdout_logfile=/dev/stdout \
stdout_logfile_maxbytes=0 \
stderr_logfile=/dev/stderr \
stderr_logfile_maxbytes=0' > /etc/supervisor/conf.d/supervisord.conf

# Define o diretório de trabalho padrão
WORKDIR /var/www/html

# Copia dependências do Composer primeiro (para cache)
COPY composer.json composer.lock ./

# Instala dependências (Otimizado para produção)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copia o restante da aplicação
COPY . .

# Ajusta permissões (Crítico para Laravel)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expõe a porta 80 (Nginx)
EXPOSE 80

# Inicia o Supervisor (que inicia Nginx e PHP-FPM)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

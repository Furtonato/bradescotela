FROM php:8.2-apache-bullseye

# Habilitar mod_rewrite e instalar dependências
# 1. Habilita o mod_rewrite (necessário para o .htaccess)
# 2. Instala dependências e extensões do PHP
RUN a2enmod rewrite \
    && apt-get update && apt-get install -y \
        libpq-dev \
        unzip \
        git \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Instalar composer
# Copia o binário do composer da imagem oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Definir o diretório de trabalho padrão do Apache
WORKDIR /var/www/html

# *** AJUSTE PRINCIPAL: Copia a nova configuração do Virtual Host do Apache ***
# Isso sobrescreve a configuração padrão, apontando para 'public/'
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar arquivos do composer e instalar dependências (aproveita o cache)
COPY composer.json composer.lock ./
# Instala as dependências (sem desenvolvimento e otimizado)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copiar o restante da aplicação
# Copia todo o código da aplicação (incluindo a pasta public)
COPY . .

# Executar scripts do composer (se houver) e otimizar
RUN composer dump-autoload --optimize && composer run-script post-install-cmd --no-dev || true

# Definir permissões
# Garante que o usuário www-data (padrão do Apache) possa ler/escrever nos arquivos
RUN chown -R www-data:www-data /var/www/html

# Expor a porta e iniciar o servidor
EXPOSE 80
# O comando padrão da imagem PHP-Apache para iniciar o servidor
CMD ["apache2-foreground"]
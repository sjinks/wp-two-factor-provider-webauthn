ARG VERSION=php8.0@sha256:06b3c3b2fdc126d5e28b1f1c78a99009fe186d7354c907074095d5661bd18570
FROM wordpress:${VERSION}
RUN \
    a2enmod ssl && \
    a2ensite default-ssl.conf && \
    curl -L https://github.com/FiloSottile/mkcert/releases/download/v1.4.4/mkcert-v1.4.4-linux-amd64 -o /usr/local/bin/mkcert && \
    chmod +x /usr/local/bin/mkcert && \
    /usr/local/bin/mkcert -cert-file /etc/ssl/certs/ssl-cert-snakeoil.pem -key-file /etc/ssl/private/ssl-cert-snakeoil.key localhost && \
    mkdir -p /var/www/mu-plugins

RUN \
    curl -L https://phar.phpunit.de/phpcov.phar -o /usr/local/bin/phpcov && \
    chmod +x /usr/local/bin/phpcov && \
    pecl install pcov && \
    echo 'extension=pcov.so' > /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini && \
    echo 'pcov.enabled=1' >> /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini

COPY e2e-helper.php e2e-coverage.php /var/www/mu-plugins/

ARG VERSION=php8.3@sha256:9b46af6a41fd0dcb2e5cf85033a6350d05010356847ede1cfcac5cf94292eb26
FROM wordpress:${VERSION}
RUN \
    a2enmod ssl && \
    a2ensite default-ssl.conf && \
    curl -L https://github.com/FiloSottile/mkcert/releases/download/v1.4.4/mkcert-v1.4.4-linux-amd64 -o /usr/local/bin/mkcert && \
    chmod +x /usr/local/bin/mkcert && \
    /usr/local/bin/mkcert -cert-file /etc/ssl/certs/ssl-cert-snakeoil.pem -key-file /etc/ssl/private/ssl-cert-snakeoil.key localhost && \
    mkdir -p /var/www/mu-plugins

# E2E tests do not require coverage; newer PHP images may omit PECL.
RUN if command -v pecl > /dev/null 2>&1; then \
    curl -L https://phar.phpunit.de/phpcov.phar -o /usr/local/bin/phpcov && \
    chmod +x /usr/local/bin/phpcov && \
    pecl install pcov && \
    echo 'extension=pcov.so' > /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini && \
    echo 'pcov.enabled=1' >> /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini; \
    else \
        echo 'PECL not available; skipping optional PCOV coverage tooling.'; \
    fi

COPY e2e-helper.php e2e-coverage.php /var/www/mu-plugins/

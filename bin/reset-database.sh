#!/bin/sh

set -ex

cd "$(dirname "$0")/.."

docker compose exec -T wordpress rm -rf /var/www/html/wp-content/mu-plugins
docker compose run --rm cli sh -c '\
    wp db reset --yes && \
    wp core install --url="https://localhost:8443" --title="Test Site" --admin_user=admin --admin_password=password --admin_email=wordpress@example.com --skip-email && \
    wp rewrite structure "/%postname%/" && \
    pin_two_factor=$(wp eval "echo version_compare( get_bloginfo( \"version\" ), \"7.0\", \"<\" ) ? \"yes\" : \"no\";") && \
    if [ "$pin_two_factor" = yes ]; then \
        wp plugin install --activate --force two-factor --version=0.16.0; \
    else \
        wp plugin install --activate --force two-factor; \
    fi && \
    wp plugin activate two-factor-provider-webauthn && \
    wp user create user1 user1@example.com --user_pass=password && \
    wp user create user2 user2@example.com --user_pass=password && \
    wp user create user3 user3@example.com --user_pass=password \
'

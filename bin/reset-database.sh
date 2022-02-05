#!/bin/sh

set -x
set -e

cd "$(dirname "$0")/.."

docker-compose exec -T wordpress rm -rf /var/www/html/wp-content/mu-plugins
docker-compose run --rm cli sh -c '\
    wp db reset --yes && \
    wp core install --url="https://localhost:8443" --title="Test Site" --admin_user=admin --admin_password=password --admin_email=wordpress@example.com --skip-email && \
    wp rewrite structure "/%postname%/" && \
    wp plugin install --activate two-factor && \
    wp plugin activate two-factor-provider-webauthn && \
    wp user create user1 user1@example.com --user_pass=password && \
    wp user create user2 user2@example.com --user_pass=password && \
    wp user create user3 user3@example.com --user_pass=password \
'

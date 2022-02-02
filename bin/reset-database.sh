#!/bin/sh

set -x
set -e

cd "$(dirname "$0")/.."

docker-compose exec -T wordpress rm -rf /var/www/html/wp-content/mu-plugins
docker-compose run --rm cli sh -c '\
    wp db reset --yes && \
    wp core install --url="https://localhost:8443" --title="Test Site" --admin_user=admin --admin_password=password --admin_email=wordpress@example.com --skip-email && \
    wp rewrite structure "/%postname%/" && \
    wp plugin install --activate two-factor \
'

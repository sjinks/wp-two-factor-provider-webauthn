#!/bin/sh

set -x
set -e

cd ..
docker-compose up -d --build
while ! docker-compose run --rm cli wp db check; do
    sleep 1;
done
docker-compose run --rm cli wp core install --url="https://127.0.0.1:8443" --title="Some Site" --admin_user=admin --admin_password=password --admin_email=wordpress@example.com --skip-email
docker-compose run --rm cli wp plugin install --activate two-factor

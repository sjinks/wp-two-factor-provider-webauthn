#!/bin/sh

set -x
set -e

cd "$(dirname "$0")/.."
docker-compose up -d --build
while ! docker-compose run --rm cli wp db check; do
    sleep 1;
done

mkdir -p -m 0777 coverage-report
"$(dirname "$0")/reset-database.sh"

docker-compose exec -T wordpress cp -aR /var/www/mu-plugins /var/www/html/wp-content/

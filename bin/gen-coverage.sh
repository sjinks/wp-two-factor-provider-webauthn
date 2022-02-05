#!/bin/sh

set -x
set -e

cd "$(dirname "$0")/.."

docker-compose exec wordpress sh -c "\
    cd /var/www/html/wp-content/plugins/two-factor-provider-webauthn && \
    phpcov merge coverage-report $*
"

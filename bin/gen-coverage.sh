#!/bin/sh

set -ex

cd "$(dirname "$0")/.."
exec docker compose exec -w /var/www/html/wp-content/plugins/two-factor-provider-webauthn wordpress phpcov merge coverage-report "$@"

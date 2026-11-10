#!/bin/sh

export ADMIN_USERNAME=vipgo
export E2E_WP_CLI=vip
export PLAYWRIGHT_BASE_URL=https://tfa-webauthn.vipdev.lndo.site
npx playwright test

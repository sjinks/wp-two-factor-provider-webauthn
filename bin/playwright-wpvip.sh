#!/bin/sh

export ADMIN_USERNAME=vipgo
export PLAYWRIGHT_BASE_URL=https://tfa-webauthn.vipdev.lndo.site
npx playwright test

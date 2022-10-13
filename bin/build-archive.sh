#!/bin/sh

(
    cd .. && \
    git archive --format=tar --prefix=two-factor-provider-webauthn/ HEAD | (cd /var/tmp/ && tar xf -)
    (cd /var/tmp/two-factor-provider-webauthn && composer install --no-dev --no-interaction && composer remove --update-no-dev --no-interaction composer/installers cweagans/composer-patches && rm -rf composer.lock composer.json patches vendor/madwizard/webauthn/.github vendor/madwizard/webauthn/conformance vendor/madwizard/webauthn/tests vendor/psr/log/Psr/Log/Test vendor/typisttech) && \
    (cd /var/tmp && zip -r -9 two-factor-provider-webauthn.zip two-factor-provider-webauthn) && \
    mv /var/tmp/two-factor-provider-webauthn.zip two-factor-provider-webauthn.zip
)

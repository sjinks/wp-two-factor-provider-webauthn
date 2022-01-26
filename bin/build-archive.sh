#!/bin/sh

(
    cd .. && \
    git archive --format=tar --prefix=two-factor-provider-webauthn/ HEAD | (cd /var/tmp/ && tar xf -) && \
    cp composer.lock /var/tmp/two-factor-provider-webauthn/ && \
    (cd /var/tmp/two-factor-provider-webauthn && composer install --no-dev --no-interaction && composer remove --update-no-dev --no-interaction composer/installers && rm -f composer.lock) && \
    (cd /var/tmp && zip -r -9 two-factor-provider-webauthn.zip two-factor-provider-webauthn) && \
    mv /var/tmp/two-factor-provider-webauthn.zip two-factor-provider-webauthn.zip
)

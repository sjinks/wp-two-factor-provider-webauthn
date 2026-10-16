#!/bin/sh

set -ex

export VIP_PROXY=
export DO_NOT_TRACK=1
vip dev-env destroy || true
vip dev-env create < /dev/null
vip dev-env start --skip-wp-versions-check
vip dev-env exec --quiet -- wp user update vipgo --user_pass=password
for browser in chromium firefox webkit; do
    vip dev-env exec --quiet -- wp user create "user1-$browser" "user1-$browser@example.com" --user_pass=password
done
vip dev-env exec --quiet -- wp user create user2 user2@example.com --user_pass=password
vip dev-env exec --quiet -- wp user create user3 user3@example.com --user_pass=password

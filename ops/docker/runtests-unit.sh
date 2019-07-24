#!/usr/bin/env bash

set -ex

# run tests
cd /var/www/html

RESTORE_SHOP_AFTER_TESTS_SUITE=1 \
PHPBIN="php -d error_reporting=0" \
    vendor/bin/runtests

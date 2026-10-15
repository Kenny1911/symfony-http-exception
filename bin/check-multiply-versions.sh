#!/usr/bin/env bash

WORKING_DIR="$(dirname $(dirname $(realpath $0)))"
cd "${WORKING_DIR}"

for symfony_version in '^6.4' '^7.4' '8.0.*' '8.1.*'; do
  composer install &&
  composer update --with-all-dependencies \
    --with "symfony/event-dispatcher:${symfony_version}" \
    --with "symfony/http-foundation:${symfony_version}" \
    --with "symfony/http-kernel:${symfony_version}" \
    --with "symfony/expression-language:${symfony_version}" &&
  composer run checks || exit $?
done

for translation_version in '^2.3' '^3.0'; do
  composer install &&
  composer update --with-all-dependencies \
    --with "symfony/translation-contracts:${translation_version}" &&
  composer run checks || exit $?
done

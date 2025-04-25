#!/bin/sh

MATRIX_FILE=".github/workflows/tests.yml"
GREEN='\033[32m'
RED='\033[31m'
RESET='\033[0m'

# Check that phpenv is installed and available
if ! command -v phpenv >/dev/null 2>&1; then
  echo "${RED}✘ ${RESET} phpenv is not installed or not in your PATH."
  echo "Please install phpenv or ensure it's available in your shell environment."
  exit 1
fi

# Extract matrix values using yq and transform them into space-separated strings
php_versions=$(yq '.jobs.tests.strategy.matrix.php[]' "$MATRIX_FILE" | tr '\n' ' ')

# Loop through each matrix combination
for php_version in $php_versions; do
  echo $php_version > .php-version
  php_current_version=$(php -v | head -n 1 | cut -d " " -f 2)

  echo "==> ${php_version} >> ${php_current_version}"

  rm -rf composer.lock vendor
  composer update -qn --prefer-stable > /dev/null 2>&1
  if [ "$?" != "0" ]; then
    echo "${RED}✘ ${RESET} composer update failed"
    exit 1
  fi

  composer test  > "test-${php_version}.log" 2>&1
  if [ "$?" != "0" ]; then
    echo "${RED}✘ ${RESET} Test failed for PHP $php_version"
    exit 1
  else
    echo "${GREEN}✔ ${RESET} Tests passed for PHP"
  fi
done

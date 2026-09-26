#!/usr/bin/env bash
set -euo pipefail
rm -rf build dist
mkdir -p build dist
cp -R app config vendor composer.json composer.lock 2>/dev/null build/ || cp -R app config vendor composer.json build/
rm -rf build/app/storage build/app/tests
printf 'Build completed successfully.\n'

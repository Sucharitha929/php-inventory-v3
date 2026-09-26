#!/usr/bin/env bash
set -euo pipefail
mkdir -p dist
VERSION="${BUILD_VERSION:-1.0.${BUILD_NUMBER:-0}}"
rm -f "dist/php-inventory-${VERSION}.tar.gz"
tar -czf "dist/php-inventory-${VERSION}.tar.gz" -C build .
printf 'Created dist/php-inventory-%s.tar.gz\n' "$VERSION"

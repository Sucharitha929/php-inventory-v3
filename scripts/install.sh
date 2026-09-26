#!/usr/bin/env bash
set -euo pipefail
INSTALL_DIR="${INSTALL_DIR:-/opt/php-inventory}"
mkdir -p "$INSTALL_DIR"
if [[ -n "${1:-}" ]]; then tar -xzf "$1" -C "$INSTALL_DIR"; fi
mkdir -p "$INSTALL_DIR/app/storage"
php "$INSTALL_DIR/scripts/init-db.php"
echo "Installed to $INSTALL_DIR"

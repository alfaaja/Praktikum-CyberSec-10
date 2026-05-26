#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd -- "$SCRIPT_DIR/.." && pwd)"
LOG_DIR="$PROJECT_ROOT/logs"

reset_from_host() {
  : > "$LOG_DIR/access.log"
  : > "$LOG_DIR/error.log"
  : > "$LOG_DIR/other_vhosts_access.log"
}

reset_from_container() {
  if command -v docker >/dev/null 2>&1; then
    (
      cd "$PROJECT_ROOT"
      docker compose exec -T web sh -lc ': > /var/log/apache2/access.log && : > /var/log/apache2/error.log && : > /var/log/apache2/other_vhosts_access.log'
    )
    return
  fi

  if command -v docker-compose >/dev/null 2>&1; then
    (
      cd "$PROJECT_ROOT"
      docker-compose exec -T web sh -lc ': > /var/log/apache2/access.log && : > /var/log/apache2/error.log && : > /var/log/apache2/other_vhosts_access.log'
    )
    return
  fi

  return 1
}

if reset_from_host 2>/dev/null; then
  :
elif reset_from_container 2>/dev/null; then
  :
else
  echo "[!] Gagal mengosongkan log secara otomatis."
  echo "[!] File log kemungkinan dimiliki container."
  echo "[!] Coba salah satu perintah ini dari root project:"
  echo "    docker compose exec -T web sh -lc ': > /var/log/apache2/access.log && : > /var/log/apache2/error.log && : > /var/log/apache2/other_vhosts_access.log'"
  echo "    atau"
  echo "    sudo truncate -s 0 \"$LOG_DIR/access.log\" \"$LOG_DIR/error.log\" \"$LOG_DIR/other_vhosts_access.log\""
  exit 1
fi

echo "[+] Semua log berhasil dikosongkan."
echo "[+] Siap untuk demo baru."

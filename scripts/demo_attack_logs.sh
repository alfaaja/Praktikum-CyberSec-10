#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd -- "$SCRIPT_DIR/.." && pwd)"

BASE_URL="${1:-http://127.0.0.1:8080}"

section() {
  echo ""
  echo "============================================================"
  echo "$1"
  echo "============================================================"
}

explain() {
  echo "    $1"
}

show_cmd() {
  echo "\$ $1"
}

run_get() {
  local label="$1"
  local path="$2"
  local referer="${3:-$BASE_URL/}"
  local request_url="$BASE_URL$path"

  show_cmd "curl \"$request_url\""
  printf "[GET]  %-24s " "$label"
  curl -sS -o /dev/null \
    -H "Referer: $referer" \
    -w "status=%{http_code} bytes=%{size_download} time=%{time_total}s\n" \
    "$request_url"
}

run_post_login() {
  local label="$1"
  local password="$2"
  local request_url="$BASE_URL/login.php"

  show_cmd "curl -X POST \"$request_url\" -d \"username=admin&password=$password\""
  printf "[POST] %-24s " "$label"
  curl -sS -o /dev/null \
    -X POST \
    -H "Referer: $BASE_URL/login.php" \
    -d "username=admin&password=$password" \
    -w "status=%{http_code} bytes=%{size_download} time=%{time_total}s\n" \
    "$request_url"
}

run_raw_url() {
  local label="$1"
  local raw_url="$2"

  show_cmd "curl \"$raw_url\""
  printf "[GET]  %-24s " "$label"
  curl -sS -o /dev/null \
    -H "Referer: $BASE_URL/products.php" \
    -w "status=%{http_code} bytes=%{size_download} time=%{time_total}s\n" \
    "$raw_url"
}

section "Target"
echo "[+] Base URL              : $BASE_URL"
echo "[+] Root project          : $PROJECT_ROOT"
echo "[+] Log file yang dipantau: $PROJECT_ROOT/logs/access.log"
echo ""
echo "[+] Saran:"
echo "    Jalankan 'tail -f \"$PROJECT_ROOT/logs/access.log\"' di terminal lain."

section "1. Traffic Normal"
explain "Tahap ini meniru user toko biasa: buka home, katalog, detail produk, keranjang, promo, dan halaman seller."
explain "Yang diamati di log: request tersebar di beberapa endpoint, dominan GET, dan status 200."
echo ""
echo "[+] Command yang dijalankan pada fase ini:"
run_get "home" "/"
run_get "katalog" "/products.php" "$BASE_URL/"
run_get "cari-keyboard" "/products.php?q=keyboard" "$BASE_URL/products.php"
run_get "detail-1" "/product.php?id=1" "$BASE_URL/products.php"
run_get "detail-2" "/product.php?id=2" "$BASE_URL/products.php"
run_get "keranjang" "/cart.php?add=1" "$BASE_URL/product.php?id=1"
run_get "promo" "/promo.php" "$BASE_URL/"
run_get "seller-center" "/admin.php" "$BASE_URL/login.php"
run_get "404-check" "/not-found-test" "$BASE_URL/"

section "2. Brute Force Login"
explain "Tahap ini mengirim banyak POST ke /login.php dengan password berbeda."
explain "Yang diamati di log: lonjakan request POST ke satu endpoint dari IP yang sama."
echo ""
echo "[+] Pola command yang dipakai:"
show_cmd 'for i in {1..20}; do curl -X POST "'"$BASE_URL"'/login.php" -d "username=admin&password=pass$i"; done'
for i in $(seq 1 20); do
  run_post_login "login-attempt-$i" "pass$i"
done

section "3. SQL Injection Pattern"
explain "Tahap ini mengirim query string yang mengandung pola mencurigakan seperti OR, UNION, dan comment '--'."
explain "Yang diamati di log: payload aneh muncul langsung di request URI."
echo ""
echo "[+] Command yang dijalankan pada fase ini:"
run_raw_url "sqli-or" "$BASE_URL/product.php?id=1%27%20OR%20%271%27=%271"
run_raw_url "sqli-union" "$BASE_URL/product.php?id=1%20UNION%20SELECT%201,2,3"
run_raw_url "sqli-comment" "$BASE_URL/product.php?id=1%20AND%201=1--"

section "4. Request Flood / DDoS Ringan"
explain "Tahap ini menaikkan volume request ke homepage dalam waktu singkat."
explain "Yang diamati di log: satu IP mendominasi jumlah request, terutama ke path '/'."
if command -v ab >/dev/null 2>&1; then
  show_cmd "ab -n 100 -c 10 $BASE_URL/"
  ab -n 100 -c 10 "$BASE_URL/" || true
else
  echo "[!] apache benchmark 'ab' belum terinstall. Fallback ke loop curl 30 request."
  echo "    Install kalau perlu: sudo apt install apache2-utils -y"
  echo "[+] Pola command fallback:"
  show_cmd 'for i in {1..30}; do curl "'"$BASE_URL"'/" ; done'
  for i in $(seq 1 30); do
    run_get "flood-$i" "/" "$BASE_URL/"
  done
fi

section "Selesai"
echo "[+] Buka analisis cepat dengan:"
echo "    \"$PROJECT_ROOT/scripts/analyze_logs.sh\""
echo ""
echo "[+] Query manual yang bisa dipakai sambil presentasi:"
echo "    awk '{print \$1}' \"$PROJECT_ROOT/logs/access.log\" | sort | uniq -c | sort -nr | head"
echo "    grep 'POST /login.php' \"$PROJECT_ROOT/logs/access.log\""
echo "    grep -Ei 'union|select|information_schema|%27|--|or' \"$PROJECT_ROOT/logs/access.log\""

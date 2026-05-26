#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd -- "$SCRIPT_DIR/.." && pwd)"
DEFAULT_LOG_FILE="$PROJECT_ROOT/logs/access.log"
LOG_FILE="${1:-$DEFAULT_LOG_FILE}"

if [ ! -f "$LOG_FILE" ] && [ -f "$PROJECT_ROOT/$LOG_FILE" ]; then
  LOG_FILE="$PROJECT_ROOT/$LOG_FILE"
fi

if [ ! -f "$LOG_FILE" ]; then
  echo "[!] File log tidak ditemukan: $LOG_FILE"
  exit 1
fi

section() {
  echo ""
  echo "=============================="
  echo "$1"
  echo "=============================="
}

explain() {
  echo ""
  echo "Penjelasan: $1"
}

show_cmd() {
  echo "\$ $1"
}

count_matches() {
  local pattern="$1"
  grep -Ei -c "$pattern" "$LOG_FILE" 2>/dev/null || true
}

section "Ringkasan Log"
echo "[+] Command yang dipakai:"
show_cmd "wc -l \"$LOG_FILE\""
show_cmd "awk '{print \$1}' \"$LOG_FILE\" | sort | uniq -c | sort -nr | head"
TOTAL_REQUESTS="$(wc -l < "$LOG_FILE" | tr -d ' ')"
UNIQUE_IPS="$(awk '{print $1}' "$LOG_FILE" | sort -u | wc -l | tr -d ' ')"
echo "Total request   : $TOTAL_REQUESTS"
echo "IP unik         : $UNIQUE_IPS"
echo "Top IP request  :"
awk '{count[$1]++} END {for (ip in count) print count[ip], ip}' "$LOG_FILE" | sort -nr | head
explain "Bagian ini memberi gambaran umum sebelum masuk ke jenis serangan tertentu. Lihat total request, jumlah IP unik, dan siapa pengirim request paling dominan."

section "1. Analisis Log Normal"
echo "[+] Command yang dipakai:"
show_cmd "awk '\$6 ~ /\"GET/ && (\$7 == \"/\" || \$7 ~ /^\\/products\\.php/ || \$7 ~ /^\\/product\\.php/ || \$7 ~ /^\\/cart\\.php/ || \$7 == \"/promo.php\" || \$7 == \"/style.css\") {count[\$7]++} END {for (path in count) print count[path], path}' \"$LOG_FILE\" | sort -nr"
NORMAL_COUNT="$(
  awk '$6 ~ /"GET/ && ($7 == "/" || $7 ~ /^\/products\.php/ || $7 ~ /^\/product\.php/ || $7 ~ /^\/cart\.php/ || $7 == "/promo.php" || $7 == "/style.css") && tolower($0) !~ /union|select|information_schema|%27|--|sleep\(/ && tolower($0) !~ /%20or%20|%20and%20/ {count++} END {print count + 0}' "$LOG_FILE"
)"
echo "Request normal toko terdeteksi: $NORMAL_COUNT"
echo "Endpoint toko yang paling sering muncul:"
awk '$6 ~ /"GET/ && ($7 == "/" || $7 ~ /^\/products\.php/ || $7 ~ /^\/product\.php/ || $7 ~ /^\/cart\.php/ || $7 == "/promo.php" || $7 == "/style.css") && tolower($0) !~ /union|select|information_schema|%27|--|sleep\(/ && tolower($0) !~ /%20or%20|%20and%20/ {count[$7]++} END {for (path in count) print count[path], path}' "$LOG_FILE" | sort -nr
explain "Traffic normal biasanya tersebar ke beberapa halaman toko, dominan GET, dan status code-nya normal. Ini jadi pembanding saat nanti muncul pola serangan yang lebih fokus dan lebih agresif."

section "2. Deteksi DDoS dari Log"
echo "[+] Command yang dipakai:"
show_cmd "awk '{print \$1}' \"$LOG_FILE\" | sort | uniq -c | sort -nr | head"
show_cmd "awk '{print \$7}' \"$LOG_FILE\" | sort | uniq -c | sort -nr | head"
show_cmd "awk '{print \$9}' \"$LOG_FILE\" | sort | uniq -c | sort -nr"
echo "IP dengan jumlah request tertinggi:"
awk '{count[$1]++} END {for (ip in count) print count[ip], ip}' "$LOG_FILE" | sort -nr | head
echo ""
echo "Endpoint paling sering dihantam:"
awk '{count[$7]++} END {for (path in count) print count[path], path}' "$LOG_FILE" | sort -nr | head
echo ""
echo "Status code terbanyak:"
awk '{count[$9]++} END {for (code in count) print count[code], code}' "$LOG_FILE" | sort -nr

ROOT_HITS="$(awk '$7 == "/" {count++} END {print count + 0}' "$LOG_FILE")"
TOP_IP_COUNT="$(awk '{count[$1]++} END {max=0; for (ip in count) if (count[ip] > max) max=count[ip]; print max + 0}' "$LOG_FILE")"
echo ""
echo "Request ke homepage (/): $ROOT_HITS"
echo "Request terbanyak dari satu IP: $TOP_IP_COUNT"
if [ "$TOP_IP_COUNT" -ge 50 ]; then
  echo "Indikasi flood kuat: satu IP menghasilkan >= 50 request."
else
  echo "Belum ada lonjakan besar. Jalankan ab/curl loop jika ingin menunjukkan flood."
fi
explain "DDoS atau flood biasanya terlihat dari satu IP yang request-nya jauh lebih tinggi dari yang lain, sering ke satu endpoint yang sama, dalam waktu singkat dan berulang."

section "3. Deteksi Brute Force dari Log"
echo "[+] Command yang dipakai:"
show_cmd "grep 'POST /login.php' \"$LOG_FILE\""
show_cmd "grep 'POST /login.php' \"$LOG_FILE\" | awk '{print \$1}' | sort | uniq -c | sort -nr"
BRUTE_COUNT="$(awk '$6 == "\"POST" && $7 == "/login.php" {count++} END {print count + 0}' "$LOG_FILE")"
echo "Total POST ke /login.php: $BRUTE_COUNT"
if [ "$BRUTE_COUNT" -gt 0 ]; then
  echo "Distribusi request login per IP:"
  awk '$6 == "\"POST" && $7 == "/login.php" {count[$1]++} END {for (ip in count) print count[ip], ip}' "$LOG_FILE" | sort -nr
  echo ""
  echo "Contoh request login yang tercatat:"
  awk '$6 == "\"POST" && $7 == "/login.php" {print}' "$LOG_FILE"
else
  echo "Tidak ada POST /login.php ditemukan."
fi
explain "Brute force login umumnya terlihat dari banyak request POST ke halaman login dari sumber yang sama. Polanya beda dengan belanja normal karena user biasa tidak mengirim POST login berulang-ulang."

section "4. Deteksi SQL Injection dari Log"
SQLI_PATTERN='union|select|information_schema|%27|--|/\*|\bor\b|\band\b|sleep\('
echo "[+] Command yang dipakai:"
show_cmd "grep -Ei 'union|select|information_schema|%27|--|or|and|sleep' \"$LOG_FILE\""
SQLI_COUNT="$(count_matches "$SQLI_PATTERN")"
echo "Jumlah request mencurigakan: $SQLI_COUNT"
if [ "$SQLI_COUNT" -gt 0 ]; then
  echo "Request yang cocok dengan pattern SQLi:"
  grep -Ei "$SQLI_PATTERN" "$LOG_FILE"
else
  echo "Tidak ada pattern SQLi ditemukan."
fi
explain "SQL injection di access log biasanya terlihat dari query string yang mengandung keyword aneh seperti UNION, SELECT, OR, tanda comment SQL, atau karakter encoded seperti %27."

section "Bonus: 404 Probing"
echo "[+] Command yang dipakai:"
show_cmd "awk '\$9 == 404 {print}' \"$LOG_FILE\""
FOUR_O_FOUR_COUNT="$(awk '$9 == 404 {count++} END {print count + 0}' "$LOG_FILE")"
echo "Jumlah status 404: $FOUR_O_FOUR_COUNT"
if [ "$FOUR_O_FOUR_COUNT" -gt 0 ]; then
  awk '$9 == 404 {print}' "$LOG_FILE"
else
  echo "Tidak ada status 404 ditemukan."
fi
explain "404 probing sering muncul saat ada pihak yang mencoba menebak path atau file yang tidak ada. Kalau jumlah 404 tinggi dan polanya acak, itu patut dicurigai."

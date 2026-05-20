# Praktikum 10 - TokoKita Log Analysis Lab

Lab website toko sederhana untuk mendemokan analisis log:
- Traffic normal
- Request flood / DDoS simulation ringan
- Brute force login admin
- SQL Injection pattern pada parameter produk
- 404 probing

## Jalankan Lab

Kalau Docker Compose versi baru tersedia:

```bash
docker compose up -d
```

Kalau sistem memakai Compose versi lama:

```bash
docker-compose up -d
```

Buka website:

```text
http://127.0.0.1:8080
```

Pantau log:

```bash
tail -f logs/access.log
```

## Halaman Demo

| Path | Fungsi |
|---|---|
| `/` | Homepage toko |
| `/products.php` | Katalog produk |
| `/product.php?id=1` | Detail produk, demo SQLi pattern |
| `/login.php` | Login admin, demo brute force |
| `/cart.php` | Cart dummy |
| `/promo.php` | Promo dummy |
| `/admin.php` | Admin dummy |
| `/not-found-test` | Demo 404 |

## Demo Traffic Normal

```bash
curl http://127.0.0.1:8080/
curl http://127.0.0.1:8080/products.php
curl http://127.0.0.1:8080/product.php?id=1
curl http://127.0.0.1:8080/cart.php
```

## Demo Brute Force Lokal

```bash
for i in {1..20}; do
  curl -s -X POST http://127.0.0.1:8080/login.php \
  -d "username=admin&password=pass$i" > /dev/null
done
```

Analisis:

```bash
grep "POST /login.php" logs/access.log
grep "login" logs/access.log | awk '{print $1}' | sort | uniq -c | sort -nr
```

## Demo SQL Injection Pattern

```bash
curl "http://127.0.0.1:8080/product.php?id=1%27%20OR%20%271%27=%271"
curl "http://127.0.0.1:8080/product.php?id=1%20UNION%20SELECT%201,2,3"
curl "http://127.0.0.1:8080/product.php?id=1%20AND%201=1--"
```

Analisis:

```bash
grep -Ei "union|select|information_schema|%27|--|or" logs/access.log
```

## Demo Request Flood Ringan

Install Apache Benchmark:

```bash
sudo apt install apache2-utils -y
```

Jalankan ke localhost saja:

```bash
ab -n 300 -c 30 http://127.0.0.1:8080/
```

Analisis:

```bash
awk '{print $1}' logs/access.log | sort | uniq -c | sort -nr | head
awk '{print $7}' logs/access.log | sort | uniq -c | sort -nr | head
awk '{print $9}' logs/access.log | sort | uniq -c | sort -nr
```

## Script Otomatis

```bash
chmod +x scripts/demo_attack_logs.sh scripts/analyze_logs.sh
./scripts/demo_attack_logs.sh
./scripts/analyze_logs.sh
```

## Reset Log

```bash
sudo truncate -s 0 logs/access.log logs/error.log logs/other_vhosts_access.log
```

## Etika

Semua simulasi hanya untuk server lokal atau lab milik sendiri.
Jangan menjalankan flood, brute force, atau payload SQLi ke website publik tanpa izin tertulis.

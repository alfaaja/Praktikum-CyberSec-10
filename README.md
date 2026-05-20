# Praktikum 10 - TokoKita Log Analysis Lab

Lab ini berisi website toko sederhana berbasis PHP + Apache yang sengaja dipakai untuk membuat traffic normal dan beberapa pola serangan ringan agar bisa dianalisis dari log Apache.

Skenario yang bisa didemokan:
- Traffic normal user toko
- Brute force login admin
- SQL injection pattern pada parameter produk
- 404 probing
- Request flood / DDoS simulation ringan

## Tujuan Lab

Setelah menjalankan lab ini, praktikan diharapkan bisa:
- Menjalankan web lab lokal dengan Docker
- Menghasilkan log access dan error dari aktivitas web
- Membedakan traffic normal dan traffic mencurigakan
- Melakukan analisis dasar terhadap log Apache
- Menjelaskan indikasi brute force, SQLi, 404 probing, dan flood dari log

## Struktur Project

| Path | Fungsi |
|---|---|
| `src/` | Source code website toko |
| `logs/` | Output log Apache dari container |
| `scripts/demo_attack_logs.sh` | Script otomatis untuk menghasilkan traffic demo |
| `scripts/analyze_logs.sh` | Script analisis cepat untuk access log |
| `scripts/reset_logs.sh` | Script reset file log |
| `docker-compose.yml` | Konfigurasi container PHP Apache |

## Prasyarat

Sebelum mulai, pastikan perangkat lab punya:
- Docker Engine atau Docker Desktop
- Docker Compose plugin (`docker compose`) atau Compose lama (`docker-compose`)
- Browser atau `curl`
- Opsional: `apache2-utils` kalau ingin memakai `ab` untuk simulasi flood

Kalau Docker belum terpasang dan sistemmu berbasis Ubuntu, Debian, atau Kali, install cepat dengan satu baris:

```bash
sudo apt update && sudo apt install -y docker.io docker-compose curl apache2-utils
```

Keterangan paket:
- `docker.io` untuk Docker Engine
- `docker-compose` untuk command `docker compose` atau `docker-compose`
- `curl` untuk uji request manual dari terminal
- `apache2-utils` opsional untuk simulasi flood memakai `ab`

Setelah install, aktifkan service Docker:

```bash
sudo systemctl enable --now docker
```

Supaya user biasa bisa menjalankan Docker tanpa `sudo`, tambahkan user ke group Docker:

```bash
sudo usermod -aG docker $USER
```

Lalu logout-login lagi, atau jalankan:

```bash
newgrp docker
```

Cek versi tool:

```bash
docker --version
docker compose version
```

Kalau `docker compose` tidak tersedia, coba:

```bash
docker-compose --version
```

## Setup Lab dari Nol

### 1. Masuk ke folder project

```bash
cd /path/ke/praktikum10-toko-log-lab
```

Kalau project masih berbentuk `.zip`, extract dulu lalu masuk ke folder hasil extract.

Kalau Docker belum di-install, kerjakan dulu langkah install di bagian **Prasyarat**, lalu lanjut ke langkah berikutnya.

### 2. Jalankan container

Kalau sistem memakai Compose versi baru:

```bash
docker compose up -d
```

Kalau sistem memakai Compose versi lama:

```bash
docker-compose up -d
```

### 3. Verifikasi container berjalan

```bash
docker compose ps
```

Atau kalau memakai Compose lama:

```bash
docker-compose ps
```

Container web akan publish aplikasi ke port `8080`.

### 4. Buka website lab

```text
http://127.0.0.1:8080
```

Kalau halaman home TokoKita tampil, berarti setup berhasil.

### 5. Pastikan log bisa dipantau

Jalankan di terminal terpisah:

```bash
tail -f logs/access.log
```

Kalau kamu membuka halaman web lalu muncul baris baru di log, berarti lab siap dipakai.

## Informasi Demo

### Kredensial login

Halaman login tersedia di:

```text
http://127.0.0.1:8080/login.php
```

Kredensial valid:
- Username: `admin`
- Password: `admin123`

Kredensial ini berguna untuk membedakan login normal dan brute force.

### Halaman demo

| Path | Fungsi |
|---|---|
| `/` | Homepage toko |
| `/products.php` | Katalog produk |
| `/products.php?q=keyboard` | Simulasi pencarian produk |
| `/product.php?id=1` | Detail produk normal |
| `/product.php?id=payload` | Demo pola SQLi pada parameter `id` |
| `/login.php` | Login admin, demo brute force |
| `/cart.php` | Keranjang dummy |
| `/promo.php` | Halaman promo |
| `/admin.php` | Seller/admin dummy |
| `/not-found-test` | Demo status 404 |

## Langkah Praktikum Lengkap

Urutan ini cocok dipakai saat praktikum, demo kelas, atau presentasi.

### Langkah 1. Reset log sebelum mulai

Supaya hasil analisis bersih, kosongkan log dulu:

```bash
chmod +x scripts/reset_logs.sh
./scripts/reset_logs.sh
```

Kalau script gagal karena permission, alternatif manual:

```bash
sudo truncate -s 0 logs/access.log logs/error.log logs/other_vhosts_access.log
```

### Langkah 2. Pantau log secara realtime

Jalankan:

```bash
tail -f logs/access.log
```

Tujuannya supaya setiap request yang dikirim ke website langsung terlihat saat praktikum berjalan.

### Langkah 3. Buat traffic normal

Tujuan tahap ini adalah menunjukkan pola user biasa saat membuka halaman toko.

Contoh lewat browser:
1. Buka home page
2. Masuk ke katalog produk
3. Cari produk dengan kata kunci tertentu
4. Buka detail produk
5. Tambahkan produk ke keranjang
6. Buka halaman promo

Contoh lewat `curl`:

```bash
curl http://127.0.0.1:8080/
curl http://127.0.0.1:8080/products.php
curl "http://127.0.0.1:8080/products.php?q=keyboard"
curl "http://127.0.0.1:8080/product.php?id=1"
curl "http://127.0.0.1:8080/cart.php?add=1"
curl http://127.0.0.1:8080/promo.php
```

Yang perlu diamati di log:
- Method dominan `GET`
- Endpoint tersebar di beberapa halaman
- Status umumnya `200`
- Tidak ada payload aneh di query string

### Langkah 4. Demo brute force login

Tahap ini meniru attacker yang mencoba banyak password ke endpoint login.

Jalankan:

```bash
for i in {1..20}; do
  curl -s -X POST http://127.0.0.1:8080/login.php \
  -d "username=admin&password=pass$i" > /dev/null
done
```

Kalau ingin menunjukkan login yang valid juga:

```bash
curl -X POST http://127.0.0.1:8080/login.php \
  -d "username=admin&password=admin123"
```

Yang perlu diamati di log:
- Banyak request `POST /login.php`
- Datang berulang dari IP yang sama
- Polanya lebih rapat dibanding user normal

Analisis cepat:

```bash
grep 'POST /login.php' logs/access.log
grep 'POST /login.php' logs/access.log | awk '{print $1}' | sort | uniq -c | sort -nr
```

### Langkah 5. Demo SQL injection pattern

Tahap ini tidak benar-benar menjalankan query database berbahaya, tetapi memunculkan pola payload mencurigakan di log.

Jalankan:

```bash
curl "http://127.0.0.1:8080/product.php?id=1%27%20OR%20%271%27=%271"
curl "http://127.0.0.1:8080/product.php?id=1%20UNION%20SELECT%201,2,3"
curl "http://127.0.0.1:8080/product.php?id=1%20AND%201=1--"
```

Yang perlu diamati di log:
- Ada karakter encoded seperti `%27`
- Ada keyword seperti `OR`, `AND`, `UNION`, `SELECT`
- Payload muncul di request URI

Analisis cepat:

```bash
grep -Ei 'union|select|information_schema|%27|--|or|and|sleep' logs/access.log
```

### Langkah 6. Demo 404 probing

Tahap ini menunjukkan request ke path yang tidak ada.

Jalankan:

```bash
curl http://127.0.0.1:8080/not-found-test
curl http://127.0.0.1:8080/backup.zip
curl http://127.0.0.1:8080/admin-old.php
```

Yang perlu diamati di log:
- Status code `404`
- Request menuju path yang tidak tersedia
- Bisa menjadi indikasi enumerasi file atau probing

Analisis cepat:

```bash
awk '$9 == 404 {print}' logs/access.log
```

### Langkah 7. Demo request flood / DDoS ringan

Kalau `ab` belum terpasang:

```bash
sudo apt install apache2-utils -y
```

Jalankan simulasi ringan:

```bash
ab -n 300 -c 30 http://127.0.0.1:8080/
```

Kalau tidak ingin install `ab`, bisa pakai loop sederhana:

```bash
for i in {1..30}; do
  curl -s http://127.0.0.1:8080/ > /dev/null
done
```

Yang perlu diamati di log:
- Satu IP mendominasi jumlah request
- Banyak request ke endpoint yang sama
- Trafik sangat rapat dalam waktu singkat

Analisis cepat:

```bash
awk '{print $1}' logs/access.log | sort | uniq -c | sort -nr | head
awk '{print $7}' logs/access.log | sort | uniq -c | sort -nr | head
awk '{print $9}' logs/access.log | sort | uniq -c | sort -nr
```

### Langkah 8. Jalankan analisis otomatis

Project ini sudah menyediakan script analisis:

```bash
chmod +x scripts/analyze_logs.sh
./scripts/analyze_logs.sh
```

Script ini akan membantu menampilkan:
- Ringkasan total request
- IP paling aktif
- Indikasi traffic normal
- Indikasi brute force
- Indikasi SQLi
- 404 probing
- Ringkasan flood

### Langkah 9. Jalankan demo otomatis end-to-end

Kalau ingin langsung menghasilkan traffic campuran:

```bash
chmod +x scripts/demo_attack_logs.sh
./scripts/demo_attack_logs.sh
```

Setelah itu jalankan:

```bash
./scripts/analyze_logs.sh
```

## Alur Singkat Praktikum yang Disarankan

Kalau dosen atau asisten minta langkah paling ringkas, urutannya bisa seperti ini:
1. Jalankan `docker compose up -d`
2. Reset log dengan `./scripts/reset_logs.sh`
3. Pantau log dengan `tail -f logs/access.log`
4. Buat traffic normal
5. Jalankan brute force login
6. Jalankan payload SQLi
7. Jalankan 404 probing
8. Jalankan flood ringan
9. Analisis hasil dengan `./scripts/analyze_logs.sh`
10. Jelaskan perbedaan pola tiap skenario

## Query Analisis Manual

Beberapa query yang berguna saat presentasi:

Top IP:

```bash
awk '{print $1}' logs/access.log | sort | uniq -c | sort -nr | head
```

Top endpoint:

```bash
awk '{print $7}' logs/access.log | sort | uniq -c | sort -nr | head
```

Semua login POST:

```bash
grep 'POST /login.php' logs/access.log
```

Semua indikasi SQLi:

```bash
grep -Ei 'union|select|information_schema|%27|--|or|and|sleep' logs/access.log
```

Semua status 404:

```bash
awk '$9 == 404 {print}' logs/access.log
```

Total request:

```bash
wc -l logs/access.log
```

## Troubleshooting

### Port 8080 sudah dipakai

Kalau port `8080` bentrok, ubah bagian ini di `docker-compose.yml`:

```yaml
ports:
  - "8080:80"
```

Misalnya menjadi:

```yaml
ports:
  - "8081:80"
```

Lalu akses:

```text
http://127.0.0.1:8081
```

### Log tidak bertambah

Cek:
- Container benar-benar running
- Request diarahkan ke port yang benar
- Folder `logs/` masih ter-mount ke container

Verifikasi:

```bash
docker compose ps
docker compose logs web
```

### Script tidak bisa dieksekusi

Berikan permission execute:

```bash
chmod +x scripts/*.sh
```

### Docker Compose command tidak tersedia

Gunakan command lama:

```bash
docker-compose up -d
```

## Menghentikan Lab

Kalau praktikum selesai:

```bash
docker compose down
```

Atau:

```bash
docker-compose down
```

## Etika dan Batasan

Semua simulasi dalam repo ini hanya untuk:
- localhost
- mesin lab milik sendiri
- lingkungan pembelajaran yang punya izin

Jangan menjalankan brute force, flood, probing, atau payload SQLi ke sistem publik tanpa izin tertulis.

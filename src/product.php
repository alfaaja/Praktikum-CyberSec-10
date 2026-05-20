<?php
require_once __DIR__ . "/data.php";
$page_title = "Detail Produk";
include __DIR__ . "/header.php";

$id = $_GET["id"] ?? "1";
$is_suspicious = looks_like_sqli($id);
$product = !$is_suspicious && isset($products[$id]) ? $products[$id] : null;
?>
<main class="wrap page">
  <a class="back" href="/products.php">← Kembali ke katalog</a>

  <?php if ($product): ?>
    <section class="detail-grid">
      <div class="detail-image-wrap">
        <div class="detail-image"><?= htmlspecialchars($product["image"]) ?></div>
        <div class="floating-note">
          <strong>Ready Stock</strong>
          <span>Tersedia untuk pengiriman cepat hari ini</span>
        </div>
      </div>
      <div class="detail-copy">
        <div class="detail-badges">
          <span class="tag"><?= htmlspecialchars($product["category"]) ?></span>
          <span class="tag soft">Pilihan pelanggan</span>
        </div>
        <h1><?= htmlspecialchars($product["name"]) ?></h1>
        <p class="price"><?= htmlspecialchars($product["price"]) ?></p>
        <p><?= htmlspecialchars($product["description"]) ?></p>
        <div class="stock-row">
          <p class="stock">Stok tersedia: <?= htmlspecialchars((string) $product["stock"]) ?></p>
          <span class="pill">Siap dikirim</span>
        </div>
        <div class="hero-actions">
          <a class="btn primary" href="/cart.php?add=<?= urlencode($id) ?>">Tambah ke Keranjang</a>
          <a class="btn ghost" href="/promo.php">Lihat Promo</a>
        </div>
      </div>
    </section>

    <div class="detail-lab-grid">
      <section class="panel">
        <h2>Info Pengiriman</h2>
        <ul class="clean-list">
          <li>Pesanan diproses di hari yang sama untuk order sebelum pukul 15.00.</li>
          <li>Kemasan dibuat ringkas dan aman untuk barang kecil maupun aksesoris kerja.</li>
          <li>Notifikasi status pesanan tersedia setelah checkout selesai.</li>
        </ul>
      </section>
      <section class="panel">
        <h2>Kenapa Banyak Dipilih</h2>
        <ul class="clean-list">
          <li>Deskripsi produk singkat dan jelas, cocok untuk pembelian cepat.</li>
          <li>Kategori dikurasi supaya pilihan tetap fokus dan tidak terasa terlalu ramai.</li>
          <li>Promo dan keranjang saling terhubung untuk pengalaman belanja yang mulus.</li>
        </ul>
      </section>
    </div>
  <?php else: ?>
    <div class="notice warning">
      <strong>Produk tidak tersedia.</strong>
      <p>Item yang kamu cari mungkin sedang habis, dipindahkan, atau tautannya sudah berubah.</p>
    </div>

    <section class="panel">
      <h2>Lanjutkan Belanja</h2>
      <p>Kembali ke katalog untuk melihat produk lain yang masih tersedia atau buka halaman promo untuk penawaran terbaru.</p>
      <div class="hero-actions">
        <a class="btn primary" href="/products.php">Lihat Produk Lain</a>
        <a class="btn ghost" href="/promo.php">Cek Promo</a>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php include __DIR__ . "/footer.php"; ?>

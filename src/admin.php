<?php
require_once __DIR__ . "/data.php";
$page_title = "Seller Center";
$total_categories = count(array_unique(array_column($products, "category")));
include __DIR__ . "/header.php";
?>
<main class="wrap page">
  <section class="page-hero">
    <div class="page-title">
      <p class="eyebrow">Seller Center</p>
      <h1>Dashboard toko yang ringkas dan mudah dibaca</h1>
      <p>Pantau pesanan, produk aktif, dan performa toko dalam satu halaman yang simpel untuk kebutuhan operasional harian.</p>
    </div>

    <div class="hero-metrics compact">
      <div class="metric-tile">
        <span>Pesanan hari ini</span>
        <strong>24</strong>
      </div>
      <div class="metric-tile">
        <span>Produk aktif</span>
        <strong><?= htmlspecialchars((string) count($products)) ?></strong>
      </div>
      <div class="metric-tile">
        <span>Pendapatan</span>
        <strong>Rp2,4jt</strong>
      </div>
    </div>
  </section>

  <div class="dash-grid">
    <div class="metric">
      <span>Pesanan Hari Ini</span>
      <strong>24</strong>
    </div>
    <div class="metric">
      <span>Produk Aktif</span>
      <strong><?= htmlspecialchars((string) count($products)) ?></strong>
    </div>
    <div class="metric">
      <span>Rating Toko</span>
      <strong>4.9 / 5</strong>
    </div>
  </div>

  <div class="command-grid two-column">
    <section class="command-card">
      <span class="chip">Pesanan Terbaru</span>
      <h3>Yang baru masuk pagi ini</h3>
      <ul class="clean-list">
        <li>Kopi Susu Aren 1 Liter untuk area Jakarta Selatan.</li>
        <li>Keyboard Mechanical Sagara dengan permintaan packing aman.</li>
        <li>Notebook Incident Report untuk hadiah tim kecil.</li>
      </ul>
    </section>

    <section class="command-card">
      <span class="chip">Performa Toko</span>
      <h3>Ringkasan mingguan yang paling penting</h3>
      <ul class="clean-list">
        <li>Produk kategori aksesoris masih jadi yang paling sering dilihat pelanggan.</li>
        <li>Voucher gratis ongkir memberi dampak paling tinggi ke checkout selesai.</li>
        <li>Jam pembelian tersibuk ada di rentang 19.00 sampai 21.00.</li>
      </ul>
    </section>
  </div>
</main>
<?php include __DIR__ . "/footer.php"; ?>

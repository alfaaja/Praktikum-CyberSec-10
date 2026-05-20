<?php
$page_title = "Promo";
include __DIR__ . "/header.php";
?>
<main class="wrap page">
  <section class="page-hero">
    <div class="page-title">
      <p class="eyebrow">Promo Mingguan</p>
      <h1>Promo yang bikin checkout terasa lebih ringan</h1>
      <p>Pilih voucher yang paling cocok untuk belanja hari ini, mulai dari potongan harga sampai gratis ongkir untuk item favoritmu.</p>
    </div>

    <div class="hero-metrics compact">
      <div class="metric-tile">
        <span>Kode promo</span>
        <strong>3 aktif</strong>
      </div>
      <div class="metric-tile">
        <span>Rekomendasi</span>
        <strong>Belanja hemat</strong>
      </div>
      <div class="metric-tile">
        <span>Berlaku</span>
        <strong>Minggu ini</strong>
      </div>
    </div>
  </section>

  <div class="promo-grid">
    <div class="promo-card">
      <span>FRESH10</span>
      <h2>Diskon 10%</h2>
      <p>Potongan harga untuk semua kategori pilihan tanpa minimum pembelian.</p>
    </div>
    <div class="promo-card">
      <span>ONGKIRIN</span>
      <h2>Gratis Ongkir</h2>
      <p>Gratis ongkir untuk belanja minimum Rp90.000 ke area layanan utama.</p>
    </div>
    <div class="promo-card">
      <span>BUNDLING</span>
      <h2>Hemat Paket</h2>
      <p>Belanja dua item atau lebih untuk mendapatkan harga yang terasa lebih bersahabat.</p>
    </div>
  </div>

  <div class="command-grid two-column">
    <article class="command-card">
      <span class="chip">Cara Pakai</span>
      <h3>Voucher langsung dipakai saat checkout</h3>
      <ul class="clean-list">
        <li>Pilih produk favoritmu terlebih dahulu dari katalog.</li>
        <li>Simpan item ke keranjang lalu pilih promo yang paling sesuai.</li>
        <li>Gunakan kode voucher sebelum menyelesaikan pembelian.</li>
      </ul>
    </article>
    <article class="command-card">
      <span class="chip">Syarat Singkat</span>
      <h3>Belanja hemat tanpa ribet</h3>
      <ul class="clean-list">
        <li>Promo tidak dapat digabung dengan potongan harga lain dalam satu transaksi.</li>
        <li>Voucher gratis ongkir mengikuti area pengiriman yang tersedia.</li>
        <li>Stok promo dapat berubah sewaktu-waktu sesuai ketersediaan produk.</li>
      </ul>
    </article>
  </div>
</main>
<?php include __DIR__ . "/footer.php"; ?>

<?php
require_once __DIR__ . "/data.php";
$page_title = "Home";
$total_products = count($products);
$total_categories = count(array_unique(array_column($products, "category")));
$featured_products = array_slice($products, 0, 3, true);
include __DIR__ . "/header.php";
?>
<main>
  <section class="hero">
    <div class="wrap hero-grid">
      <div class="hero-copy">
        <div class="hero-badges">
          <span class="hero-badge">Gratis Ongkir</span>
          <span class="hero-badge">Kurasi Mingguan</span>
          <span class="hero-badge">Pilihan Pelanggan</span>
        </div>
        <p class="eyebrow">Kebutuhan kerja, ngopi, dan daily setup</p>
        <h1>Belanja barang favorit yang bikin meja kerja terasa lebih hidup.</h1>
        <p class="lead">TokoKita merangkum produk praktis untuk teman kerja harian, sudut ngopi, dan setup ringan yang nyaman dipakai dari pagi sampai malam.</p>
        <div class="hero-actions">
          <a class="btn primary" href="/products.php">Lihat Produk</a>
          <a class="btn ghost" href="/promo.php">Promo Hari Ini</a>
        </div>
        <div class="hero-metrics-inline">
          <div>
            <strong><?= htmlspecialchars((string) $total_products) ?></strong>
            <span>produk pilihan</span>
          </div>
          <div>
            <strong><?= htmlspecialchars((string) $total_categories) ?></strong>
            <span>kategori favorit</span>
          </div>
          <div>
            <strong>4.9</strong>
            <span>rating pelanggan</span>
          </div>
        </div>
      </div>
      <div class="hero-panel">
        <div class="hero-card">
          <div class="hero-card-header">
            <div>
              <strong>Favorit Minggu Ini</strong>
              <span>Kurasi produk yang paling sering dipilih ulang</span>
            </div>
            <span class="hero-chip">Best Seller</span>
          </div>
          <div class="hero-metrics compact">
            <div class="metric-tile">
              <span>Pengiriman</span>
              <strong>Same Day</strong>
            </div>
            <div class="metric-tile">
              <span>Pembayaran</span>
              <strong>Aman</strong>
            </div>
            <div class="metric-tile">
              <span>Support</span>
              <strong>Responsif</strong>
            </div>
          </div>
          <div class="activity-list">
            <?php foreach ($featured_products as $product): ?>
              <div class="activity-item">
                <div>
                  <strong><?= htmlspecialchars($product["name"]) ?></strong>
                  <span><?= htmlspecialchars($product["description"]) ?></span>
                </div>
                <span class="activity-endpoint"><?= htmlspecialchars($product["price"]) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="wrap section">
    <div class="section-head">
      <div>
        <p class="eyebrow">Kenapa Belanja di Sini</p>
        <h2>Tampilan rapi, pilihan sederhana, dan checkout terasa ringan</h2>
      </div>
    </div>

    <div class="feature-grid">
      <article class="feature-card">
        <span class="feature-icon">01</span>
        <h3>Kurasi yang nggak bikin bingung</h3>
        <p>Produk dipilih secukupnya supaya kamu cepat menemukan barang yang relevan tanpa harus scroll terlalu jauh.</p>
      </article>
      <article class="feature-card">
        <span class="feature-icon">02</span>
        <h3>Cocok untuk meja kerja modern</h3>
        <p>Mulai dari minuman, aksesoris, sampai item kecil penunjang aktivitas harian semuanya dikemas dengan gaya yang konsisten.</p>
      </article>
      <article class="feature-card">
        <span class="feature-icon">03</span>
        <h3>Siap checkout kapan saja</h3>
        <p>Alur belanja dibuat ringkas supaya pelanggan bisa langsung pindah dari lihat produk ke promo lalu masuk keranjang tanpa hambatan.</p>
      </article>
    </div>
  </section>

  <section class="wrap section">
    <div class="section-head">
      <div>
        <p class="eyebrow">Produk Pilihan</p>
        <h2>Barang yang sering dicari</h2>
      </div>
      <a href="/products.php">Lihat semua →</a>
    </div>

    <div class="product-grid">
      <?php foreach (array_slice($products, 0, 4, true) as $id => $product): ?>
        <a class="product-card" href="/product.php?id=<?= urlencode($id) ?>">
          <div class="product-image"><?= htmlspecialchars($product["image"]) ?></div>
          <div class="product-info">
            <div class="product-meta">
              <span><?= htmlspecialchars($product["category"]) ?></span>
              <small>Stok <?= htmlspecialchars((string) $product["stock"]) ?></small>
            </div>
            <h3><?= htmlspecialchars($product["name"]) ?></h3>
            <div class="price-row">
              <p><?= htmlspecialchars($product["price"]) ?></p>
              <strong>Lihat Detail</strong>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="wrap section">
    <div class="section-head">
      <div>
        <p class="eyebrow">Pilihan Sesuai Mood</p>
        <h2>Belanja sesuai suasana kerjamu hari ini</h2>
      </div>
    </div>

    <div class="command-grid">
      <article class="command-card">
        <span class="chip">Work Setup</span>
        <h3>Untuk meja kerja yang lebih rapi</h3>
        <p>Pilih keyboard, mouse, dan notebook yang bikin ritme kerja lebih nyaman dari awal sampai akhir hari.</p>
      </article>
      <article class="command-card">
        <span class="chip">Coffee Break</span>
        <h3>Teman ngopi yang tetap produktif</h3>
        <p>Kopi susu literan, hoodie nyaman, dan aksesoris sederhana yang pas untuk jeda santai tanpa mengganggu fokus.</p>
      </article>
      <article class="command-card">
        <span class="chip">Gift Ideas</span>
        <h3>Hadiah kecil buat teman satu tim</h3>
        <p>Barang-barang simpel dengan harga ringan yang enak dijadikan kejutan untuk rekan kerja atau partner proyek.</p>
      </article>
    </div>
  </section>
</main>
<?php include __DIR__ . "/footer.php"; ?>

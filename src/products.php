<?php
require_once __DIR__ . "/data.php";
$page_title = "Produk";
include __DIR__ . "/header.php";

$q = $_GET["q"] ?? "";
$filtered = [];
$categories = array_values(array_unique(array_column($products, "category")));

foreach ($products as $id => $product) {
    if ($q === "" || stripos($product["name"], $q) !== false || stripos($product["category"], $q) !== false) {
        $filtered[$id] = $product;
    }
}
?>
<main class="wrap page">
  <section class="page-hero">
    <div class="page-title">
      <p class="eyebrow">Katalog</p>
      <h1>Produk TokoKita</h1>
      <p>Koleksi barang pilihan untuk teman kerja, sudut ngopi, dan kebutuhan kecil yang bikin aktivitas harian terasa lebih enak.</p>
    </div>

    <div class="hero-metrics compact">
      <div class="metric-tile">
        <span>Total produk</span>
        <strong><?= htmlspecialchars((string) count($products)) ?></strong>
      </div>
      <div class="metric-tile">
        <span>Kategori aktif</span>
        <strong><?= htmlspecialchars((string) count($categories)) ?></strong>
      </div>
      <div class="metric-tile">
        <span>Hasil pencarian</span>
        <strong><?= htmlspecialchars((string) count($filtered)) ?></strong>
      </div>
    </div>
  </section>

  <form class="search-box" method="GET" action="/products.php">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari kopi, keyboard, adapter...">
    <button type="submit">Cari</button>
  </form>

  <div class="result-toolbar">
    <p class="result-count">
      Menampilkan <strong><?= htmlspecialchars((string) count($filtered)) ?></strong> dari
      <strong><?= htmlspecialchars((string) count($products)) ?></strong> produk.
    </p>
    <div class="chip-row">
      <?php foreach ($categories as $category): ?>
        <span class="chip"><?= htmlspecialchars($category) ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="product-grid">
    <?php foreach ($filtered as $id => $product): ?>
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
            <strong>Buka</strong>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (!$filtered): ?>
    <section class="panel empty-state">
      <h2>Produk tidak ditemukan</h2>
      <p>Coba kata kunci lain seperti <code>kopi</code>, <code>keyboard</code>, atau <code>notebook</code> untuk melihat produk yang tersedia.</p>
    </section>
  <?php endif; ?>
</main>
<?php include __DIR__ . "/footer.php"; ?>

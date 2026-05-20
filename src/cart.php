<?php
require_once __DIR__ . "/data.php";
$page_title = "Keranjang";
include __DIR__ . "/header.php";

$add = $_GET["add"] ?? null;
$item = $add && isset($products[$add]) ? $products[$add] : null;

function format_rupiah(int $number): string {
    return "Rp" . number_format($number, 0, ",", ".");
}
?>
<main class="wrap page">
  <div class="page-title">
    <p class="eyebrow">Keranjang</p>
    <h1>Keranjang Belanja</h1>
    <p>Simpan pilihanmu di sini sebelum lanjut ke checkout atau kembali menambah produk lain.</p>
  </div>

  <?php if ($item): ?>
    <?php
    $subtotal = rupiah_to_number($item["price"]);
    $total = $subtotal;
    ?>
    <div class="cart-layout">
      <div>
        <div class="cart-row">
          <div class="cart-emoji"><?= htmlspecialchars($item["image"]) ?></div>
          <div>
            <h3><?= htmlspecialchars($item["name"]) ?></h3>
            <p><?= htmlspecialchars($item["price"]) ?></p>
          </div>
          <span class="pill">Ditambahkan</span>
        </div>

        <section class="panel">
          <h2>Tambahkan lagi biar lebih hemat</h2>
          <p>Lengkapi keranjang dengan item pendamping dari kategori lain supaya ongkir makin efisien dan pilihan belanjamu terasa lebih lengkap.</p>
        </section>
      </div>

      <aside class="panel cart-summary">
        <h2>Ringkasan Belanja</h2>
        <div class="summary-list">
          <div>
            <span>Subtotal</span>
            <strong><?= htmlspecialchars(format_rupiah($subtotal)) ?></strong>
          </div>
          <div>
            <span>Ongkir</span>
            <strong>Gratis</strong>
          </div>
          <div>
            <span>Diskon</span>
            <strong>0</strong>
          </div>
        </div>
        <div class="summary-total">
          <span>Total</span>
          <strong><?= htmlspecialchars(format_rupiah($total)) ?></strong>
        </div>
        <div class="hero-actions">
          <a class="btn primary" href="/promo.php">Lihat Promo</a>
          <a class="btn ghost" href="/products.php">Belanja Lagi</a>
        </div>
      </aside>
    </div>
  <?php else: ?>
    <section class="panel empty-state">
      <h2>Keranjang masih kosong</h2>
      <p>Belum ada produk yang ditambahkan. Mulai dari katalog untuk memilih barang yang paling cocok buat kebutuhanmu hari ini.</p>
      <div class="hero-actions">
        <a class="btn primary" href="/products.php">Pilih Produk</a>
        <a class="btn ghost" href="/">Kembali ke Home</a>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php include __DIR__ . "/footer.php"; ?>

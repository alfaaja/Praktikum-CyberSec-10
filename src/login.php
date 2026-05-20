<?php
$page_title = "Masuk";
$message = "";
$status_class = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if ($username === "admin" && $password === "admin123") {
        $message = "Login berhasil. Selamat datang kembali di TokoKita.";
        $status_class = "success";
    } else {
        $message = "Login gagal. Periksa kembali username atau password kamu.";
        $status_class = "danger";
    }
}

include __DIR__ . "/header.php";
?>
<main class="wrap page narrow">
  <div class="auth-card">
    <div class="auth-side">
      <div class="hero-badges">
        <span class="hero-badge">Member Area</span>
        <span class="hero-badge">Seller Center</span>
      </div>
      <p class="eyebrow">Akun TokoKita</p>
      <h1>Masuk untuk lanjut belanja atau kelola tokomu.</h1>
      <p>Satu akun untuk menyimpan produk favorit, melihat pesanan, dan membuka Seller Center kapan pun kamu butuh.</p>
      <div class="mini-grid">
        <div class="mini-stat">
          <strong>Lacak pesanan</strong>
          <span>Update status setelah checkout</span>
        </div>
        <div class="mini-stat">
          <strong>Simpan favorit</strong>
          <span>Kembali belanja tanpa cari ulang</span>
        </div>
      </div>
      <div class="mini-hint">
        <strong>Login lebih cepat</strong>
        <span>Masuk sekali untuk melanjutkan checkout dan membuka halaman seller.</span>
      </div>
    </div>

    <div class="auth-form">
      <div class="form-intro">
        <p class="eyebrow">Masuk Akun</p>
        <h2>Selamat datang kembali</h2>
        <p>Masukkan akun kamu untuk melihat pesanan, menyimpan keranjang, atau membuka Seller Center.</p>
      </div>

      <?php if ($message): ?>
        <div class="notice <?= htmlspecialchars($status_class) ?>">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <form class="form-grid" method="POST" action="/login.php">
        <label for="username">Email atau Username</label>
        <input id="username" type="text" name="username" placeholder="nama@contoh.com" autocomplete="username" required>

        <label for="password">Kata Sandi</label>
        <input id="password" type="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>

        <button type="submit">Masuk</button>
      </form>

      <p class="form-footnote">Dengan masuk, kamu bisa melanjutkan belanja, melihat promo tersimpan, dan membuka Seller Center.</p>

      <?php if ($status_class === "success"): ?>
        <div class="hero-actions">
          <a class="btn ghost" href="/admin.php">Buka Seller Center</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="command-grid two-column">
    <section class="command-card">
      <span class="chip">Keuntungan Akun</span>
      <h3>Belanja lebih cepat di kunjungan berikutnya</h3>
      <ul class="clean-list">
        <li>Simpan alamat pengiriman tanpa isi ulang setiap kali checkout.</li>
        <li>Pantau pesanan dan riwayat belanja langsung dari satu halaman.</li>
        <li>Buka Seller Center untuk memantau performa toko dan produk aktif.</li>
      </ul>
    </section>

    <section class="command-card">
      <span class="chip">Butuh Bantuan?</span>
      <h3>Tim kami siap bantu kapan saja</h3>
      <ul class="clean-list">
        <li>Live chat tersedia setiap hari untuk pertanyaan pembayaran dan pengiriman.</li>
        <li>Reset password bisa dilakukan lewat email yang terdaftar.</li>
        <li>Untuk kerja sama toko, lanjutkan ke Seller Center setelah berhasil masuk.</li>
      </ul>
    </section>
  </div>
</main>
<?php include __DIR__ . "/footer.php"; ?>

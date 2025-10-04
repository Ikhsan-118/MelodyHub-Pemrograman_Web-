<?php
session_start();

// Jika belum login, redirect ke login
if (!isset($_SESSION['username'])) {
    header("Location: login.php?msg=belum_login");
    exit();
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>MelodyHub</title>

  <!-- CSS terpisah -->
  <link rel="stylesheet" href="css/style.css">

  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
  <div class="app" role="application">
    <!-- SIDEBAR -->
    <aside class="sidebar" aria-label="Navigasi Utama">
      <div class="brand">
        <div class="logo"><img src="assets/img/1.jpeg" alt="MelodyHub logo"></div>
        <div>
          <h1>MelodyHub</h1>
          <div class="small">Web Musik Kalcer</div>
        </div>
      </div>

      <nav class="side-nav" aria-label="Menu Utama">
        <ul>
          <li><button class="nav-btn active" data-modal="home"><i class="fas fa-home"></i><span>Beranda</span></button></li>
          <li><button class="nav-btn" data-modal="browse"><i class="fas fa-compass"></i><span>Browse</span></button></li>
          <li><button class="nav-btn" data-modal="search"><i class="fas fa-search"></i><span>Cari</span></button></li>
          <li><button class="nav-btn" data-modal="playlist"><i class="fas fa-music"></i><span>Playlist</span></button></li>
          <li><button class="nav-btn" data-modal="artist"><i class="fas fa-users"></i><span>Artis</span></button></li>
        </ul>
      </nav>

      <div class="collection" aria-label="Koleksi Kamu">
        <div class="collection-header">
          <h4>Koleksi Kamu</h4>
          <button id="create-playlist" title="Buat playlist baru"><i class="fas fa-plus"></i> Buat</button>
        </div>

        <div class="collection-tabs" role="tablist">
          <button class="tab active" data-tab="playlist" role="tab" aria-selected="true">Playlist</button>
          <button class="tab" data-tab="artis" role="tab" aria-selected="false">Artis</button>
          <button class="tab" data-tab="album" role="tab" aria-selected="false">Album</button>
          <button class="tab" data-tab="podcast" role="tab" aria-selected="false">Podcast</button>
        </div>

        <div class="collection-search">
          <input id="collection-search" type="search" placeholder="Cari koleksi..." aria-label="Cari koleksi">
        </div>

        <div class="collection-list" id="collection-list" aria-live="polite">
          <!-- Isi oleh JS: playlist tersimpan / artis / album / podcast -->
        </div>
      </div>
    </aside>

    <!-- MAIN -->
    <main>
      <header class="topbar">
        <div>
          <h2>For You</h2>
          <div class="small">Rekomendasi untuk suasana kamu</div>
        </div>

        <div class="searchbox" role="search">
          <i class="fas fa-search" aria-hidden="true"></i>
          <input id="main-search" type="search" placeholder="Apa yang ingin kamu putar?" aria-label="Kolom pencarian">
        </div>
      </header>

      <section>
        <div class="section-title">
          <h3>Daily Mix & Rekomendasi</h3>
          <button class="link-btn" data-modal="browse">Tampilkan semua</button>
        </div>

        <div class="cards-row" id="reco-row" role="list" aria-label="Rekomendasi">
          <!-- contoh statis: bisa diganti oleh JS nanti -->
          <article class="card" role="listitem" data-track="peluru">
            <figure><img src="assets/img/cover-dailymix1.jpg" alt="Daily Mix"></figure>
            <h3>Daily Mix 01</h3>
            <p class="small">Barasuara • Hindia • LombaSihir</p>
          </article>

          <article class="card" role="listitem" data-track="secerca-cahaya">
            <figure><img src="assets/img/cover-dailymix2.jpg" alt=""></figure>
            <h3>Daily Mix 02</h3>
            <p class="small">The Adams • The Jansen • .feast</p>
          </article>
        </div>
      </section>

      <section style="margin-top:18px">
        <h3>Baru diputar</h3>
        <div class="grid" id="main-grid" aria-live="polite">
          <!-- item isi oleh HTML/placeholder, JS menambahkan overlay & click -->
          <article class="album" data-track="peluru"><div class="cover"><img src="assets/img/peluru.jpg" alt="Peluru"></div><h4>Peluru</h4><p class="small">Barasuara</p></article>
          <article class="album" data-track="secerca-cahaya"><div class="cover"><img src="assets/img/secerca-cahaya.jpg" alt="Secerca Cahaya"></div><h4>Secerca Cahaya</h4><p class="small">Hindia</p></article>
          <article class="album" data-track="senja-yang-rindu"><div class="cover"><img src="assets/img/senja-rindu.jpg" alt="Senja"></div><h4>Senja yang Rindu</h4><p class="small">.feast</p></article>
          <article class="album" data-track="spellbound"><div class="cover"><img src="assets/img/spellbound.jpg" alt="Spellbound"></div><h4>Spellbound</h4><p class="small">LombaSihir</p></article>
        </div>
      </section>
    </main>

    <!-- RIGHT PANEL -->
    <aside class="right" aria-label="Detail Artis">
      <div class="artist-hero">
        <h2 id="right-artist">Barasuara</h2>
        <div class="small">Band Alternatif • Indonesia</div>
      </div>

      <div class="artist-meta">
        <h4>Tentang artis</h4>
        <p class="small">Barasuara dikenal karena energi panggung dan lirik yang intens.</p>

        <h4 style="margin-top:12px">Lagu populer</h4>
        <ol class="small" id="artist-popular" style="padding-left:0;list-style:none">
          <li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.02)">Peluru</li>
          <li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.02)">Matahari</li>
        </ol>
      </div>
    </aside>

    <!-- PLAYER -->
    <div class="player" role="region" aria-label="Player">
      <div class="now-playing">
        <div class="thumb"><img id="player-thumb" src="assets/img/peluru-thumb.jpg" alt="thumbnail"></div>
        <div>
          <div class="title" id="player-title" style="font-weight:600">Peluru</div>
          <div class="artist small" id="player-artist">Barasuara</div>
        </div>
      </div>

      <div class="controls">
        <button class="shuffle" title="Shuffle"><i class="fas fa-random"></i></button>
        <button class="prev" title="Prev"><i class="fas fa-backward"></i></button>
        <button class="play-toggle" title="Play">▶</button>
        <button class="next" title="Next"><i class="fas fa-forward"></i></button>
        <button class="repeat" title="Repeat" data-mode="none"><i class="fas fa-redo"></i></button>
      </div>

      <div class="progress">
        <div class="time-current">0:00</div>
        <div class="seek" role="progressbar" aria-label="progress"><i></i></div>
        <div class="time-total">0:00</div>
      </div>
    </div>
  </div>

  <!-- single modal -->
  <div id="modal-overlay" class="modal-overlay" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal" role="document" aria-labelledby="modal-title">
      <header>
        <h3 id="modal-title">Modal</h3>
        <div>
          <button class="close" id="modal-close" aria-label="Tutup"><i class="fas fa-times"></i></button>
        </div>
      </header>
      <div id="modal-body"></div>
    </div>
  </div>

  <!-- JS terpisah -->
  <script src="js/app.js" defer></script>
</body>
</html>

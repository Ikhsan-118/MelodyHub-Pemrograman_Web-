# 🎵 MelodyHub — Status Progres Proyek

**MelodyHub** adalah website musik bertema *Web Musik Kalcer* yang dibangun menggunakan **HTML5 murni** (tanpa CSS, tanpa JavaScript, tanpa framework). README ini menjelaskan sampai di mana progres pengembangan saat ini, apa yang sudah selesai, bagian yang masih perlu dikerjakan, dan panduan singkat untuk menjalankan atau mem-publish ke GitHub Pages.

---

## 📌 Ringkasan Progres (Sekilas)
- **Status keseluruhan:** *Struktur HTML & konten dasar selesai — Siap untuk dideploy / dilanjutkan dengan CSS/JS*.
- **Perkiraan progress:** ~**70%** (struktur dan konten halaman lengkap; fitur interaktif dan styling belum diimplementasikan).

---

## ✅ Yang sudah selesai
- Pembuatan halaman HTML menggunakan elemen semantic:
  - `index.html` — Beranda (For You / Popular / Featured artists)
  - `browse.html` — Browse (genre, new releases, charts)
  - `search.html` — Halaman Cari (form statis)
  - `playlist.html` — Halaman Playlist (contoh playlist kalcer)
  - `artist.html` — Halaman profil artis
- Daftar artis diperbarui menjadi:
  - **Hindia**, **.feast**, **LombaSihir**, **Barasuara**, **The Adams**, **The Jansen**
- Penggunaan elemen semantic dan aksesibilitas dasar:
  - `<header>`, `<nav>`, `<aside>`, `<main>`, `<section>`, `<article>`, `<footer>`
  - `aria-label` dan `id` untuk pengenal seksi penting
- Elemen `<audio>` sudah ditempatkan sebagai placeholder (komentar contoh `<source>` disediakan).
- README awal (dokumen ini melengkapi dengan status progres).

---

## 🔜 Yang sedang / sebaiknya dikerjakan (Next steps)
1. **Deploy ke GitHub Pages**  
   - Jika kamu belum upload: buat repo (mis. `melodyhub`), commit semua file, lalu aktifkan Pages di *Settings → Pages* (branch `main`, folder `/root`).  
   - Setelah aktif, alamat akan: `https://username.github.io/melodyhub/` (ganti `username`).
2. **Tambahkan folder `assets/`** (opsional sekarang):  
   - `assets/audio/` → untuk menaruh file mp3 contoh (perhatikan lisensi).  
   - `assets/img/` → cover/thumbnail album (untuk nanti styling).
3. **(Opsional tapi disarankan) Tambah CSS**  
   - Bikin file `styles.css` dan link di `<head>` untuk membuat layout mirip Spotify (sidebar + konten grid).
4. **(Opsional) Tambah JavaScript**  
   - Interaktif: kontrol player (`<audio>`), playlist dynamic, form search live.
5. **Jika ingin streaming nyata**  
   - Butuh backend (server + storage) dan lisensi musik. Ini untuk pengembangan lanjutan.

---

## ⚠️ Batasan & Known Issues saat ini
- **Tidak ada CSS**: tampilan masih polos (plain HTML). Struktur sudah siap untuk distyling.
- **Tidak ada JS**: form pencarian bersifat statis (tidak mengembalikan hasil dinamis). Kontrol player bergantung native browser (`<audio>`).
- **Audio**: elemen `<audio>` hanya berisi placeholder — file audio belum disertakan di repo.

---

## 🗂️ Struktur file saat ini
/ (root)
├─ index.html
├─ browse.html
├─ search.html
├─ playlist.html
├─ artist.html
└─ README.md 

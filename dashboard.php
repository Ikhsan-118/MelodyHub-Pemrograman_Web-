<?php
// dashboard.php
session_start();

// akses hanya untuk user yang sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php?msg=belum_login");
    exit();
}

require_once 'koneksi.php'; // pastikan file ini ada

// pesan notifikasi sederhana
$notice = '';

// HANDLE CREATE / UPDATE (method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // gunakan action untuk membedakan create/update
    $action = $_POST['action'] ?? 'create';

    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $artist = trim($_POST['artist'] ?? '');
        $album = trim($_POST['album'] ?? '');
        $cover = trim($_POST['cover'] ?? '');
        $audio_src = trim($_POST['audio_src'] ?? '');
        $duration = trim($_POST['duration'] ?? '');

        if ($title === '' || $artist === '') {
            $notice = "Judul dan artis harus diisi.";
        } else {
            $stmt = $conn->prepare("INSERT INTO tracks (title, artist, album, cover, audio_src, duration) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssss', $title, $artist, $album, $cover, $audio_src, $duration);
            if ($stmt->execute()) {
                $notice = "Lagu berhasil ditambahkan.";
            } else {
                $notice = "Gagal menambahkan lagu: " . $stmt->error;
            }
            $stmt->close();
        }
    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $artist = trim($_POST['artist'] ?? '');
        $album = trim($_POST['album'] ?? '');
        $cover = trim($_POST['cover'] ?? '');
        $audio_src = trim($_POST['audio_src'] ?? '');
        $duration = trim($_POST['duration'] ?? '');

        if ($id <= 0 || $title === '' || $artist === '') {
            $notice = "Data tidak lengkap untuk update.";
        } else {
            $stmt = $conn->prepare("UPDATE tracks SET title=?, artist=?, album=?, cover=?, audio_src=?, duration=? WHERE id=?");
            $stmt->bind_param('ssssssi', $title, $artist, $album, $cover, $audio_src, $duration, $id);
            if ($stmt->execute()) {
                $notice = "Lagu berhasil diupdate.";
            } else {
                $notice = "Gagal update: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// HANDLE DELETE via GET -> ?delete=ID
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    if ($delId > 0) {
        $stmt = $conn->prepare("DELETE FROM tracks WHERE id = ?");
        $stmt->bind_param('i', $delId);
        if ($stmt->execute()) {
            // redirect agar refresh tidak mengulang delete
            header("Location: dashboard.php?msg=deleted");
            exit();
        } else {
            $notice = "Gagal menghapus: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Jika edit via GET -> ?edit=ID maka ambil data untuk ditampilkan di form
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    if ($eid > 0) {
        $stmt = $conn->prepare("SELECT id, title, artist, album, cover, audio_src, duration FROM tracks WHERE id = ?");
        $stmt->bind_param('i', $eid);
        $stmt->execute();
        $res = $stmt->get_result();
        $editData = $res->fetch_assoc();
        if ($editData) { $editMode = true; }
        $stmt->close();
    }
}

// ambil semua tracks untuk listing
$tracks = [];
$res = $conn->query("SELECT id, title, artist, album, cover, audio_src, duration, created_at FROM tracks ORDER BY created_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $tracks[] = $row;
    }
    $res->close();
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard - MelodyHub</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* CSS sederhana untuk dashboard (agar rapi) */
    body{font-family:Arial,Helvetica,sans-serif;background:#f5f7fb;color:#0b1220;margin:0;padding:0}
    .wrap{max-width:1100px;margin:28px auto;padding:18px}
    header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
    header h1{margin:0;font-size:22px}
    .user{font-size:14px;color:#333}
    .notice{padding:10px;border-radius:8px;background:#e6ffea;color:#0a6b25;margin-bottom:12px}
    .warn{padding:10px;border-radius:8px;background:#ffdede;color:#8b1a1a;margin-bottom:12px}
    .grid{display:grid;grid-template-columns:1fr 420px;gap:18px;align-items:start}
    .card{background:#fff;border-radius:10px;padding:14px;box-shadow:0 6px 18px rgba(20,30,40,0.06)}
    table{width:100%;border-collapse:collapse}
    table th, table td{padding:8px;text-align:left;border-bottom:1px solid #eef2f6;font-size:14px}
    .actions a{margin-right:8px;color:#0073e6;text-decoration:none}
    .form-row{display:flex;flex-direction:column;gap:8px}
    input[type=text], textarea{padding:8px;border:1px solid #d7e0ea;border-radius:6px}
    button{background:#1DB954;color:#fff;border:0;padding:10px 12px;border-radius:6px;cursor:pointer}
    .btn-danger{background:#e05b5b}
    .small{font-size:13px;color:#66788a}
    .top-links a{margin-left:12px;color:#666;text-decoration:none}
  </style>
</head>
<body>
  <div class="wrap">
    <header>
      <h1>Dashboard MelodyHub</h1>
      <div class="user">
        Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
        <span class="top-links"><a href="index.php">Home</a> | <a href="logout.php">Logout</a></span>
      </div>
    </header>

    <?php if (!empty($notice)): ?>
      <div class="notice"><?= htmlspecialchars($notice) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
      <div class="notice">Data lagu berhasil dihapus.</div>
    <?php endif; ?>

    <div class="grid">
      <!-- LEFT: table list -->
      <div class="card">
        <h3>Daftar Lagu</h3>
        <p class="small">List semua lagu di database. Gunakan tombol Edit atau Delete untuk mengelola.</p>

        <?php if (count($tracks) === 0): ?>
          <p class="small">Belum ada lagu.</p>
        <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>Judul</th>
                <th>Artis</th>
                <th>Album</th>
                <th>Durasi</th>
                <th>Dibuat</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($tracks as $t): ?>
                <tr>
                  <td><?= htmlspecialchars($t['title']) ?></td>
                  <td><?= htmlspecialchars($t['artist']) ?></td>
                  <td><?= htmlspecialchars($t['album']) ?></td>
                  <td><?= htmlspecialchars($t['duration']) ?></td>
                  <td class="small"><?= htmlspecialchars($t['created_at']) ?></td>
                  <td class="actions">
                    <a href="dashboard.php?edit=<?= $t['id'] ?>">Edit</a>
                    <a href="dashboard.php?delete=<?= $t['id'] ?>" onclick="return confirm('Hapus lagu ini?')">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

      <!-- RIGHT: form add / edit -->
      <div class="card">
        <h3><?= $editMode ? "Edit Lagu" : "Tambah Lagu Baru" ?></h3>
        <form method="POST" action="dashboard.php" class="form-row">
          <input type="hidden" name="action" value="<?= $editMode ? 'update' : 'create' ?>">
          <?php if ($editMode): ?>
            <input type="hidden" name="id" value="<?= (int)$editData['id'] ?>">
          <?php endif; ?>

          <label>Judul</label>
          <input type="text" name="title" value="<?= $editMode ? htmlspecialchars($editData['title']) : '' ?>" required>

          <label>Artis</label>
          <input type="text" name="artist" value="<?= $editMode ? htmlspecialchars($editData['artist']) : '' ?>" required>

          <label>Album</label>
          <input type="text" name="album" value="<?= $editMode ? htmlspecialchars($editData['album']) : '' ?>">

          <label>Cover (path)</label>
          <input type="text" name="cover" value="<?= $editMode ? htmlspecialchars($editData['cover']) : '' ?>" placeholder="assets/img/cover.jpg">

          <label>Audio (path)</label>
          <input type="text" name="audio_src" value="<?= $editMode ? htmlspecialchars($editData['audio_src']) : '' ?>" placeholder="assets/audio/file.mp3">

          <label>Durasi (ex: 3:45)</label>
          <input type="text" name="duration" value="<?= $editMode ? htmlspecialchars($editData['duration']) : '' ?>">

          <div style="display:flex;gap:8px;margin-top:12px">
            <button type="submit"><?= $editMode ? 'Update' : 'Tambahkan' ?></button>
            <?php if ($editMode): ?>
              <a href="dashboard.php" style="display:inline-block;padding:10px 12px;border-radius:6px;background:#ddd;color:#333;text-decoration:none">Batal</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <footer style="margin-top:18px;color:#6b7b88;font-size:13px">
      <p>Catatan: file koneksi: <code>koneksi.php</code>. Untuk menambahkan user baru silakan import <code>melodyhub.sql</code> atau tambahkan manual ke tabel <code>users</code>.</p>
    </footer>
  </div>
</body>
</html>

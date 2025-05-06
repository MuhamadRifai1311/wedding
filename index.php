<?php
$file = "ucapan.txt";

// Saat form dikirim, simpan data ke file
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nama']) && isset($_POST['isi'])) {
  $nama = htmlspecialchars($_POST['nama']);
  $isi = htmlspecialchars($_POST['isi']);
  $baris = $nama . "|" . $isi . "|" . date("Y-m-d H:i:s") . "\n";

  // Baca file dan simpan data baru di bagian paling atas
  $currentContent = file_get_contents($file);
  $newContent = $baris . $currentContent;
  file_put_contents($file, $newContent);

  // Kirim response
  echo json_encode([
    'status' => 'success',
    'message' => 'Ucapan berhasil dikirim',
  ]);
  exit();
}

// Baca isi file ucapan
$ucapan = [];
if (file_exists($file)) {
  $lines = file($file, FILE_IGNORE_NEW_LINES);
  foreach ($lines as $line) {
    $parts = explode("|", $line);
    if (count($parts) === 3) {
      list($nama, $isi, $tanggal) = $parts;
      $ucapan[] = [
        'nama' => $nama,
        'isi' => $isi,
        'tanggal' => $tanggal
      ];
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Undangan Pernikahan | Dinda & Rian</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-pink-50 font-sans text-gray-700">

<!-- Hero Section -->
<section class="min-h-screen flex flex-col items-center justify-center bg-pink-200 text-center px-4">
  <h1 class="text-4xl md:text-6xl font-bold mb-2">Dinda & Rian</h1>
  <?php
    if(isset($_GET['nama']) && !empty($_GET['nama'])){
      $nama = htmlspecialchars($_GET['nama']);
      echo "<p>Yth. Kepada <strong>$nama</strong></p>";
    } else {
      echo "<p>Yth. Kepada <strong>Tamu Undangan</strong></p>";
    }
  ?>
  <p class="text-xl mb-6">Sabtu, 10 Mei 2025</p>
  <button 
    onclick="document.getElementById('invitation').scrollIntoView({ behavior: 'smooth' })"
    class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-full transition"
  >
    Buka Undangan
  </button>
</section>

<!-- Invitation Content -->
<section id="invitation" class="py-16 px-6 bg-white text-center">
  <h2 class="text-2xl md:text-3xl font-semibold mb-4">Assalamu’alaikum Warahmatullahi Wabarakatuh</h2>
  <p class="max-w-xl mx-auto mb-8">
    Dengan penuh rasa syukur, kami mengundang Anda untuk hadir pada acara pernikahan kami.
  </p>

  <div class="max-w-2xl mx-auto text-left space-y-8">
    <div>
      <h3 class="text-xl font-bold text-pink-600">Akad Nikah</h3>
      <p>10 Mei 2025, Pukul 09:00 WIB</p>
      <p>Gedung Serbaguna Bahagia, Bandung</p>
    </div>
    <div>
      <h3 class="text-xl font-bold text-pink-600">Resepsi</h3>
      <p>10 Mei 2025, Pukul 11:00 - 14:00 WIB</p>
      <p>Gedung Serbaguna Bahagia, Bandung</p>
    </div>
  </div>

  <p class="italic mt-12 text-sm text-gray-500">
    Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir.
  </p>
</section>

<!-- Form Ucapan -->
<section class="bg-white py-12 px-6 text-center">
  <h2 class="text-2xl font-semibold mb-4">Kirim Ucapan</h2>
  <form id="ucapanForm" class="max-w-md mx-auto space-y-4">
    <input type="text" name="nama" required placeholder="Nama Anda" class="w-full p-2 border rounded" />
    <textarea name="isi" required placeholder="Ucapan" class="w-full p-2 border rounded h-28"></textarea>
    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded">Kirim</button>
  </form>
</section>

<!-- Daftar Ucapan -->
<section class="bg-pink-50 py-12 px-6">
  <h2 class="text-2xl font-semibold text-center mb-6">Ucapan dari Tamu</h2>
  <div id="ucapanList" class="max-w-3xl mx-auto space-y-4">
    <?php foreach ($ucapan as $u): ?>
      <div class="bg-white p-4 rounded shadow">
        <strong><?= htmlspecialchars($u['nama']) ?></strong>
        <p><?= nl2br(htmlspecialchars($u['isi'])) ?></p>
        <p class="text-xs text-gray-400"><?= date("d M Y H:i", strtotime($u['tanggal'])) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<script>
$(document).ready(function() {
  // Handle form submission via AJAX
  $('#ucapanForm').submit(function(e) {
    e.preventDefault();
    
    var nama = $('input[name="nama"]').val();
    var isi = $('textarea[name="isi"]').val();

    $.ajax({
      url: '',
      type: 'POST',
      data: {
        nama: nama,
        isi: isi
      },
      success: function(response) {
        var data = JSON.parse(response);
        if (data.status === 'success') {
          // Tambahkan ucapan baru ke atas daftar ucapan
          var newUcapan = '<div class="bg-white p-4 rounded shadow">';
          newUcapan += '<strong>' + nama + '</strong>';
          newUcapan += '<p>' + isi + '</p>';
          newUcapan += '<p class="text-xs text-gray-400">' + new Date().toLocaleString() + '</p>';
          newUcapan += '</div>';
          
          $('#ucapanList').prepend(newUcapan);
          
          // Kosongkan form setelah kirim
          $('input[name="nama"]').val('');
          $('textarea[name="isi"]').val('');
        }
      }
    });
  });
});
</script>

</body>
</html>

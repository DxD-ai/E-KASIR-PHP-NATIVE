<?php
// Menjalankan session
session_start();

// Menghapus semua data session yang ada
session_unset();

// Menghancurkan session
session_destroy();

// Mengarahkan user kembali ke halaman login (sesuaikan nama filenya, misal index.php atau login.php)
echo "<script>
    alert('Anda telah berhasil logout');
    window.location.href='index.php'; 
</script>";
exit;
?>
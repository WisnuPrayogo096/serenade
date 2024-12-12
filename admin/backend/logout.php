<?php
session_start();
session_unset();
session_destroy();
?>

<script>
alert("Anda telah berhasil keluar. Terima kasih telah berkunjung!");
window.location.href = "../index.php";
</script>
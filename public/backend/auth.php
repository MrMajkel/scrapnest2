<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

// Blokowanie cache (ważne przy cofnięciu)
header("Expires: 0");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

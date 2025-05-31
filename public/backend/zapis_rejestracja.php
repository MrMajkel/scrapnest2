<?php
require 'db.php';

$imie = $_POST['imie'] ?? '';
$nazwisko = $_POST['nazwisko'] ?? '';
$email = $_POST['email'] ?? '';
$haslo = $_POST['haslo'] ?? '';
$powtorzhaslo = $_POST['powtorzhaslo'] ?? '';
$rola = $_POST['rola'] ?? '';

if (!$imie || !$nazwisko || !$email || !$haslo || !$powtorzhaslo || !$rola) {
    header("Location: ../rejestracja.php?error=puste");
    exit;
}

if ($haslo !== $powtorzhaslo) {
    header("Location: ../rejestracja.php?error=hasla");
    exit;
}

$sprawdz = pg_query_params($conn, "SELECT 1 FROM users WHERE email = $1", [$email]);
if (pg_num_rows($sprawdz) > 0) {
    header("Location: ../rejestracja.php?error=email");
    exit;
}

$hashed = password_hash($haslo, PASSWORD_DEFAULT);
pg_query_params($conn,
    "INSERT INTO users (imie, nazwisko, email, haslo, rola) VALUES ($1, $2, $3, $4, $5)",
    [$imie, $nazwisko, $email, $hashed, $rola]
);

header("Location: ../logowanie.php?zarejestrowano=1");
exit;
?>

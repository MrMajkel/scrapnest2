<?php
session_start();
require 'db.php';

if (empty($_POST['email']) || empty($_POST['haslo'])) {
    header("Location: ../index.php?error=1");
    exit;
}

$email = trim($_POST['email']);
$haslo = $_POST['haslo'];

$result = pg_query_params($conn, "SELECT id, email, haslo, imie, nazwisko, rola FROM users WHERE email = $1", [$email]);
$user = pg_fetch_assoc($result);

if (!$user || !password_verify($haslo, $user['haslo'])) {
    header("Location: ../index.php?error=1");
    exit;
}

$_SESSION['user'] = [
    'id' => $user['id'],
    'email' => $user['email'],
    'imie' => $user['imie'],
    'nazwisko' => $user['nazwisko'],
    'rola' => $user['rola']
];

header("Location: ../panel.php");
exit;
?>

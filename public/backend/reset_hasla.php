<?php
require 'db.php';

$email = $_POST['email'] ?? '';
if (!$email) {
    echo "<div class='error-message'>Nie podano adresu e-mail.</div>";
    exit;
}

$result = pg_query_params($conn, "SELECT * FROM users WHERE email = $1", [$email]);
$user = pg_fetch_assoc($result);

if ($user) {
    $noweHaslo = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
    $hashed = password_hash($noweHaslo, PASSWORD_DEFAULT);

    pg_query_params($conn, "UPDATE users SET haslo = $1 WHERE email = $2", [$hashed, $email]);

    echo "<div class='success-message'>Nowe hasło: $noweHaslo</div>";
} else {
    echo "<div class='error-message'>Nie znaleziono konta </br>z tym adresem e-mail</div>";
}
?>

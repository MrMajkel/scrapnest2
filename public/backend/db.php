<?php
$host = 'db';
$dbname = 'scrapnest_db';
$user = 'user';
$password = 'pass';

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Błąd połączenia z bazą danych PostgreSQL.");
}
?>

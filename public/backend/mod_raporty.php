<?php
session_start();
require 'db.php';
header('Content-Type: application/json');
header("Content-Type: text/html; charset=UTF-8");

$data = $_GET['data'] ?? date('Y-m-d');
$format = $_GET['format'] ?? 'html';

$escaped = pg_escape_literal($conn, $data);
$query = "SELECT * FROM dzienny_raport_metali WHERE data = $escaped";
$result = pg_query($conn, $query);

$rows = [];
while ($row = pg_fetch_assoc($result)) {
    $rows[] = $row;
}

if ($format === 'csv') {
    header("Content-Disposition: attachment; filename=raport_dzienny_$data.csv");

    echo "\xEF\xBB\xBF";

    echo "Data;Metal;Zakup;Sprzedaż;Różnica\n";
    foreach ($rows as $r) {
        echo "$data;{$r['metal']};{$r['suma_zakupow']};{$r['suma_sprzedazy']};{$r['roznica_sprzedaz_zakup']}\n";
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Raport dzienny <?= htmlspecialchars($data) ?></title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 8px; text-align: center; }
    th { background-color: #f0f0f0; }
  </style>
</head>
<body>
  <h2>Raport dzienny - <?= htmlspecialchars($data) ?></h2>
  <table>
    <tr>
      <th>Metal</th>
      <th>Suma zakupów</th>
      <th>Suma sprzedaży</th>
      <th>Różnica</th>
    </tr>
    <?php if (empty($rows)): ?>
      <tr><td colspan="4">Brak danych dla wybranego dnia.</td></tr>
    <?php else: ?>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['metal']) ?></td>
          <td><?= htmlspecialchars($row['suma_zakupow']) ?></td>
          <td><?= htmlspecialchars($row['suma_sprzedazy']) ?></td>
          <td><?= htmlspecialchars($row['roznica_sprzedaz_zakup']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>
  <br>
  <button onclick="window.print()">Drukuj lub zapisz jako PDF</button>
</body>
</html>

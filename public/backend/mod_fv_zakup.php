<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function insertPozycjeFaktury($conn, $fakturaId, $metalStr, $wagaStr) {
    $metale = array_map('trim', explode("\n", $metalStr));
    $wagiSurowe = array_map('trim', explode("\n", $wagaStr));

    for ($i = 0; $i < max(count($metale), count($wagiSurowe)); $i++) {
        $metal = $metale[$i] ?? '';
        $wagaRaw = str_replace(',', '.', $wagiSurowe[$i] ?? '');

        if ($metal === '' || $wagaRaw === '' || !is_numeric($wagaRaw)) continue;

        $waga = floatval($wagaRaw);
        $ok = pg_query_params($conn,
            "INSERT INTO pozycje_faktury_zakupowe (faktura_id, metal, waga) VALUES ($1, $2, $3)",
            [$fakturaId, $metal, $waga]
        );
        if (!$ok) return false;
    }

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $limit = (int)($_GET['limit'] ?? 20);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $res = pg_query_params($conn, "
        SELECT f.id, f.nr_faktury AS numer, f.data, f.firma,
            STRING_AGG(p.metal, E'\n' ORDER BY p.id) AS metal,
            STRING_AGG(p.waga::TEXT, E'\n' ORDER BY p.id) AS waga
        FROM faktury_zakupowe f
        LEFT JOIN pozycje_faktury_zakupowe p ON f.id = p.faktura_id
        GROUP BY f.id, f.nr_faktury, f.data, f.firma
        ORDER BY f.id DESC
        LIMIT $1 OFFSET $2
    ", [$limit, $offset]);
    
    if (!$res) respond(['success' => false, 'error' => 'Błąd bazy danych: ' . pg_last_error($conn)], 500);
    respond(pg_fetch_all($res) ?: []);    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'create') {
        pg_query($conn, "BEGIN");

        $res1 = pg_query_params($conn,
            "INSERT INTO faktury_zakupowe (nr_faktury, data, firma) VALUES ($1, $2, $3) RETURNING id",
            [$_POST['numer'], $_POST['data'], $_POST['firma']]
        );

        if (!$res1) {
            pg_query($conn, "ROLLBACK");
            respond(['success' => false, 'error' => 'Błąd tworzenia faktury'], 500);
        }

        $newId = pg_fetch_result($res1, 0, 0);
        if (!insertPozycjeFaktury($conn, $newId, $_POST['metal'], $_POST['waga'])) {
            pg_query($conn, "ROLLBACK");
            respond(['success' => false, 'error' => 'Błąd dodawania pozycji'], 500);
        }

        pg_query($conn, "COMMIT");
        respond(['success' => true]);
    }

    if ($action === 'update') {
        pg_query($conn, "BEGIN");

        $res = pg_query_params($conn,
            "UPDATE faktury_zakupowe SET nr_faktury = $1, data = $2, firma = $3 WHERE id = $4",
            [$_POST['numer'], $_POST['data'], $_POST['firma'], $_POST['id']]
        );

        if (!$res) {
            pg_query($conn, "ROLLBACK");
            respond(['success' => false, 'error' => 'Błąd aktualizacji faktury'], 500);
        }

        pg_query_params($conn, "DELETE FROM pozycje_faktury_zakupowe WHERE faktura_id = $1", [$_POST['id']]);

        if (!insertPozycjeFaktury($conn, $_POST['id'], $_POST['metal'], $_POST['waga'])) {
            pg_query($conn, "ROLLBACK");
            respond(['success' => false, 'error' => 'Błąd dodawania pozycji'], 500);
        }

        pg_query($conn, "COMMIT");
        respond(['success' => true]);
    }

    if ($action === 'delete') {
        $res = pg_query_params($conn, "DELETE FROM faktury_zakupowe WHERE id = $1", [$_POST['id']]);
        if (!$res) respond(['success' => false, 'error' => 'Błąd usuwania'], 500);
        respond(['success' => true]);
    }

    respond(['success' => false, 'error' => 'Nieznana akcja'], 400);
}

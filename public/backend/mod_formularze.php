<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function insertPozycje($conn, $formularzId, $metalStr, $wagaStr) {
    $metale = array_map('trim', explode("\n", $metalStr));
    $wagiSurowe = array_map('trim', explode("\n", $wagaStr));

    for ($i = 0; $i < max(count($metale), count($wagiSurowe)); $i++) {
        $metal = $metale[$i] ?? '';
        $wagaRaw = str_replace(',', '.', $wagiSurowe[$i] ?? '');

        if ($metal === '' || $wagaRaw === '' || !is_numeric($wagaRaw)) continue;

        $waga = floatval($wagaRaw);
        $ok = pg_query_params($conn,
            "INSERT INTO pozycje_formularza (formularz_id, metal, waga) VALUES ($1, $2, $3)",
            [$formularzId, $metal, $waga]
        );
        if (!$ok) return false;
    }

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

    $res = pg_query_params($conn, "
        SELECT f.id, f.nr_formularza AS numer, f.data,
               COALESCE(string_agg(p.metal, E'\n' ORDER BY p.id), '') AS metal,
               COALESCE(string_agg(p.waga::text, E'\n' ORDER BY p.id), '') AS waga
        FROM formularze f
        LEFT JOIN pozycje_formularza p ON f.id = p.formularz_id
        GROUP BY f.id
        ORDER BY f.id DESC
        LIMIT $1 OFFSET $2
    ", [$limit, $offset]);

    if (!$res) respond(['success' => false, 'error' => 'Błąd bazy danych'], 500);
    respond(pg_fetch_all($res) ?: []);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'create') {
        pg_query($conn, "BEGIN");

        $res1 = pg_query_params($conn,
            "INSERT INTO formularze (nr_formularza, data) VALUES ($1, $2) RETURNING id",
            [$_POST['numer'], $_POST['data']]
        );

        if (!$res1) {
            pg_query($conn, "ROLLBACK");
            respond(['success' => false, 'error' => 'Błąd tworzenia formularza'], 500);
        }

        $newId = pg_fetch_result($res1, 0, 0);
        if (!insertPozycje($conn, $newId, $_POST['metal'], $_POST['waga'])) {
            pg_query($conn, "ROLLBACK");
            respond(['success' => false, 'error' => 'Błąd dodawania pozycji'], 500);
        }

        pg_query($conn, "COMMIT");
        respond(['success' => true]);
    }

    if ($action === 'update') {
        pg_query($conn, "BEGIN");

        $res = pg_query_params($conn,
            "UPDATE formularze SET nr_formularza = $1, data = $2 WHERE id = $3",
            [$_POST['numer'], $_POST['data'], $_POST['id']]
        );

        if (!$res) {
            pg_query($conn, "ROLLBACK");
            respond(['success' => false, 'error' => 'Błąd aktualizacji formularza'], 500);
        }

        pg_query_params($conn, "DELETE FROM pozycje_formularza WHERE formularz_id = $1", [$_POST['id']]);

        if (!insertPozycje($conn, $_POST['id'], $_POST['metal'], $_POST['waga'])) {
            pg_query($conn, "ROLLBACK");
            respond(['success' => false, 'error' => 'Błąd dodawania pozycji'], 500);
        }

        pg_query($conn, "COMMIT");
        respond(['success' => true]);
    }

    if ($action === 'delete') {
        $res = pg_query_params($conn, "DELETE FROM formularze WHERE id = $1", [$_POST['id']]);
        if (!$res) respond(['success' => false, 'error' => 'Błąd usuwania'], 500);
        respond(['success' => true]);
    }

    respond(['success' => false, 'error' => 'Nieznana akcja'], 400);
}

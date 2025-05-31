<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

    $res = pg_query_params($conn,
        "SELECT * FROM kontrahenci ORDER BY id DESC LIMIT $1 OFFSET $2",
        [$limit, $offset]
    );

    echo json_encode(pg_fetch_all($res) ?: []);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'create') {
        $res = pg_query_params($conn,
            "INSERT INTO kontrahenci (nazwa_firmy, bdo, nip, adres, telefon, mail) VALUES ($1, $2, $3, $4, $5, $6)",
            [$_POST['nazwa_firmy'], $_POST['bdo'], $_POST['nip'], $_POST['adres'], $_POST['telefon'], $_POST['mail']]
        );
        echo json_encode(['success' => $res !== false]);
        exit;
    }    

    if ($action === 'update') {
        $res = pg_query_params($conn,
            "UPDATE kontrahenci SET nazwa_firmy=$1, bdo=$2, nip=$3, adres=$4, telefon=$5, mail=$6 WHERE id=$7",
            [$_POST['nazwa_firmy'], $_POST['bdo'], $_POST['nip'], $_POST['adres'], $_POST['telefon'], $_POST['mail'], $_POST['id']]
        );
        echo json_encode(['success' => $res !== false]);
        exit;
    }

    if ($action === 'delete') {
        $res = pg_query_params($conn, "DELETE FROM kontrahenci WHERE id=$1", [$_POST['id']]);
        echo json_encode(['success' => $res !== false]);
        exit;
    }
}
?>

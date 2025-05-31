<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['rola'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Brak dostępu']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

    $res = pg_query_params($conn,
        "SELECT id, imie, nazwisko, email, rola FROM users ORDER BY id DESC LIMIT $1 OFFSET $2",
        [$limit, $offset]
    );

    echo json_encode(pg_fetch_all($res) ?: []);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'create') {
        $haslo = password_hash($_POST['haslo'], PASSWORD_DEFAULT);
        $res = pg_query_params($conn,
            "INSERT INTO users (imie, nazwisko, email, haslo, rola) VALUES ($1, $2, $3, $4, $5)",
            [$_POST['imie'], $_POST['nazwisko'], $_POST['email'], $haslo, $_POST['rola']]
        );
        echo json_encode(['success' => $res !== false]);
        exit;
    }

    if ($action === 'update') {
        $res = pg_query_params($conn,
            "UPDATE users SET imie=$1, nazwisko=$2, email=$3, rola=$4 WHERE id=$5",
            [$_POST['imie'], $_POST['nazwisko'], $_POST['email'], $_POST['rola'], $_POST['id']]
        );
        echo json_encode(['success' => $res !== false]);
        exit;
    }

    if ($action === 'delete') {
        $res = pg_query_params($conn, "DELETE FROM users WHERE id=$1", [$_POST['id']]);
        echo json_encode(['success' => $res !== false]);
        exit;
    }
}
?>

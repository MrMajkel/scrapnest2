<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $typ = $_GET['typ'] ?? '';

    if ($typ === 'masa') {
        $res = pg_query($conn, "SELECT suma_wag FROM calkowita_masa");
        echo json_encode(pg_fetch_assoc($res) ?: []);
        exit;
    } elseif ($typ === 'iloscmetali') {
        $res = pg_query($conn, "SELECT liczba_metali FROM laczna_ilosc_metali");
        echo json_encode(pg_fetch_assoc($res) ?: []);
        exit;
    } elseif ($typ === 'iloscodbiorcow') {
        $res = pg_query($conn, "SELECT liczba FROM liczba_odbiorcow");
        echo json_encode(pg_fetch_assoc($res) ?: []);
        exit;
    } elseif ($typ === 'iloscformularzy') {
        $res = pg_query($conn, "SELECT liczba FROM liczba_formularzy");
        echo json_encode(pg_fetch_assoc($res) ?: []);
        exit;
    } elseif ($typ === 'magazyn') {
        $res = pg_query($conn, "SELECT * FROM stan_magazynowy_biezacy");
        $rows = [];
        while ($row = pg_fetch_assoc($res)) {
            $rows[] = $row;
        }
        echo json_encode($rows);
        exit;    
    
    } elseif ($typ === 'ostatnie_sprzedaze') {
        $res = pg_query($conn, "
            SELECT f.id, f.data, f.firma,
                STRING_AGG(p.metal, E'\n' ORDER BY p.id) AS metal,
                STRING_AGG(p.waga::TEXT, E'\n' ORDER BY p.id) AS waga
            FROM faktury_sprzedazowe f
            LEFT JOIN pozycje_faktury_sprzedazowe p ON f.id = p.faktura_id
            GROUP BY f.id, f.data, f.firma
            ORDER BY f.id DESC
            LIMIT 5
        ");
        echo json_encode(pg_fetch_all($res) ?: []);
        exit;
    } else {
        echo json_encode(['error' => 'Nieznany typ zapytania']);
        http_response_code(400);
        exit;
    }
}

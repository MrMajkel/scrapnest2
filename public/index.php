<?php

require_once __DIR__.'/../Routing.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

// Rejestracja tras
Routing::get('index', 'DefaultController');
Routing::get('formularze', 'DefaultController');
Routing::get('fv_sprzedaz', 'DefaultController');
Routing::get('fv_zakup', 'DefaultController');
Routing::get('kontrahenci', 'DefaultController');
Routing::get('logowanie', 'DefaultController');
Routing::get('panel', 'DefaultController');
Routing::get('raporty', 'DefaultController');
Routing::get('rejestracja', 'DefaultController');
Routing::get('reset_hasla', 'DefaultController');
Routing::get('uzytkownicy', 'DefaultController');

Routing::run($path);

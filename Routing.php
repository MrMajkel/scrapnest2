<?php

require_once __DIR__.'/src/controllers/DefaultController.php';

class Routing {
    public static $routes = [];

    public static function get($url, $controller) {
        self::$routes[$url] = $controller;
    }

    public static function run($url) {
        $action = explode("/", $url)[0];

        if (!array_key_exists($action, self::$routes)) {
            http_response_code(404);
            die("404 - Page not found");
        }

        $controller = self::$routes[$action];
        $object = new $controller;

        if (!method_exists($object, $action)) {
            http_response_code(404);
            die("Method $action not found in controller $controller");
        }

        $object->$action();
    }
}

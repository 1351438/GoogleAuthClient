<?php
global $json, $router;

$router->both("/home", function () {
    require_once __DIR__ . "/actions/home.php";
});

$router->both("/new", function () {
    require_once __DIR__ . "/actions/new_code.php";
});
$router->both("/settings", function () {
    require_once __DIR__ . "/actions/settings.php";
});
$router->both("/php", function () {
    require_once __DIR__ . "/actions/phpinfo.php";
});
?>
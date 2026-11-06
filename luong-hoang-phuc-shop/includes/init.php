<?php
declare(strict_types=1);
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/functions.php";
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        "httponly" => true,
        "secure" => !empty($_SERVER["HTTPS"]),
        "samesite" => "Lax",
    ]);
    session_start();
}

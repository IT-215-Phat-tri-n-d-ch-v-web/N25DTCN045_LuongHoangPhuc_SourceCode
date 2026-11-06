<?php require_once __DIR__ . "/includes/init.php";
$_SESSION = [];
session_regenerate_id(true);
flash("Bạn đã đăng xuất.");
redirect("index.php");

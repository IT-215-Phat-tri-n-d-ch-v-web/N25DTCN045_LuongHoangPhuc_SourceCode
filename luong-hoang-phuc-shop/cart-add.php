<?php require_once __DIR__ . "/includes/init.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("index.php");
}
verify_csrf();
$id = (int) ($_POST["product_id"] ?? 0);
$qty = max(1, (int) ($_POST["quantity"] ?? 1));
$s = db()->prepare("SELECT id,stock FROM products WHERE id=? AND is_active=1");
$s->execute([$id]);
$p = $s->fetch();
if (!$p || $p["stock"] < $qty) {
    flash("Sản phẩm không đủ tồn kho.", "error");
    redirect("index.php");
}
$_SESSION["cart"][$id] = min(
    (int) ($_SESSION["cart"][$id] ?? 0) + $qty,
    (int) $p["stock"],
);
flash("Đã thêm sản phẩm vào giỏ hàng.");
redirect("cart.php");

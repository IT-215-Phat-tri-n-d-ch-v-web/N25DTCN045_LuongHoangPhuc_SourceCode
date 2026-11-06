<?php require_once __DIR__ . "/includes/init.php";
$page_title = "Giỏ hàng";
verify_csrf();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($_POST["qty"] ?? [] as $id => $qty) {
        $id = (int) $id;
        $qty = (int) $qty;
        if ($qty <= 0) {
            unset($_SESSION["cart"][$id]);
        } else {
            $_SESSION["cart"][$id] = min($qty, 99);
        }
    }
    if (isset($_POST["clear"])) {
        $_SESSION["cart"] = [];
    }
    flash("Giỏ hàng đã được cập nhật.");
    redirect("cart.php");
}
$cart = $_SESSION["cart"] ?? [];
$items = [];
$total = 0;
if ($cart) {
    $ids = array_map("intval", array_keys($cart));
    $in = implode(",", array_fill(0, count($ids), "?"));
    $s = db()->prepare(
        "SELECT id,name,image,price,stock FROM products WHERE id IN ($in)",
    );
    $s->execute($ids);
    $items = $s->fetchAll();
    foreach ($items as &$it) {
        $it["quantity"] = $cart[$it["id"]];
        $it["subtotal"] = $it["price"] * $it["quantity"];
        $total += $it["subtotal"];
    }
    unset($it);
}
require __DIR__ . "/includes/header.php";
?>
<div class="panel"><h1>Giỏ hàng</h1><?php if (
    !$items
): ?><div class="empty">Giỏ hàng đang trống. <a href="<?= e(
    url(),
) ?>">Tiếp tục mua sắm</a></div><?php else: ?><form method="post"><?= csrf_field() ?><div class="table-wrap"><table class="data-table"><thead><tr><th>Sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th>Tạm tính</th></tr></thead><tbody><?php foreach (
    $items
    as $it
): ?><tr data-cart-row data-price="<?= $it["price"] ?>"><td><?= e(
    $it["name"],
) ?></td><td><?= money(
    $it["price"],
) ?></td><td><input style="width:90px" type="number" min="0" max="<?= $it[
    "stock"
] ?>" name="qty[<?= $it["id"] ?>]" value="<?= $it[
    "quantity"
] ?>" data-quantity></td><td data-line-total><?= money(
    $it["subtotal"],
) ?></td></tr><?php endforeach; ?></tbody></table></div><p class="price">Tổng cộng: <?= money(
    $total,
) ?></p><button class="btn btn-outline" name="update" value="1">Cập nhật giỏ</button> <button class="btn btn-danger" name="clear" value="1" data-confirm="Bạn chắc chắn muốn xóa toàn bộ giỏ hàng?">Xóa giỏ</button> <a class="btn" href="<?= e(
    url("checkout.php"),
) ?>">Tiến hành đặt hàng</a></form><?php endif; ?></div><?php require __DIR__ .
    "/includes/footer.php"; ?>

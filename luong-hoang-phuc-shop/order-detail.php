<?php require_once __DIR__ . "/includes/init.php";
require_login();
$id = (int) ($_GET["id"] ?? 0);
$s = db()->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$s->execute([$id, current_user()["id"]]);
$o = $s->fetch();
if (!$o) {
    http_response_code(404);
    require __DIR__ . "/404.php";
    exit();
}
$items = db()->prepare("SELECT * FROM order_items WHERE order_id=?");
$items->execute([$id]);
$rows = $items->fetchAll();
$page_title = "Chi tiết đơn hàng";
require __DIR__ . "/includes/header.php";
?>
<div class="panel"><h1>Đơn <?= e(
    $o["order_code"],
) ?></h1><p>Trạng thái: <span class="status"><?= e(
    status_label($o["status"]),
) ?></span></p><p>Người nhận: <?= e($o["recipient_name"]) ?> · <?= e(
     $o["phone"],
 ) ?></p><p>Địa chỉ: <?= e(
    $o["shipping_address"],
) ?></p><div class="table-wrap"><table class="data-table"><thead><tr><th>Sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th></tr></thead><tbody><?php foreach (
    $rows
    as $r
): ?><tr><td><?= e($r["product_name"]) ?></td><td><?= money(
    $r["unit_price"],
) ?></td><td><?= $r["quantity"] ?></td><td><?= money(
    $r["subtotal"],
) ?></td></tr><?php endforeach; ?></tbody></table></div><h2 class="price">Tổng: <?= money(
    $o["total_amount"],
) ?></h2><?php if (
    $o["status"] === "pending"
): ?><form method="post" action="<?= e(
    url("order-cancel.php"),
) ?>"><?= csrf_field() ?><input type="hidden" name="order_id" value="<?= $id ?>"><button class="btn btn-danger" data-confirm="Bạn chắc chắn muốn hủy đơn hàng này?">Hủy đơn hàng</button></form><?php endif; ?></div><?php require __DIR__ .
    "/includes/footer.php"; ?>

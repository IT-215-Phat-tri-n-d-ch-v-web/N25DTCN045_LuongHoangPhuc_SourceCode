<?php require_once __DIR__ . "/includes/init.php";
require_login();
$page_title = "Đơn hàng của tôi";
$s = db()->prepare(
    "SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC",
);
$s->execute([current_user()["id"]]);
$orders = $s->fetchAll();
require __DIR__ . "/includes/header.php";
?>
<div class="panel"><h1>Lịch sử đơn hàng</h1><div class="table-wrap"><table class="data-table"><thead><tr><th>Mã đơn</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th></th></tr></thead><tbody><?php foreach (
    $orders
    as $o
): ?><tr><td><?= e($o["order_code"]) ?></td><td><?= e(
    $o["created_at"],
) ?></td><td><?= money(
    $o["total_amount"],
) ?></td><td><span class="status"><?= e(
    status_label($o["status"]),
) ?></span></td><td><a href="<?= e(
    url("order-detail.php?id=" . $o["id"]),
) ?>">Chi tiết</a></td></tr><?php endforeach; ?></tbody></table></div></div><?php require __DIR__ .
    "/includes/footer.php"; ?>

<?php require_once __DIR__ . "/../includes/init.php";
require_admin();
$page_title = "Quản lý đơn hàng";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $id = (int) $_POST["id"];
    $status = $_POST["status"] ?? "";
    if (
        in_array(
            $status,
            ["pending", "shipping", "completed", "cancelled"],
            true,
        )
    ) {
        db()
            ->prepare("UPDATE orders SET status=? WHERE id=?")
            ->execute([$status, $id]);
        flash("Đã cập nhật trạng thái đơn hàng.");
    }
    redirect("admin/orders.php");
}
$rows = db()
    ->query(
        "SELECT o.*,u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC",
    )
    ->fetchAll();
require __DIR__ . "/../includes/header.php";
?>
<div class="panel"><h1>Quản lý đơn hàng</h1><div class="table-wrap"><table class="data-table"><thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Tổng</th><th>Trạng thái</th><th>Cập nhật</th></tr></thead><tbody><?php foreach (
    $rows
    as $o
): ?><tr><td><?= e($o["order_code"]) ?></td><td><?= e(
    $o["email"],
) ?></td><td><?= money($o["total_amount"]) ?></td><td><?= e(
    status_label($o["status"]),
) ?></td><td><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $o[
    "id"
] ?>"><select name="status"><?php foreach (
    ["pending", "shipping", "completed", "cancelled"]
    as $st
): ?><option value="<?= $st ?>" <?= $st === $o["status"]
    ? "selected"
    : "" ?>><?= e(
    status_label($st),
) ?></option><?php endforeach; ?></select><button class="btn">Lưu</button></form></td></tr><?php endforeach; ?></tbody></table></div></div><?php require __DIR__ .
    "/../includes/footer.php"; ?>

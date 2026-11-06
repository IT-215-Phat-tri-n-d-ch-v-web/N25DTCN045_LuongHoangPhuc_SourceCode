<?php require_once __DIR__ . "/includes/init.php";
require_login();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("orders.php");
}
verify_csrf();
$id = (int) ($_POST["order_id"] ?? 0);
$pdo = db();
try {
    $pdo->beginTransaction();
    $s = $pdo->prepare(
        "SELECT * FROM orders WHERE id=? AND user_id=? FOR UPDATE",
    );
    $s->execute([$id, current_user()["id"]]);
    $o = $s->fetch();
    if (!$o || $o["status"] !== "pending") {
        throw new RuntimeException("Chỉ được hủy đơn đang chờ xử lý.");
    }
    $items = $pdo->prepare(
        "SELECT product_id,quantity FROM order_items WHERE order_id=?",
    );
    $items->execute([$id]);
    $upd = $pdo->prepare("UPDATE products SET stock=stock+? WHERE id=?");
    foreach ($items as $it) {
        $upd->execute([$it["quantity"], $it["product_id"]]);
    }
    $pdo->prepare("UPDATE orders SET status='cancelled' WHERE id=?")->execute([
        $id,
    ]);
    $pdo->commit();
    flash("Đã hủy đơn và hoàn lại tồn kho.");
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash($e->getMessage(), "error");
}
redirect("orders.php");

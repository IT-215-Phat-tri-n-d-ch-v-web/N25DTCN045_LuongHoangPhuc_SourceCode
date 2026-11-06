<?php require_once __DIR__ . "/includes/init.php";
require_login();
$page_title = "Đặt hàng";
$cart = $_SESSION["cart"] ?? [];
$error = "";
if (!$cart) {
    flash("Giỏ hàng đang trống.", "error");
    redirect("cart.php");
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $name = trim($_POST["recipient_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["shipping_address"] ?? "");
    $note = trim($_POST["note"] ?? "");
    if ($name === "" || $phone === "" || $address === "") {
        $error = "Vui lòng nhập đầy đủ thông tin nhận hàng.";
    }
    if (!$error) {
        $pdo = db();
        try {
            $pdo->beginTransaction();
            $ids = array_map("intval", array_keys($cart));
            $in = implode(",", array_fill(0, count($ids), "?"));
            $s = $pdo->prepare(
                "SELECT * FROM products WHERE id IN ($in) AND is_active=1 FOR UPDATE",
            );
            $s->execute($ids);
            $products = $s->fetchAll();
            if (count($products) !== count($ids)) {
                throw new RuntimeException(
                    "Một số sản phẩm không còn khả dụng.",
                );
            }
            $total = 0;
            foreach ($products as $p) {
                $qty = (int) $cart[$p["id"]];
                if ($qty > (int) $p["stock"]) {
                    throw new RuntimeException(
                        "Tồn kho không đủ cho " . $p["name"],
                    );
                }
                $total += (float) $p["price"] * $qty;
            }
            $code = order_code();
            $s = $pdo->prepare(
                "INSERT INTO orders(user_id,order_code,recipient_name,phone,shipping_address,note,total_amount,status) VALUES(?,?,?,?,?,?,?,'pending')",
            );
            $s->execute([
                current_user()["id"],
                $code,
                $name,
                $phone,
                $address,
                $note,
                $total,
            ]);
            $orderId = (int) $pdo->lastInsertId();
            $ins = $pdo->prepare(
                "INSERT INTO order_items(order_id,product_id,product_name,unit_price,quantity,subtotal) VALUES(?,?,?,?,?,?)",
            );
            $upd = $pdo->prepare(
                "UPDATE products SET stock=stock-? WHERE id=?",
            );
            foreach ($products as $p) {
                $qty = (int) $cart[$p["id"]];
                $ins->execute([
                    $orderId,
                    $p["id"],
                    $p["name"],
                    $p["price"],
                    $qty,
                    $p["price"] * $qty,
                ]);
                $upd->execute([$qty, $p["id"]]);
            }
            $pdo->commit();
            unset($_SESSION["cart"]);
            flash("Đặt hàng thành công. Mã đơn: " . $code);
            redirect("order-detail.php?id=" . $orderId);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = APP_DEBUG
                ? $e->getMessage()
                : "Không thể tạo đơn hàng. Vui lòng thử lại.";
        }
    }
}
$u = db()->prepare("SELECT * FROM users WHERE id=?");
$u->execute([current_user()["id"]]);
$user = $u->fetch();
require __DIR__ . "/includes/header.php";
?>
<div class="form-card"><h1>Thông tin nhận hàng</h1><?php if (
    $error
): ?><div class="alert error"><?= e(
    $error,
) ?></div><?php endif; ?><form method="post"><?= csrf_field() ?><div class="field"><label>Người nhận</label><input name="recipient_name" required value="<?= e(
    $_POST["recipient_name"] ?? $user["full_name"],
) ?>"></div><div class="field"><label>Số điện thoại</label><input name="phone" required value="<?= e(
    $_POST["phone"] ?? $user["phone"],
) ?>"></div><div class="field"><label>Địa chỉ</label><input name="shipping_address" required value="<?= e(
    $_POST["shipping_address"] ?? $user["address"],
) ?>"></div><div class="field"><label>Ghi chú</label><textarea name="note"><?= e(
    $_POST["note"] ?? "",
) ?></textarea></div><button class="btn">Xác nhận đặt hàng</button></form></div><?php require __DIR__ .
    "/includes/footer.php"; ?>

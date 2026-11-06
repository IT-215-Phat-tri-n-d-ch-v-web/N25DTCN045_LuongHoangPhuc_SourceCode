<?php require_once __DIR__ . "/../includes/init.php";
require_admin();
$page_title = "Quản trị";
$stats = db()
    ->query("SELECT status,COUNT(*) total FROM orders GROUP BY status")
    ->fetchAll();
$revenue = db()
    ->query(
        "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status='completed'",
    )
    ->fetchColumn();
$low = db()
    ->query("SELECT name,stock FROM products ORDER BY stock ASC LIMIT 5")
    ->fetchAll();
require __DIR__ . "/../includes/header.php";
?>
<div class="section-head"><div><span class="eyebrow">Khu vực quản trị</span><h1>Tổng quan cửa hàng</h1></div></div><div class="stats"><div class="stat-card"><span class="muted">Doanh thu hoàn tất</span><strong><?= money(
    $revenue,
) ?></strong></div><div class="stat-card"><span class="muted">Tổng đơn hàng</span><strong><?= db()
    ->query("SELECT COUNT(*) FROM orders")
    ->fetchColumn() ?></strong></div><div class="stat-card"><span class="muted">Sản phẩm</span><strong><?= db()
    ->query("SELECT COUNT(*) FROM products")
    ->fetchColumn() ?></strong></div><div class="stat-card"><span class="muted">Khách hàng</span><strong><?= db()
    ->query("SELECT COUNT(*) FROM users WHERE role='customer'")
    ->fetchColumn() ?></strong></div></div><div class="panel"><h2>Đơn hàng theo trạng thái</h2><?php foreach (
    $stats
    as $s
): ?><p><?= e(status_label($s["status"])) ?>: <strong><?= $s[
    "total"
] ?></strong></p><?php endforeach; ?></div><div class="panel"><h2>5 sản phẩm tồn kho thấp</h2><?php foreach (
    $low
    as $p
): ?><p><?= e($p["name"]) ?> — còn <?= $p[
     "stock"
 ] ?></p><?php endforeach; ?></div><div class="panel"><a class="btn" href="<?= e(
    url("admin/products.php"),
) ?>">Quản lý sản phẩm</a> <a class="btn btn-outline" href="<?= e(
    url("admin/categories.php"),
) ?>">Danh mục</a> <a class="btn btn-outline" href="<?= e(
    url("admin/orders.php"),
) ?>">Đơn hàng</a></div><?php require __DIR__ . "/../includes/footer.php"; ?>

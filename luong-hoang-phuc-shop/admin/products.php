<?php require_once __DIR__ . "/../includes/init.php";
require_admin();
$page_title = "Quản lý sản phẩm";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $id = (int) ($_POST["id"] ?? 0);
    $s = db()->prepare("DELETE FROM products WHERE id=?");
    try {
        $s->execute([$id]);
        flash("Đã xóa sản phẩm.");
    } catch (PDOException $e) {
        flash("Không thể xóa sản phẩm đã xuất hiện trong đơn hàng.", "error");
    }
    redirect("admin/products.php");
}
$rows = db()
    ->query(
        "SELECT p.*,c.name category_name FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC",
    )
    ->fetchAll();
require __DIR__ . "/../includes/header.php";
?>
<div class="panel"><div class="section-head"><h1>Sản phẩm</h1><a class="btn" href="<?= e(
    url("admin/product-form.php"),
) ?>">Thêm sản phẩm</a></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Tên</th><th>Danh mục</th><th>Giá</th><th>Tồn</th><th>Thao tác</th></tr></thead><tbody><?php foreach (
    $rows
    as $p
): ?><tr><td><?= e($p["name"]) ?></td><td><?= e(
    $p["category_name"],
) ?></td><td><?= money($p["price"]) ?></td><td><?= $p[
    "stock"
] ?></td><td><a href="<?= e(
    url("admin/product-form.php?id=" . $p["id"]),
) ?>">Sửa</a> <form style="display:inline" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $p[
    "id"
] ?>"><button class="btn btn-danger" data-confirm="Xóa sản phẩm này?">Xóa</button></form></td></tr><?php endforeach; ?></tbody></table></div></div><?php require __DIR__ .
    "/../includes/footer.php"; ?>

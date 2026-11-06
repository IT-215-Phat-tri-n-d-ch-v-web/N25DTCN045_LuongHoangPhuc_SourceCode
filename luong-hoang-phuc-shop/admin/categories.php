<?php require_once __DIR__ . "/../includes/init.php";
require_admin();
$page_title = "Danh mục";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $action = $_POST["action"] ?? "";
    try {
        if ($action === "add") {
            db()
                ->prepare(
                    "INSERT INTO categories(name,slug,description) VALUES(?,?,?)",
                )
                ->execute([
                    trim($_POST["name"]),
                    trim($_POST["slug"]),
                    trim($_POST["description"]),
                ]);
            flash("Đã thêm danh mục.");
        } elseif ($action === "delete") {
            db()
                ->prepare("DELETE FROM categories WHERE id=?")
                ->execute([(int) $_POST["id"]]);
            flash("Đã xóa danh mục.");
        }
    } catch (Throwable $e) {
        flash(
            "Không thể thao tác: danh mục có thể đang chứa sản phẩm hoặc slug đã tồn tại.",
            "error",
        );
    }
    redirect("admin/categories.php");
}
$rows = db()
    ->query(
        "SELECT c.*,COUNT(p.id) product_count FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id",
    )
    ->fetchAll();
require __DIR__ . "/../includes/header.php";
?>
<div class="panel"><h1>Quản lý danh mục</h1><form method="post" class="form-grid"><?= csrf_field() ?><input type="hidden" name="action" value="add"><div class="field"><label>Tên danh mục</label><input name="name" required></div><div class="field"><label>Slug</label><input name="slug" required></div><div class="field full"><label>Mô tả</label><input name="description"></div><button class="btn">Thêm danh mục</button></form></div><div class="panel"><div class="table-wrap"><table class="data-table"><thead><tr><th>Danh mục</th><th>Số sản phẩm</th><th></th></tr></thead><tbody><?php foreach (
    $rows
    as $r
): ?><tr><td><?= e($r["name"]) ?></td><td><?= $r[
    "product_count"
] ?></td><td><?php if (
    !$r["product_count"]
): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r[
    "id"
] ?>"><button class="btn btn-danger" data-confirm="Xóa danh mục?">Xóa</button></form><?php else: ?><span class="muted">Không thể xóa khi còn sản phẩm</span><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div><?php require __DIR__ .
    "/../includes/footer.php"; ?>

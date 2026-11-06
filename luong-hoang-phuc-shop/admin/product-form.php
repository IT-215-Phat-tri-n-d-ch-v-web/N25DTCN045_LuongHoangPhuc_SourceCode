<?php require_once __DIR__ . "/../includes/init.php";
require_admin();
$id = (int) ($_GET["id"] ?? 0);
$p = [
    "category_id" => "",
    "name" => "",
    "slug" => "",
    "description" => "",
    "image" => "assets/product-placeholder.svg",
    "price" => "",
    "stock" => 0,
    "specs" => "",
];
if ($id) {
    $s = db()->prepare("SELECT * FROM products WHERE id=?");
    $s->execute([$id]);
    $p = $s->fetch() ?: $p;
}
$cats = db()->query("SELECT * FROM categories")->fetchAll();
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $data = [
        (int) $_POST["category_id"],
        trim($_POST["name"]),
        trim($_POST["slug"]),
        trim($_POST["description"]),
        trim($_POST["image"] ?: "assets/product-placeholder.svg"),
        (float) $_POST["price"],
        (int) $_POST["stock"],
        trim($_POST["specs"]),
    ];
    try {
        if ($id) {
            $data[] = $id;
            db()
                ->prepare(
                    "UPDATE products SET category_id=?,name=?,slug=?,description=?,image=?,price=?,stock=?,specs=? WHERE id=?",
                )
                ->execute($data);
        } else {
            db()
                ->prepare(
                    "INSERT INTO products(category_id,name,slug,description,image,price,stock,specs) VALUES(?,?,?,?,?,?,?,?)",
                )
                ->execute($data);
        }
        flash("Đã lưu sản phẩm.");
        redirect("admin/products.php");
    } catch (Throwable $e) {
        $error = "Không thể lưu. Kiểm tra slug, danh mục và dữ liệu.";
    }
}
$page_title = "Biểu mẫu sản phẩm";
require __DIR__ . "/../includes/header.php";
?>
<div class="form-card"><h1><?= $id ? "Sửa" : "Thêm" ?> sản phẩm</h1><?php if (
     $error
 ): ?><div class="alert error"><?= e(
    $error,
) ?></div><?php endif; ?><form method="post"><?= csrf_field() ?><div class="field"><label>Danh mục</label><select name="category_id" required><?php foreach (
    $cats
    as $c
): ?><option value="<?= $c["id"] ?>" <?= $p["category_id"] == $c["id"]
    ? "selected"
    : "" ?>><?= e(
    $c["name"],
) ?></option><?php endforeach; ?></select></div><?php foreach (
    [
        "name" => "Tên sản phẩm",
        "slug" => "Slug",
        "description" => "Mô tả",
        "image" => "Đường dẫn ảnh",
        "price" => "Giá",
        "stock" => "Tồn kho",
        "specs" => "Thông số kỹ thuật",
    ]
    as $key => $label
): ?><div class="field"><label><?= e($label) ?></label><?php if (
    in_array($key, ["description", "specs"])
): ?><textarea name="<?= $key ?>" required><?= $key === "description"
    ? e($p[$key])
    : e(
        $p[$key],
    ) ?></textarea><?php else: ?><input name="<?= $key ?>" value="<?= e(
    (string) $p[$key],
) ?>" <?= in_array($key, ["name", "slug", "price"])
    ? "required"
    : "" ?>></div><?php endif;endforeach; ?><button class="btn">Lưu sản phẩm</button></form></div><?php require __DIR__ .
    "/../includes/footer.php"; ?>

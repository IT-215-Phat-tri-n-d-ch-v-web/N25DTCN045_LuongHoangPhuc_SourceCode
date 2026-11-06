<?php
require_once __DIR__ . "/includes/init.php";
$id = (int) ($_GET["id"] ?? 0);
$stmt = db()->prepare(
    "SELECT p.*,c.name category_name FROM products p JOIN categories c ON c.id=p.category_id WHERE p.id=? AND p.is_active=1",
);
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) {
    http_response_code(404);
    require __DIR__ . "/404.php";
    exit();
}
$page_title = $p["name"];
require __DIR__ . "/includes/header.php";
$related = db()->prepare(
    "SELECT id,name,image,price FROM products WHERE category_id=? AND id<>? AND is_active=1 LIMIT 3",
);
$related->execute([$p["category_id"], $id]);
?>
<div class="panel two-col"><div><img style="width:100%;max-height:420px;object-fit:contain;background:#111827;border-radius:14px" src="<?= e(
    product_image($p["image"]),
) ?>" alt="<?= e($p["name"]) ?>"></div><div><span class="tag"><?= e(
    $p["category_name"],
) ?></span><h1><?= e($p["name"]) ?></h1><div class="price"><?= money(
    $p["price"],
) ?></div><p><?= e(
    $p["description"],
) ?></p><p class="muted">Tồn kho: <?= (int) $p[
    "stock"
] ?></p><form method="post" action="<?= e(
    url("cart-add.php"),
) ?>"><?= csrf_field() ?><input type="hidden" name="product_id" value="<?= $id ?>"><div class="field"><label for="quantity">Số lượng</label><input id="quantity" type="number" name="quantity" min="1" max="<?= max(
    1,
    (int) $p["stock"],
) ?>" value="1" required></div><button class="btn" <?= $p["stock"] < 1
    ? "disabled"
    : "" ?>>Thêm vào giỏ hàng</button></form></div></div>
<div class="panel"><h2>Thông số kỹ thuật</h2><p><?= nl2br(
    e($p["specs"] ?? "Đang cập nhật"),
) ?></p></div>
<h2>Sản phẩm cùng danh mục</h2><div class="product-grid"><?php foreach (
    $related
    as $r
): ?><article class="product-card"><img src="<?= e(
    product_image($r["image"]),
) ?>" alt="<?= e($r["name"]) ?>"><div class="product-info"><h3><?= e(
    $r["name"],
) ?></h3><div class="price"><?= money(
    $r["price"],
) ?></div><a class="btn btn-outline" href="<?= e(
    url("product.php?id=" . $r["id"]),
) ?>">Xem sản phẩm</a></div></article><?php endforeach; ?></div>
<?php require __DIR__ . "/includes/footer.php"; ?>

<?php
require_once __DIR__ . "/includes/init.php";

$page_title = "Trang chủ";
$category = (int) ($_GET["category"] ?? 0);
$q = trim($_GET["q"] ?? "");
$sort = $_GET["sort"] ?? "new";

$page = max(1, (int) ($_GET["page"] ?? 1));
$perPage = 8;
$offset = ($page - 1) * $perPage;

$where = ["p.is_active = 1"];
$params = [];

if ($category) {
    $where[] = "p.category_id = ?";
    $params[] = $category;
}

if ($q !== "") {
    $where[] = "p.name LIKE ?";
    $params[] = "%" . $q . "%";
}

$orderOptions = [
    "price_asc" => "p.price ASC",
    "price_desc" => "p.price DESC",
    "new" => "p.created_at DESC",
];
$order = $orderOptions[$sort] ?? "p.created_at DESC";
$sqlWhere = implode(" AND ", $where);

$count = db()->prepare("SELECT COUNT(*) FROM products p WHERE $sqlWhere");
$count->execute($params);
$total = (int) $count->fetchColumn();

$stmt = db()->prepare(
    "SELECT p.*, c.name category_name
    FROM products p
    JOIN categories c ON c.id = p.category_id
    WHERE $sqlWhere
    ORDER BY $order
    LIMIT $perPage OFFSET $offset",
);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$pages = max(1, (int) ceil($total / $perPage));

require __DIR__ . "/includes/header.php";
?>

<section class="hero">
    <div>
        <span class="eyebrow">Công nghệ cho hành trình của bạn</span>
        <h1>Chọn thiết bị phù hợp. Nâng tầm trải nghiệm.</h1>
        <p>Khám phá laptop, linh kiện và thiết bị ngoại vi dành cho học tập, công việc và giải trí.</p>
        <a class="btn" href="#products">Khám phá sản phẩm</a>
    </div>
    <div class="hero-art" aria-label="Minh họa máy tính">⌨️ 💻 🖱️</div>
</section>

<div class="section-head" id="products">
    <div>
        <span class="eyebrow">Danh mục sản phẩm</span>
        <h2>Sản phẩm nổi bật</h2>
    </div>
    <span class="muted">Tìm thấy <?= $total ?> sản phẩm</span>
</div>

<form class="filters" method="get">
    <input
        name="q"
        value="<?= e($q) ?>"
        placeholder="Tìm theo tên sản phẩm..."
        aria-label="Tìm kiếm sản phẩm"
    >

    <select name="category" aria-label="Lọc danh mục">
        <option value="0">Tất cả danh mục</option>
        <?php foreach ($categories as $c): ?>
            <option
                value="<?= (int) $c["id"] ?>"
                <?= $category === (int) $c["id"] ? "selected" : "" ?>
            >
                <?= e($c["name"]) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="sort" aria-label="Sắp xếp">
        <option value="new">Mới nhất</option>
        <option value="price_asc" <?= $sort === "price_asc"
            ? "selected"
            : "" ?>>
            Giá tăng dần
        </option>
        <option value="price_desc" <?= $sort === "price_desc"
            ? "selected"
            : "" ?>>
            Giá giảm dần
        </option>
    </select>

    <button class="btn" type="submit">Áp dụng</button>
</form>

<?php if ($products): ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <article class="product-card">
                <img
                    src="<?= e(product_image($p["image"])) ?>"
                    alt="<?= e($p["name"]) ?>"
                >
                <div class="product-info">
                    <span class="tag"><?= e($p["category_name"]) ?></span>
                    <h3><?= e($p["name"]) ?></h3>
                    <div class="price"><?= money($p["price"]) ?></div>
                    <span class="muted">Còn <?= (int) $p[
                        "stock"
                    ] ?> sản phẩm</span>
                    <a
                        class="btn btn-outline"
                        href="<?= e(url("product.php?id=" . (int) $p["id"])) ?>"
                    >
                        Xem chi tiết
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty panel">Không tìm thấy sản phẩm phù hợp.</div>
<?php endif; ?>

<?php if ($pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?<?= e(
                http_build_query([
                    "q" => $q,
                    "category" => $category,
                    "sort" => $sort,
                    "page" => $i,
                ]),
            ) ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . "/includes/footer.php"; ?>

<?php
$page_title = $page_title ?? APP_NAME;
$active = basename($_SERVER["PHP_SELF"] ?? "index.php");
?>
<!doctype html><html lang="vi"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?> | <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= e(url("css/style.css")) ?>">
</head><body>
<header class="site-header"><div class="container header-inner">
<a class="brand" href="<?= e(url()) ?>"><img src="<?= e(
    url("assets/logo-luong-hoang-phuc.png"),
) ?>" alt="Logo cửa hàng Lương Hoàng Phúc"><span><?= e(
    APP_NAME,
) ?><small>Laptop & thiết bị máy tính</small></span></a>
<button class="menu-toggle" type="button" aria-label="Mở menu">☰</button>
<nav class="nav">
<a class="<?= $active === "index.php" ? "active" : "" ?>" href="<?= e(
    url(),
) ?>">Trang chủ</a>
<a class="<?= $active === "about.php" ? "active" : "" ?>" href="<?= e(
    url("about.php"),
) ?>">Giới thiệu</a>
<a class="<?= $active === "contact.php" ? "active" : "" ?>" href="<?= e(
    url("contact.php"),
) ?>">Liên hệ</a>
<a href="<?= e(url("cart.php")) ?>">Giỏ hàng (<?= cart_count() ?>)</a>
<?php if (current_user()): ?>
<a href="<?= e(url("orders.php")) ?>">Đơn hàng</a><a href="<?= e(
    url("profile.php"),
) ?>">Tài khoản</a>
<?php if (is_admin()): ?><a href="<?= e(
    url("admin/index.php"),
) ?>">Quản trị</a><?php endif; ?>
<a href="<?= e(url("logout.php")) ?>">Đăng xuất</a>
<?php else: ?><a href="<?= e(
    url("login.php"),
) ?>">Đăng nhập</a><a class="nav-cta" href="<?= e(
    url("register.php"),
) ?>">Đăng ký</a><?php endif; ?>
</nav></div></header>
<main class="container main-content"><?php render_flash(); ?>

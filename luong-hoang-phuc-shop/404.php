<?php http_response_code(404);
if (!defined("APP_NAME")) {
    require_once __DIR__ . "/includes/init.php";
}
$page_title = "Không tìm thấy trang";
require __DIR__ . "/includes/header.php";
?><div class="panel empty"><h1>404 – Không tìm thấy nội dung</h1><p>Trang hoặc sản phẩm bạn yêu cầu không tồn tại.</p><a class="btn" href="<?= e(
    url(),
) ?>">Về trang chủ</a></div><?php require __DIR__ . "/includes/footer.php"; ?>

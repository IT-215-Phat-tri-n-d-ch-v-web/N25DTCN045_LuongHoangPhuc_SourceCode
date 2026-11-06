<?php require_once __DIR__ . "/includes/init.php";
require_login();
$page_title = "Đổi mật khẩu";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $s = db()->prepare("SELECT password FROM users WHERE id=?");
    $s->execute([current_user()["id"]]);
    $hash = $s->fetchColumn();
    $old = $_POST["current_password"] ?? "";
    $new = $_POST["new_password"] ?? "";
    if (!password_verify($old, $hash)) {
        $error = "Mật khẩu hiện tại không đúng.";
    } elseif (strlen($new) < 8) {
        $error = "Mật khẩu mới phải có ít nhất 8 ký tự.";
    } else {
        db()
            ->prepare("UPDATE users SET password=? WHERE id=?")
            ->execute([
                password_hash($new, PASSWORD_DEFAULT),
                current_user()["id"],
            ]);
        flash("Đổi mật khẩu thành công.");
        redirect("profile.php");
    }
}
require __DIR__ . "/includes/header.php";
?>
<div class="form-card"><h1>Đổi mật khẩu</h1><?php if (
    $error
): ?><div class="alert error"><?= e(
    $error,
) ?></div><?php endif; ?><form method="post"><?= csrf_field() ?><div class="field"><label>Mật khẩu hiện tại</label><input type="password" name="current_password" required></div><div class="field"><label>Mật khẩu mới</label><input type="password" name="new_password" minlength="8" required></div><button class="btn">Cập nhật mật khẩu</button></form></div><?php require __DIR__ .
    "/includes/footer.php"; ?>

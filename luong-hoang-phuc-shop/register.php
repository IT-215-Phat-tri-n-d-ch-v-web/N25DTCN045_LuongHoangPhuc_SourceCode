<?php require_once __DIR__ . "/includes/init.php";
$page_title = "Đăng ký";
$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $pass = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";
    if (
        $name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($pass) < 8 ||
        $pass !== $confirm
    ) {
        $errors[] =
            "Thông tin không hợp lệ hoặc mật khẩu chưa khớp (tối thiểu 8 ký tự).";
    }
    if (!$errors) {
        $s = db()->prepare("SELECT id FROM users WHERE email=?");
        $s->execute([$email]);
        if ($s->fetch()) {
            $errors[] = "Email đã được sử dụng.";
        }
    }
    if (!$errors) {
        $s = db()->prepare(
            "INSERT INTO users(full_name,email,password,role) VALUES(?,?,?,?)",
        );
        $s->execute([
            $name,
            $email,
            password_hash($pass, PASSWORD_DEFAULT),
            "customer",
        ]);
        flash("Đăng ký thành công. Bạn có thể đăng nhập.");
        redirect("login.php");
    }
}
require __DIR__ . "/includes/header.php";
?>
<div class="form-card"><h1>Tạo tài khoản</h1><?php foreach (
    $errors
    as $er
): ?><div class="alert error"><?= e(
    $er,
) ?></div><?php endforeach; ?><form method="post" data-password-match><?= csrf_field() ?><div class="field"><label>Họ và tên</label><input name="full_name" required value="<?= e(
    $_POST["full_name"] ?? "",
) ?>"></div><div class="field"><label>Email</label><input type="email" name="email" required value="<?= e(
    $_POST["email"] ?? "",
) ?>"></div><div class="field"><label>Mật khẩu</label><input id="password" type="password" name="password" minlength="8" required></div><div class="field"><label>Nhập lại mật khẩu</label><input id="confirm_password" type="password" name="confirm_password" minlength="8" required></div><button class="btn">Đăng ký</button></form></div><?php require __DIR__ .
    "/includes/footer.php"; ?>

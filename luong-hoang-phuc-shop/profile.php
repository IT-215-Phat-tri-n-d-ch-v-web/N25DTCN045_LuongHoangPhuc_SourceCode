<?php require_once __DIR__ . "/includes/init.php";
require_login();
$page_title = "Tài khoản";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $name = trim($_POST["full_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    if ($name === "") {
        $error = "Họ tên không được để trống.";
    } elseif ($phone !== "" && !preg_match("/\A[0-9]+\z/", $phone)) {
        $error = "Số điện thoại chỉ được chứa chữ số.";
    } else {
        db()
            ->prepare(
                "UPDATE users SET full_name=?,phone=?,address=? WHERE id=?",
            )
            ->execute([$name, $phone, $address, current_user()["id"]]);
        $_SESSION["user"]["full_name"] = $name;
        flash("Cập nhật thông tin thành công.");
        redirect("profile.php");
    }
}
$s = db()->prepare("SELECT * FROM users WHERE id=?");
$s->execute([current_user()["id"]]);
$u = $s->fetch();
require __DIR__ . "/includes/header.php";
?>
<div class="form-card"><h1>Thông tin tài khoản</h1><?php if (
    $error
): ?><div class="alert error"><?= e(
    $error,
) ?></div><?php endif; ?><form method="post"><?= csrf_field() ?><div class="field"><label>Họ tên</label><input name="full_name" required value="<?= e(
    $u["full_name"],
) ?>"></div><div class="field"><label>Email</label><input value="<?= e(
    $u["email"],
) ?>" disabled></div><div class="field"><label>Số điện thoại</label><input name="phone" type="tel" inputmode="numeric" pattern="[0-9]*" title="Chỉ nhập chữ số" value="<?= e(
    $u["phone"],
) ?>"></div><div class="field"><label>Địa chỉ</label><input name="address" value="<?= e(
    $u["address"],
) ?>"></div><button class="btn">Lưu thay đổi</button></form><a href="<?= e(
    url("change-password.php"),
) ?>">Đổi mật khẩu</a></div><?php require __DIR__ . "/includes/footer.php"; ?>

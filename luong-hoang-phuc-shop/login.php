<?php require_once __DIR__ . "/includes/init.php";
$page_title = "Đăng nhập";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $email = trim($_POST["email"] ?? "");
    $pass = $_POST["password"] ?? "";
    $s = db()->prepare("SELECT * FROM users WHERE email=?");
    $s->execute([$email]);
    $u = $s->fetch();
    if ($u && $u["locked_until"] && strtotime($u["locked_until"]) > time()) {
        $error = "Tài khoản tạm khóa. Vui lòng thử lại sau.";
    } elseif ($u && password_verify($pass, $u["password"])) {
        db()
            ->prepare(
                "UPDATE users SET failed_attempts=0,locked_until=NULL WHERE id=?",
            )
            ->execute([$u["id"]]);
        session_regenerate_id(true);
        $_SESSION["user"] = [
            "id" => $u["id"],
            "full_name" => $u["full_name"],
            "email" => $u["email"],
            "role" => $u["role"],
        ];
        $to =
            $_SESSION["intended"] ??
            ($u["role"] === "admin" ? "admin/index.php" : "index.php");
        unset($_SESSION["intended"]);
        redirect(
            str_starts_with($to, "http")
                ? "index.php"
                : ltrim(str_replace(BASE_URL, "", $to), "/"),
        );
    } else {
        $error = "Email hoặc mật khẩu không chính xác.";
        if ($u) {
            $attempt = (int) $u["failed_attempts"] + 1;
            $lock = $attempt >= 5 ? date("Y-m-d H:i:s", time() + 300) : null;
            db()
                ->prepare(
                    "UPDATE users SET failed_attempts=?,locked_until=? WHERE id=?",
                )
                ->execute([$attempt, $lock, $u["id"]]);
            if ($lock) {
                $error = "Bạn đã nhập sai 5 lần. Tài khoản bị khóa 5 phút.";
            }
        }
    }
}
require __DIR__ . "/includes/header.php";
?>
<div class="form-card"><h1>Đăng nhập</h1><?php if (
    $error
): ?><div class="alert error"><?= e(
    $error,
) ?></div><?php endif; ?><form method="post"><?= csrf_field() ?><div class="field"><label>Email</label><input type="email" name="email" required></div><div class="field"><label>Mật khẩu</label><input type="password" name="password" required></div><button class="btn">Đăng nhập</button></form><p class="muted">Chưa có tài khoản? <a href="<?= e(
    url("register.php"),
) ?>">Đăng ký</a></p></div><?php require __DIR__ . "/includes/footer.php"; ?>

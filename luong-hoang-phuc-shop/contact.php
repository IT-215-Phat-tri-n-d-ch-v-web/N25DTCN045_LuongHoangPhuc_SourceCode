<?php require_once __DIR__ . "/includes/init.php";
$page_title = "Liên hệ";
$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();
    $name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");
    if (
        $name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $subject === "" ||
        mb_strlen($message) < 10
    ) {
        $errors[] =
            "Vui lòng nhập đầy đủ thông tin hợp lệ; nội dung tối thiểu 10 ký tự.";
    }
    if (!$errors) {
        $s = db()->prepare(
            "INSERT INTO contacts(full_name,email,subject,message) VALUES(?,?,?,?)",
        );
        $s->execute([$name, $email, $subject, $message]);
        flash("Cảm ơn bạn đã liên hệ. Thông tin đã được ghi nhận.");
        redirect("contact.php");
    }
}
require __DIR__ . "/includes/header.php";
?>
<div class="form-card"><h1>Liên hệ với chúng tôi</h1><?php foreach (
    $errors
    as $er
): ?><div class="alert error"><?= e($er) ?></div><?php endforeach; ?>
<form method="post"><?= csrf_field() ?><div class="field"><label for="full_name">Họ tên</label><input id="full_name" name="full_name" required value="<?= e(
    $_POST["full_name"] ?? "",
) ?>"></div><div class="field"><label for="email">Email</label><input id="email" type="email" name="email" required value="<?= e(
    $_POST["email"] ?? "",
) ?>"></div><div class="field"><label for="subject">Tiêu đề</label><input id="subject" name="subject" required value="<?= e(
    $_POST["subject"] ?? "",
) ?>"></div><div class="field"><label for="contact-message">Nội dung</label><textarea id="contact-message" name="message" minlength="10" required><?= e(
    $_POST["message"] ?? "",
) ?></textarea></div><button class="btn">Gửi liên hệ</button></form></div>
<?php require __DIR__ . "/includes/footer.php"; ?>

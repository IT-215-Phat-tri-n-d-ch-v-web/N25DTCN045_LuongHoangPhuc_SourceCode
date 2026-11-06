<?php
declare(strict_types=1);
function e(?string $value): string
{
    return htmlspecialchars($value ?? "", ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}
function url(string $path = ""): string
{
    return rtrim(BASE_URL, "/") . "/" . ltrim($path, "/");
}
function redirect(string $path): never
{
    header("Location: " . url($path));
    exit();
}
function current_user(): ?array
{
    return $_SESSION["user"] ?? null;
}
function is_admin(): bool
{
    return (current_user()["role"] ?? "") === "admin";
}
function require_login(): void
{
    if (!current_user()) {
        $_SESSION["intended"] = $_SERVER["REQUEST_URI"] ?? url();
        redirect("login.php");
    }
}
function require_admin(): void
{
    if (!current_user()) {
        http_response_code(403);
        exit("403 - Vui lòng đăng nhập.");
    }
    if (!is_admin()) {
        http_response_code(403);
        exit("403 - Bạn không có quyền truy cập.");
    }
}
function csrf_token(): string
{
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf"];
}
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}
function verify_csrf(): void
{
    if (
        $_SERVER["REQUEST_METHOD"] === "POST" &&
        !hash_equals($_SESSION["csrf"] ?? "", $_POST["csrf"] ?? "")
    ) {
        http_response_code(419);
        exit("Yêu cầu không hợp lệ (CSRF).");
    }
}
function flash(string $message, string $type = "success"): void
{
    $_SESSION["flash"] = ["message" => $message, "type" => $type];
}
function money(float|string $amount): string
{
    return number_format((float) $amount, 0, ",", ".") . " ₫";
}
function cart_count(): int
{
    return array_sum($_SESSION["cart"] ?? []);
}
function product_image(string $image): string
{
    return url($image ?: "assets/product-placeholder.svg");
}
function status_label(string $status): string
{
    return [
        "pending" => "Chờ xử lý",
        "shipping" => "Đang giao",
        "completed" => "Hoàn tất",
        "cancelled" => "Đã hủy",
    ][$status] ?? $status;
}
function render_flash(): void
{
    if (!empty($_SESSION["flash"])) {
        $f = $_SESSION["flash"];
        unset($_SESSION["flash"]);
        echo '<div class="alert ' .
            e($f["type"]) .
            '">' .
            e($f["message"]) .
            "</div>";
    }
}
function order_code(): string
{
    return "DCA-" . date("Ymd") . "-" . strtoupper(bin2hex(random_bytes(2)));
}

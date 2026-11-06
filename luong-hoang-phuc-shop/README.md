# Cửa hàng Lương Hoàng Phúc

## Yêu cầu môi trường

- PHP 8.0+ (khuyến nghị PHP 8.1+), extension PDO MySQL, mbstring.
- MySQL 8.0+ hoặc MariaDB tương thích.
- XAMPP/Laragon.

## Cài đặt local

1. Giải nén thư mục `luong-hoang-phuc-shop` vào `htdocs`.
2. Mở phpMyAdmin, import `database/database.sql`.
3. Kiểm tra `config/config.php`: DB_HOST, DB_NAME, DB_USER, DB_PASS, BASE_URL.
4. Mở `http://localhost/luong-hoang-phuc-shop/`.

## Tài khoản demo

- Admin: `admin@example.com` / `password`
- Khách 1: `minhanh@example.com` / `password`
- Khách 2: `quocbao@example.com` / `password`

Đổi tất cả mật khẩu demo trước khi triển khai Internet. Ảnh sản phẩm hiện dùng ảnh placeholder, cần thay bằng ảnh sản phẩm thật có quyền sử dụng.

## Ghi chú phiên bản khởi tạo

Đây là bộ khung chạy local để bắt đầu tích hợp và kiểm thử. Trước khi nộp cần hoàn thiện upload ảnh an toàn, kiểm tra trạng thái đơn hàng ở mọi chuyển tiếp, phân trang admin, test CSRF toàn bộ form, rà soát đường dẫn hosting và chụp đủ minh chứng A1-D11.

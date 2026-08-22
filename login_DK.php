<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);

include "connect.php";

if ($conn->connect_error) {
    echo json_encode([
        "status"  => "error",
        "message" => "Kết nối DB thất bại"
    ]);
    exit;
}

$hoten    = trim($_POST['hoten'] ?? '');
$sdt      = trim($_POST['sdt'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($hoten) || empty($email) || empty($password)) {
    echo json_encode([
        "status"  => "empty",
        "message" => "Vui lòng điền đủ thông tin bắt buộc"
    ]);
    exit;
}

// validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "invalid_email",
        "message" => "Email không hợp lệ"
    ]);
    exit;
}

// kiểm tra email
$stmt = $conn->prepare("SELECT id FROM taikhoan WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        "status"  => "exists",
        "message" => "Email đã tồn tại"
    ]);
    exit;
}

// hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// insert
$stmt = $conn->prepare("INSERT INTO taikhoan (hoten, sdt, email, password, role, trang_thai) VALUES (?, ?, ?, ?, ?, ?)");

$role = 0;        // user
$trang_thai = 1;  // hoạt động

$stmt->bind_param("ssssii", $hoten, $sdt, $email, $hashed, $role, $trang_thai);

if ($stmt->execute()) {
    echo json_encode([
        "status"  => "success",
        "message" => "Đăng ký thành công!"
    ]);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "Lỗi tạo tài khoản"
    ]);
}

$conn->close();
?>
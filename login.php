<?php
session_start();
header('Content-Type: application/json');
include("connect.php");

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode([
        "status" => "empty",
        "message" => "Vui lòng nhập đầy đủ!"
    ]);
    exit();
}

// đổi bảng dangnhap -> ql_taikhoan
$stmt = $conn->prepare("SELECT * FROM taikhoan WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // 🔒 CHECK BỊ KHÓA TRƯỚC
    if ($user['trang_thai'] == 0) {
        echo json_encode([
            "status" => "blocked",
            "message" => "Tài khoản đã bị khóa"
        ]);
        exit();
    }

    // 🔑 SAU ĐÓ MỚI CHECK PASSWORD
    if (!password_verify($password, $user['password'])) {
        echo json_encode([
            "status" => "wrong_password",
            "message" => "Sai mật khẩu!"
        ]);
        exit();
    }

    // ✅ login OK
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['hoten'];   
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role'] = (int)$user['role'];

    echo json_encode([
        "status" => "success",
        "role"   => (int)$user['role']
    ]);

} else {
    echo json_encode([
        "status" => "not_found",
        "message" => "Email không tồn tại!"
    ]);
}
$conn->close();
?>
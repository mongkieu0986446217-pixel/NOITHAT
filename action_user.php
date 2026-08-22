<?php
include "connect.php";
session_start();

// 🔒 Check quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    die("Bạn không có quyền!");
}

// Kiểm tra có id không
if (!isset($_GET['id'])) {
    header("Location: ql_taikhoan.php");
    exit();
}

// Lấy id an toàn
$id = intval($_GET['id']);
$action = $_GET['action'] ?? '';

// 🔥 LẤY ROLE
$check = $conn->prepare("SELECT role FROM ql_taikhoan WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$result = $check->get_result();
$user = $result->fetch_assoc(); // ❗ thiếu dòng này trong code bạn

// ❌ Không tồn tại
if (!$user) {
    die("Tài khoản không tồn tại!");
}

// ❌ KHÔNG CHO ĐỤNG ADMIN
if ($user['role'] == 1) {
    die("Không thể thao tác với admin!");
}

// ===== XỬ LÝ ACTION =====
if ($action == 'delete') {

    $stmt = $conn->prepare("DELETE FROM ql_taikhoan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

} elseif ($action == 'toggle') {

    // 1. Lấy trạng thái hiện tại
    $get = $conn->prepare("SELECT trang_thai FROM ql_taikhoan WHERE id = ?");
    $get->bind_param("i", $id);
    $get->execute();
    $res = $get->get_result();
    $row = $res->fetch_assoc();

    if (!$row) {
        die("Không tìm thấy user!");
    }

    // 2. Đảo trạng thái
    $new_status = ($row['trang_thai'] == 1) ? 0 : 1;

    // 3. Update
    $update = $conn->prepare("UPDATE ql_taikhoan SET trang_thai = ? WHERE id = ?");
    $update->bind_param("ii", $new_status, $id);
    $update->execute();

} else {
    die("Action không hợp lệ!");
}
// quay lại trang
header("Location: ql_taikhoan.php");
exit();
?>
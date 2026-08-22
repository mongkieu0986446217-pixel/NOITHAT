<?php
session_start();

// nếu chưa có giỏ hàng
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// lấy dữ liệu từ form
$product = [
    "name" => $_POST['name'],
    "price" => $_POST['price'],
    "img" => $_POST['img'],
    "qty" => 1
];

// thêm vào session
$_SESSION['cart'][] = $product;

// quay lại trang sản phẩm
header("Location: sanpham.php");
exit();
?>
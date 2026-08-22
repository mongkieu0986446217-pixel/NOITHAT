<?php
include "connect.php";

/* ===== ĐƠN HÀNG ===== */
$don = $conn->query("SELECT COUNT(*) as total FROM don_hang")->fetch_assoc()['total'];
$don_huy = $conn->query("SELECT COUNT(*) as total FROM don_hang WHERE trang_thai='Đã hủy'")->fetch_assoc()['total'];

/* ===== DOANH THU ===== */
$tien = $conn->query("SELECT SUM(tong_tien) as total FROM don_hang WHERE trang_thai='Hoàn thành'")
->fetch_assoc()['total'] ?? 0;

/* ===== KHÁCH HÀNG ===== */
$kh = $conn->query("SELECT COUNT(*) as total FROM taikhoan")->fetch_assoc()['total'];

/* ===== SẢN PHẨM ===== */
$sp = $conn->query("SELECT COUNT(*) as total FROM san_pham")->fetch_assoc()['total'];
$dang_ban = $conn->query("SELECT COUNT(*) as total FROM san_pham WHERE trang_thai=1")->fetch_assoc()['total'];
$ngung_ban = $conn->query("SELECT COUNT(*) as total FROM san_pham WHERE trang_thai=0")->fetch_assoc()['total'];

/* ===== KHO ===== */
$ton = $conn->query("SELECT SUM(so_luong) as total FROM san_pham")->fetch_assoc()['total'] ?? 0;
$het = $conn->query("SELECT COUNT(*) as total FROM san_pham WHERE so_luong=0")->fetch_assoc()['total'];
$saphet = $conn->query("SELECT COUNT(*) as total FROM san_pham WHERE so_luong > 0 AND so_luong <= 5")->fetch_assoc()['total'];

/* ===== TRẠNG THÁI SẢN PHẨM ===== */
$con_hang = $conn->query("SELECT COUNT(*) as total FROM san_pham WHERE so_luong > 0")->fetch_assoc()['total'];
$het_hang = $conn->query("SELECT COUNT(*) as total FROM san_pham WHERE so_luong = 0")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="adminmau1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<header class="header">
  <div class="logo">
    <img src="img/logo.png" alt="Nội Thất LEM">
  </div>

  <div class="admin-box">
    <div class="admin-card">
      <div class="admin-name">Admin</div>
      <div class="logout-text">
        Đăng xuất <i class="fa-solid fa-right-from-bracket"></i>
      </div>
    </div>
  </div>
</header>

<div class="main">

<aside class="sidebar">
  <ul>
    <li><a href="admin.php"><i class="fa-solid fa-house"></i> Tổng quan</a></li>
    <li><a href="Danhmuc.php"><i class="fa-solid fa-warehouse"></i> Danh mục</a></li>
    <li><a href="Qlsanpham.php"><i class="fa-solid fa-couch"></i> Sản phẩm</a></li>
    <li><a href="Qldonhang.php"><i class="fa-solid fa-file-invoice"></i> Đơn hàng</a></li>
    <li><a href="ql_taikhoan.php"><i class="fa-solid fa-users"></i> Tài khoản</a></li>
  </ul>
</aside>

<section class="content">

<h2 class="title">Hoạt động hôm nay</h2>

<div class="stats">

  <div class="stat green">
    <i class="fa-solid fa-wallet"></i>
    <div>
      <p>Tiền bán hàng</p>
      <strong><?= number_format($tien) ?>đ</strong>
    </div>
  </div>

  <div class="stat blue">
    <i class="fa-solid fa-cart-shopping"></i>
    <div>
      <p>Số đơn hàng: <strong><?= $don ?></strong></p>
      <p>Số sản phẩm: <strong><?= $sp ?></strong></p>
    </div>
  </div>

  <div class="stat orange">
    <i class="fa-solid fa-user"></i>
    <div>
      <p>Khách hàng</p>
      <strong><?= $kh ?></strong>
    </div>
  </div>

  <div class="stat yellow">
    <i class="fa-solid fa-circle-xmark"></i>
    <div>
      <p>Đơn hủy</p>
      <strong><?= $don_huy ?></strong>
    </div>
  </div>

</div>

<div class="info-boxes">

  <!-- HOẠT ĐỘNG -->
  <div class="box">
    <div class="box-header">
      <i class="fa-solid fa-clock"></i> Hoạt động
    </div>
    <ul>
      <li>Tiền bán hàng <span><?= number_format($tien) ?>đ</span></li>
      <li>Số đơn hàng <span><?= $don ?></span></li>
      <li>Số sản phẩm <span><?= $sp ?></span></li>
      <li>Đơn hủy <span><?= $don_huy ?></span></li>
    </ul>
  </div>

  <!-- KHO -->
  <div class="box">
    <div class="box-header">
      <i class="fa-solid fa-warehouse"></i> Thông tin kho
    </div>
    <ul>
      <li>Tồn kho <span><?= $ton ?></span></li>
      <li>Hết hàng <span><?= $het ?></span></li>
      <li>Sắp hết hàng <span><?= $saphet ?></span></li>
    </ul>
  </div>

  <!-- SẢN PHẨM -->
  <div class="box">
    <div class="box-header">
      <i class="fa-solid fa-box"></i> Thông tin sản phẩm
    </div>
    <ul>
      <li>Tổng sản phẩm <span><?= $sp ?></span></li>
      <li>Còn hàng <span><?= $con_hang ?></span></li>
      <li>Hết hàng <span><?= $het_hang ?></span></li>
    </ul>
  </div>

</div>

</section>
</div>

</body>
</html>
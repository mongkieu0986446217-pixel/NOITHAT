<?php
    include "connect.php";
    session_start();

    $sql = "SELECT * FROM taikhoan";
    $result = mysqli_query($conn, $sql);
    
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="ql_taikhoan2.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- HEADER -->
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

  <!-- SIDEBAR -->
 <aside class="sidebar">
  <ul>
     <li>
        <a href="admin.php">
        <i class="fa-solid fa-house"></i> Tổng quan
      </a>
    </li>

    <li>
      <a href="Danhmuc.php">
      <i class="fa-solid fa-warehouse"></i> Danh mục
      </a>
    </li>

    <li>
      <a href="Qlsanpham.php">
      <i class="fa-solid fa-couch"></i> Sản phẩm
      </a>
    </li>

    <li>
  <a href="Qldonhang.php">
    <i class="fa-solid fa-file-invoice"></i> Đơn hàng
  </a>
</li>

    <li>
     <a href="ql_taikhoan.php">
      <i class="fa-solid fa-users"></i> Tài khoản
      </a>
    </li>
 
  </ul>
</aside>

  <!-- CONTENT -->
   <section class="content">

   <!-- ===== Acccount ===== -->

    <h2 class="title">Quản lí tài khoản</h2>

<div class="table-wrapper">
  <table class="table-account">
<tr>
    <th>ID</th>
    <th>Họ tên</th>
    <th>Email</th>
    <th>SĐT</th>
    <th>Password</th>
    <th>Trạng thái</th>
    <th>Hành động</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['hoten'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['sdt'] ?></td>
    <td>********</td>

    <!-- TRẠNG THÁI -->
    <td>
        <?php if ($row['trang_thai'] == 1) { ?>
            <span class="status status-active">Hoạt động</span>
        <?php } else { ?>
            <span class="status status-lock">Khóa</span>
        <?php } ?>
    </td>

    <!-- HÀNH ĐỘNG -->
    <td>
        <?php if ($row['role'] != 1) { ?>

        <a class="action-btn btn-edit"
        href="action_user.php?id=<?= $row['id'] ?>&action=toggle">

      <?php if ($row['trang_thai'] == 1) { ?>
       <i class="fa-solid fa-lock"></i> 
      <?php } else { ?>
       <i class="fa-solid fa-lock-open"></i>
      <?php } ?>

    </a>

        <a class="action-btn btn-delete"
           href="action_user.php?id=<?= $row['id'] ?>&action=delete"
           onclick="return confirm('Xóa tài khoản?')">
           <i class="fa-solid fa-trash"></i>
        </a>

        <?php } else { ?>
            <span style="color:#999;">Admin</span>
        <?php } ?>
    </td>
</tr>
<?php } ?>

</table>
</div>
</section>

</body>
</html>

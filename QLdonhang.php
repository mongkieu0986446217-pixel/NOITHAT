    <?php
include "connect.php";
// ===== UPDATE =====
if(isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE don_hang SET trang_thai=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    exit;
}

// ===== DELETE =====
if(isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);

    $conn->query("DELETE FROM chi_tiet_don_hang WHERE don_hang_id = $id");

    $stmt = $conn->prepare("DELETE FROM don_hang WHERE id=?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        echo "success";
    } else {
        echo "error";
    }
    exit;
}

// ===== LOAD DATA (LUÔN LUÔN Ở CUỐI) =====
$sql = "SELECT * FROM don_hang ORDER BY ngay_dat DESC";
$result = mysqli_query($conn, $sql);
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="Qldonhang6.css">
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
    <i class="fa-solid fa-file-invoice" style="font-size: 20px;"></i> Đơn hàng
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

<h2 class="title">Quản lí đơn hàng</h2>

<div class="filter-bar">
  <input type="text" class="search-input" placeholder="Tìm kiếm sản phẩm...">

<div class="table-wrapper">
<table class="table-account">

<thead>
<tr>
  <th>ID</th>
  <th>Khách</th>
  <th>SĐT</th>
  <th>Địa chỉ</th>
  <th>Tổng tiền</th>
  <th>Trạng thái</th>
  <th>Thanh toán</th>
  <th>Ngày đặt</th>
  <th>Hành động</th>
</tr>
</thead>

<tbody>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr id="order-<?= $row['id'] ?>">

<td><?= $row['id'] ?></td>
<td><?= $row['ten_khach'] ?></td>
<td><?= $row['sdt'] ?></td>
<td><?= $row['dia_chi'] ?></td>
<td><?= number_format($row['tong_tien']) ?> đ</td>

<td>
  <select class="status-select" data-id="<?= $row['id'] ?>">
    <option value="Chờ thanh toán" <?= $row['trang_thai']=="Chờ thanh toán"?"selected":"" ?>>Chờ thanh toán</option>
    <option value="Chờ xác nhận" <?= $row['trang_thai']=="Chờ xác nhận"?"selected":"" ?>>Chờ xác nhận</option>
    <option value="Đang giao" <?= $row['trang_thai']=="Đang giao"?"selected":"" ?>>Đang giao</option>
    <option value="Hoàn thành" <?= $row['trang_thai']=="Hoàn thành"?"selected":"" ?>>Hoàn thành</option>
    <option value="Đã hủy" <?= $row['trang_thai']=="Đã hủy"?"selected":"" ?>>Đã hủy</option>
  </select>
</td>

<td><?= $row['phuong_thuc_tt'] ?></td>
<td><?= $row['ngay_dat'] ?></td>

<td>
  <button class="btn-delete" data-id="<?= $row['id'] ?>">
    <i class="fa-solid fa-trash"></i>
  </button>
</td>

</tr>
<?php } ?>
</tbody>

</table>
</div>

</section>
<script>
document.querySelectorAll(".status-select").forEach(select => {
  select.addEventListener("change", function() {

    let id = this.dataset.id;
    let status = this.value;

    fetch("Qldonhang.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
  },
  body: `id=${id}&status=${encodeURIComponent(status)}`
})
.then(() => {
  console.log("Đã cập nhật trạng thái");
});
});
});
</script>
<script>
document.querySelectorAll(".btn-delete").forEach(btn => {
  btn.addEventListener("click", function() {

    let id = this.dataset.id;

    if(confirm("Xóa đơn này?")) {
      fetch("Qldonhang.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `delete_id=${id}`
      })
      .then(res => res.text())
      .then(data => {
  console.log("SERVER:", data); // 👈 xem nó trả gì

  if(data.trim() === "success") {
    alert("Đã xóa!");
    document.getElementById("order-" + id).remove();
  } else {
    alert("Lỗi: " + data);
  }
});
    }

  });
});
</script>
<script>
document.querySelector(".search-input").addEventListener("keyup", function() {
  let keyword = this.value.toLowerCase();

  let rows = document.querySelectorAll(".table-account tbody tr");

  rows.forEach(row => {
    let text = row.innerText.toLowerCase();

    if (text.includes(keyword)) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
});
</script>
</body>
</html>

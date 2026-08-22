<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="Qlsanpham1.css">
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

  <!-- ===== PRODUCTS ===== -->
    <h2 class="title">Quản lý sản phẩm</h2>

        <div class="product-header">

  <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm..." class="search-product">

<select id="categoryFilter" class="filter-select">
  <option value="all">Tất cả danh mục</option>
  <option value="1">Sofa</option>
  <option value="2">Đèn</option>
  <option value="3">Giường Ngủ</option>
  <option value="4">Tủ - Kệ</option>
  <option value="5">Ghế - Bàn</option>
</select>


  <select id="statusFilter" class="filter-select">
  <option value="all">Tất cả trạng thái</option>
  <option value="conhang">Còn hàng</option>
  <option value="hethang">Hết hàng</option>
</select>

      <button class="btn-add" onclick="openForm()">
  <i class="fa-solid fa-plus"></i> Thêm sản phẩm
      </button>

    </div>

<div class="table-wrapper">
  <table class="table-account">

    <thead>
      <tr>
        <th><input type="checkbox" id="checkAll"></th>
        <th>ID</th>
        <th>Tên</th>
        <th>Danh mục</th>
        <th>Giá</th>
        <th>Ảnh</th>
        <th>Số Lượng</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
      </tr>
    </thead>

    <tbody id="productList"></tbody>

  </table>
</div>

</section>

<!-- ==================== LOAD SẢN PHẨM ==================== -->
<script>
function loadSanPham() {
  let keyword = document.getElementById("searchInput") ? document.getElementById("searchInput").value : "";
  let category = document.getElementById("categoryFilter") ? document.getElementById("categoryFilter").value : "all";
  let status = document.getElementById("statusFilter") ? document.getElementById("statusFilter").value : "all";

  fetch(`http://localhost/WNT/getSanPham.php?keyword=${keyword}&category=${category}&status=${status}`)
    .then(res => res.json())
    .then(data => {
      let html = "";

      data.forEach(sp => {
        let trangThai = sp.trang_thai == 1 
          ? `<span class="status active">Còn hàng</span>`
          : `<span class="status inactive">Hết hàng</span>`;

        html += `
          <tr>
            <td><input type="checkbox" class="checkItem" value="${sp.id}"></td>
            <td>${sp.id}</td>
            <td>${sp.ten_san_pham}</td>
            <td>${sp.ten_danh_muc}</td>
            <td style="font-weight:500; color:#000;">${Number(sp.gia).toLocaleString()} đ</td>
            <td><img src="${sp.anh_san_pham}" width="70" style="border-radius:6px; object-fit:cover; box-shadow:0 2px 6px rgba(0,0,0,0.1);"></td>
            <td>${sp.so_luong}</td>
            <td>${trangThai}</td>
            <td>
              <div style="display:flex; gap:10px; justify-content:center;">

     <div onclick="sua(${sp.id})"
     style="width:40px;height:40px;background:#f3e9e3;display:flex;justify-content:center;align-items:center;border-radius:10px;cursor:pointer;">
  <i class="fa-solid fa-pen" style="color:#6B3F24;"></i>
</div>
                <div onclick="xoa(${sp.id})" style="width:40px;height:40px;background:#e8d5c4;display:flex;justify-content:center;align-items:center;border-radius:10px;cursor:pointer;">
                  <i class="fa-solid fa-trash" style="color:#6B3F24;"></i>
                </div>
              </div>
            </td>
          </tr>`;
      });

      document.getElementById("productList").innerHTML = html || 
        `<tr><td colspan="9" style="text-align:center;padding:40px;color:#888;">Không có dữ liệu sản phẩm</td></tr>`;
    })
    .catch(err => console.error("Lỗi load sản phẩm:", err));
}
</script>

<!-- ==================== HÀM XÓA - SỬA ==================== -->
<script>
function xoa(id) {

  // 🔥 LẤY CHECKBOX ĐANG CHỌN
  let checked = document.querySelectorAll(".checkItem:checked");

  // 👉 Nếu có tick nhiều → xóa nhiều
  if (checked.length > 1) {

    let count = checked.length;

    if (!confirm(`Bạn có chắc chắn muốn xóa ${count} sản phẩm này không?`)) return;

    let ids = [];
    checked.forEach(cb => ids.push(cb.value));

    fetch("http://localhost/WNT/xoaNhieuSanPham.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "ids=" + ids.join(",")
    })
    .then(res => res.text())
    .then(data => {
      if (data === "success") {
        alert(`Đã xóa ${count} sản phẩm!`);
        loadSanPham();
      } else {
        alert("Lỗi: " + data);
      }
    });

  } 
  // 👉 Nếu không tick hoặc chỉ 1 cái → xóa bình thường
  else {

    if(confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
      fetch("http://localhost/WNT/xoaSanPham.php?id=" + id)
        .then(res => res.text())
        .then(data => {
          if(data === "success"){
            alert("Đã xóa thành công!");
            loadSanPham();
          } else {
            alert("Lỗi: " + data);
          }
        });
    }

  }
}

function sua(id) {
  fetch("http://localhost/WNT/getSanPham.php")
    .then(res => res.json())
    .then(data => {
      let sp = data.find(item => item.id == id);

      
      document.getElementById("editForm").style.display = "flex";
      document.getElementById("editID").value = id;

      document.getElementById("editGia").value = sp.gia;
      document.getElementById("editSoLuong").value = sp.so_luong;
      document.getElementById("editTrangThai").value = sp.trang_thai;
    });
}
</script>

<script>
window.onload = function() {
  loadSanPham();

  document.getElementById("searchInput").addEventListener("keyup", loadSanPham);

  document.getElementById("categoryFilter").addEventListener("change", loadSanPham);
  document.getElementById("statusFilter").addEventListener("change", loadSanPham);
};
</script>
<!-- ==================== HÀM FORM (THÊM - SỬA) ==================== -->
<script>
function openForm() {
  document.getElementById("popupForm").style.display = "flex";
}

function closeForm() {
  document.getElementById("popupForm").style.display = "none";
}
// ==================== THÊM HÀM NÀY VÀO ĐÂY ====================
function closeEditForm() {
  document.getElementById("editForm").style.display = "none";
}

function themSanPham() {
  let ten = document.getElementById("tenSP").value;
  let gia = document.getElementById("giaSP").value;
  let anh = document.getElementById("anhSP").value;
  let so_luong = document.getElementById("soLuongSP").value;

  fetch("http://localhost/WNT/themsanpham.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: `ten=${ten}&gia=${gia}&anh=${anh}&so_luong=${so_luong}`
  })
  .then(res => res.text())
  .then(data => {
    alert(data);
    if(data === "success"){
      closeForm();
      loadSanPham();
    }
  });
}

function suaSanPham() {
  let id = document.getElementById("editID").value;
  let gia = document.getElementById("editGia").value;
  let so_luong = document.getElementById("editSoLuong").value;
  

  // ✅ LOGIC CHUẨN
  let trang_thai = so_luong > 0 ? 1 : 0;

  fetch("http://localhost/WNT/suasanpham.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: `id=${id}&gia=${gia}&so_luong=${so_luong}&trang_thai=${trang_thai}`
  })
  .then(res => res.text())
  .then(data => {
    if(data === "success"){
      closeEditForm();
      loadSanPham();
      alert("Cập nhật thành công!");
    } else {
      alert("Lỗi: " + data);
    }
  });
}
</script>

<!-- ===== xóa===== -->
<div id="popupForm" style="
  display:none;
  position:fixed;
  top:0; left:0;
  width:100%; height:100%;
  background: rgba(0,0,0,0.6);
  justify-content:center;
  align-items:center;
  z-index:9999;
">

  <div style="
    background:#fff;
    padding:30px;
    border-radius:15px;
    width:420px;
    box-shadow:0 10px 30px rgba(0,0,0,0.3);
    text-align:center;
  ">

    <h3 style="margin-bottom:20px; color:#333;">Thêm sản phẩm</h3>
    
    <input type="text" id="tenSP" placeholder="Tên sản phẩm" style="width:100%; padding:10px; margin-bottom:10px; border-radius:8px; border:1px solid #ccc;">
    
    <input type="number" id="giaSP" placeholder="Giá" style="width:100%; padding:10px; margin-bottom:10px; border-radius:8px; border:1px solid #ccc;">
    
    <input type="number" id="soLuongSP" placeholder="Số lượng" style="width:100%; padding:10px; margin-bottom:10px; border-radius:8px; border:1px solid #ccc;">
    
    <input type="text" id="anhSP" placeholder="Link ảnh" style="width:100%; padding:10px; margin-bottom:15px; border-radius:8px; border:1px solid #ccc;">

    <div style="display:flex; justify-content:space-between; gap:10px;">

      <button onclick="themSanPham()" style="
  flex:1;
  padding:10px;
  background:#6B3F24;
  color:white;
  border:none;
  border-radius:8px;
  font-weight:bold;
  cursor:pointer;
">
  Thêm
</button>

<button onclick="closeForm()" style="
  flex:1;
  padding:10px;
  background:#9c7b63;
  color:white;
  border:none;
  border-radius:8px;
  font-weight:bold;
  cursor:pointer;
">
  Đóng
</button>

    </div>

  </div>
</div>
<!-- ===== form sửa ===== -->
<div id="editForm" style="
  display:none;
  position:fixed;
  top:0; left:0;
  width:100%; height:100%;
  background: rgba(0,0,0,0.6);
  justify-content:center;
  align-items:center;
  z-index:9999;
">

  <div style="
    background:#fff;
    padding:30px;
    border-radius:15px;
    width:420px;
    box-shadow:0 10px 30px rgba(0,0,0,0.3);
    text-align:center;
  ">

    <h3 style="margin-bottom:20px; color:#333;">Sửa sản phẩm</h3>

    <input type="hidden" id="editID">

    <input type="number" id="editGia" placeholder="Giá"
      style="width:100%; padding:10px; margin-bottom:10px; border-radius:8px; border:1px solid #ccc;">

    <input type="number" id="editSoLuong" placeholder="Số lượng"
      style="width:100%; padding:10px; margin-bottom:10px; border-radius:8px; border:1px solid #ccc;">

    

    <div style="display:flex; gap:10px;">

      <!-- LƯU -->
      <button onclick="suaSanPham()" style="
        flex:1;
        padding:10px;
        background:#6B3F24;
        color:white;
        border:none;
        border-radius:8px;
        font-weight:bold;
        cursor:pointer;
      ">
        Lưu
      </button>

      <!-- ĐÓNG -->
      <button onclick="closeEditForm()" style="
        flex:1;
        padding:10px;
        background:#9c7b63;
        color:white;
        border:none;
        border-radius:8px;
        font-weight:bold;
        cursor:pointer;
      ">
        Đóng
      </button>

    </div>

  </div>
</div>

    <?php
    include "connect.php";
    ?>
</body>
</html>

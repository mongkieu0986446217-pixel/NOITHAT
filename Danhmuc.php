<?php
include "connect.php";

// 🔥 XỬ LÝ XÓA DANH MỤC
if (isset($_POST['action']) && $_POST['action'] == 'delete') {

    $id = $_POST['id'];

    // kiểm tra còn sản phẩm không
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM san_pham WHERE danh_muc_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['total'] > 0) {
        echo "has_product";
        exit;
    }

    // thực hiện xóa
    $stmt = $conn->prepare("DELETE FROM danh_muc WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    exit;
}
// 🔥 THÊM DANH MỤC
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $name  = trim($_POST['name'] ?? '');
    $order = (int)($_POST['order'] ?? 1);
    $link  = trim($_POST['link'] ?? '');

    // Nếu không có link thì tự tạo
    if (empty($link)) {
        $link = strtolower(str_replace(' ', '-', $name));
        $link = preg_replace('/[^a-z0-9-]/', '', $link);
    }

    if (empty($name)) {
        echo "error";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO danh_muc (ten_danh_muc, lien_ket, thu_tu) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $link, $order);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    exit;
}

// LẤY THỨ TỰ LỚN NHẤT để làm giá trị mặc định cho modal Thêm
$max_order_result = mysqli_query($conn, "SELECT MAX(thu_tu) as max_order FROM danh_muc");
$max_row = mysqli_fetch_assoc($max_order_result);
$default_order = ($max_row['max_order'] !== null) ? (int)$max_row['max_order'] + 1 : 1;

// 🔍 SEARCH
$keyword = $_GET['keyword'] ?? '';

if (!empty($keyword)) {
    $stmt = $conn->prepare("SELECT * FROM danh_muc WHERE ten_danh_muc LIKE ? ORDER BY thu_tu ASC");
    $like = "%$keyword%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM danh_muc ORDER BY thu_tu ASC";
    $result = mysqli_query($conn, $sql);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="Danhmuc.css">
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
      <a href="Danhmuc.php" class="active">
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


<h2 class="title">Danh mục</h2>


<div class="filter-bar">
  <input type="text" class="search-input" placeholder="Tìm kiếm sản phẩm...">

  <button class="btn-add" id="openAdd">
  <i class="fa-solid fa-plus"></i> Thêm danh mục
</button>
</div>

<div class="table-wrapper">
<table class="table-account">

<thead>
<tr>
    
    <th>ID</th>
    <th>Tên danh mục</th>
    <th>Liên kết</th>
    <th>Thứ tự</th>
    <th>Hành động</th>
</tr>
</thead>

<tbody>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    
    <td><?= $row['id'] ?></td>
    <td><?= $row['ten_danh_muc'] ?></td>
    <td><?= $row['lien_ket'] ?></td>
    <td><?= $row['thu_tu'] ?></td>

    <td>
        <button class="action-btn btn-edit open-edit"
        data-id="<?= $row['id'] ?>"
        data-name="<?= htmlspecialchars($row['ten_danh_muc']) ?>"
        data-order="<?= $row['thu_tu'] ?>">
  <i class="fa-solid fa-pen"></i>
</button>

        <button class="action-btn btn-delete delete-btn"
        data-id="<?= $row['id'] ?>">
  <i class="fa-solid fa-trash"></i>
</button>
    </td>
</tr>
<?php } ?>
</tbody>

</table>
</div>
<div id="editModal" class="modal">
  <div class="modal-content">
    <h3>Sửa danh mục</h3>

    <input type="hidden" id="edit-id">

    <input type="text" id="edit-name" placeholder="Tên danh mục">
    <!-- SỬA Ở ĐÂY -->
    <input type="number" id="edit-order" placeholder="Thứ tự">

    <div class="modal-actions">
      <button id="saveEdit" class="btn-save">Lưu</button>
      <button id="closeModal" class="btn-close">Đóng</button>
    </div>
  </div>
</div>

<!-- MODAL THÊM DANH MỤC -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <h3>Thêm danh mục</h3>

    <input type="text" id="add-name" placeholder="Tên danh mục" required>
    
    <!-- ĐÃ SỬA: Dùng $default_order -->
    <input type="number" id="add-order" placeholder="Thứ tự" value="<?= $default_order ?>">

    <!-- Thêm trường Liên kết -->
    <input type="text" id="add-link" placeholder="Liên kết (tự động tạo)" readonly>

    <div class="modal-actions">
      <button id="saveAdd" class="btn-save">Thêm</button>
      <button id="closeAddModal" class="btn-close">Đóng</button>
    </div>
  </div>
</div>
<script>
const modal = document.getElementById("editModal");
const editId = document.getElementById("edit-id");
const editName = document.getElementById("edit-name");
const editOrder = document.getElementById("edit-order");

// MỞ MODAL
document.querySelectorAll(".open-edit").forEach(btn => {
  btn.addEventListener("click", function() {
    modal.style.display = "flex";

    editId.value = this.dataset.id;
    editName.value = this.dataset.name;
    editOrder.value = this.dataset.order;
  });
});

// ĐÓNG MODAL
document.getElementById("closeModal").onclick = () => {
  modal.style.display = "none";
};

// LƯU (gửi AJAX)
document.getElementById("saveEdit").onclick = () => {
  fetch("update_danhmuc.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `id=${editId.value}&name=${encodeURIComponent(editName.value)}&order=${editOrder.value}`
  })
  .then(res => res.text())
  .then(() => {
    alert("Cập nhật thành công!");
    location.reload();
  });
};
</script>
<script>
document.querySelectorAll('.delete-btn').forEach(btn => {
  btn.addEventListener('click', function() {

    let id = this.dataset.id;

    if(confirm("Bạn chắc chắn muốn xóa?")) {
      fetch("Danhmuc.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id=${id}&action=delete`
      })
      .then(res => res.text())
      .then(data => {

        if(data === "success") {
          alert("Xóa thành công!");
          location.reload();
        }

        else if(data === "has_product") {
          alert("❌ Danh mục còn sản phẩm, không thể xóa!");
        }

        else {
          alert("❌ Xóa thất bại!");
        }

      });
    }

  });
});
</script>
<script>
// ==================== MODAL THÊM DANH MỤC ====================
const addModal = document.getElementById("addModal");
const addName = document.getElementById("add-name");
const addOrder = document.getElementById("add-order");
const addLink = document.getElementById("add-link");

// Mở modal
document.getElementById("openAdd").onclick = () => {
  addModal.style.display = "flex";
  addName.value = "";
  addLink.value = "";           // reset liên kết
  
  // ✅ Sửa ở đây: Dùng giá trị mặc định từ PHP thay vì cứng "1"
  addOrder.value = <?= $default_order ?>;   

  addName.focus();
};

// Tự động tạo liên kết khi gõ tên
addName.addEventListener("input", function() {
  let name = this.value.trim();
  let link = name.toLowerCase()
                   .normalize("NFD")
                   .replace(/[\u0300-\u036f]/g, "")  
                   .replace(/[^a-z0-9\s-]/g, "")     
                   .replace(/\s+/g, '-');            
  addLink.value = link;
});

// Đóng modal
document.getElementById("closeAddModal").onclick = () => {
  addModal.style.display = "none";
};

// Lưu (Thêm danh mục)
document.getElementById("saveAdd").onclick = () => {
  const name = addName.value.trim();
  const order = addOrder.value.trim();
  const link = addLink.value.trim();

  if (name === "") {
    alert("Vui lòng nhập tên danh mục!");
    addName.focus();
    return;
  }

  if (order === "" || isNaN(order)) {
    alert("Vui lòng nhập thứ tự hợp lệ!");
    addOrder.focus();
    return;
  }

  fetch("Danhmuc.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `name=${encodeURIComponent(name)}&order=${order}&link=${encodeURIComponent(link)}&action=add`
  })
  .then(res => res.text())
  .then(data => {
    if (data === "success") {
      alert("✅ Thêm danh mục thành công!");
      location.reload();
    } else {
      alert("❌ Thêm thất bại! Vui lòng thử lại.");
    }
  })
  .catch(err => {
    console.error(err);
    alert("❌ Có lỗi xảy ra khi thêm danh mục.");
  });
};
</script>
</body>
</html>

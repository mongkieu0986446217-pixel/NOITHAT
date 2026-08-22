<?php
include "connect.php";

$where = "1";

// lọc danh mục
if(isset($_GET['category']) && $_GET['category'] != "all"){
    $category = $_GET['category'];
    $where .= " AND sp.danh_muc_id = '$category'";
}

// tìm kiếm
if(isset($_GET['keyword']) && $_GET['keyword'] != ""){
    $keyword = $_GET['keyword'];
    $where .= " AND sp.ten_san_pham LIKE '%$keyword%'";
}

// lọc trạng thái
if(isset($_GET['status'])){
    if($_GET['status'] == "conhang"){
        $where .= " AND sp.trang_thai = 1";
    }
    if($_GET['status'] == "hethang"){
        $where .= " AND sp.trang_thai = 0";
    }
}

// 🔥 hàng mới
if(isset($_GET['hangmoi']) && $_GET['hangmoi'] == "1"){
    $where .= " AND sp.hang_moi = 1";
}

// 🔥 gợi ý
if(isset($_GET['goiy']) && $_GET['goiy'] == "1"){
    $where .= " AND sp.goi_y = 1";
}

// SQL chính
$sql = "SELECT sp.id, sp.ten_san_pham, sp.gia, sp.anh_san_pham, sp.trang_thai,
               sp.so_luong,
               dm.ten_danh_muc
        FROM san_pham sp
        JOIN danh_muc dm ON sp.danh_muc_id = dm.id
        WHERE $where";

// ================= SẮP XẾP =================
if(isset($_GET['custom_order'])){
    $order = $_GET['custom_order'];

    // chỉ lấy đúng ID
    $sql .= " AND sp.id IN ($order)";
    $sql .= " ORDER BY FIELD(sp.id, $order)";
}
else if(isset($_GET['limit'])){
    $sql .= " ORDER BY sp.id DESC LIMIT " . $_GET['limit'];
}
else {
    $sql .= " ORDER BY sp.id ASC";
}
// ===========================================

$result = $conn->query($sql);

if(!$result){
    die("Lỗi SQL: " . $conn->error);
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
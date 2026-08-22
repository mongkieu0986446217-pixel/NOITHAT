<?php
include "connect.php";

$where = "sp.trang_thai = 1"; // chỉ lấy còn hàng

// lọc danh mục (nếu có)
if(isset($_GET['category']) && $_GET['category'] != "all"){
    $category = (int)$_GET['category'];
    $where .= " AND sp.danh_muc_id = $category";
}

// custom order (nếu có)
if(isset($_GET['custom_order'])){
    $order = $_GET['custom_order'];
    $sql = "SELECT * FROM san_pham sp 
            WHERE $where AND sp.id IN ($order)
            ORDER BY FIELD(sp.id, $order)";
}
else {
    $sql = "SELECT * FROM san_pham sp 
            WHERE $where 
            ORDER BY sp.id DESC";
}

$result = $conn->query($sql);

$data = [];
while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
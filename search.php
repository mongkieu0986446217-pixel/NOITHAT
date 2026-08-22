<?php
include "connect.php";

$keyword = trim($_GET['keyword'] ?? '');

if($keyword == ""){
    exit();
}

$sql = "SELECT * FROM san_pham WHERE ten_san_pham LIKE ? LIMIT 10";
$stmt = $conn->prepare($sql);

$search = "%" . $keyword . "%";
$stmt->bind_param("s", $search);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){
    echo "<p>Không tìm thấy sản phẩm</p>";
    exit();
}

while($row = $result->fetch_assoc()){
    echo '
    <a href="chitiet.php?id='.$row['id'].'" class="product-card">
        <img src="'.htmlspecialchars($row['anh_san_pham']).'">
        <h3>'.htmlspecialchars($row['ten_san_pham']).'</h3>
        <p class="price">'.number_format($row['gia']).'đ</p>
    </a>
    ';
}
?>
<?php
session_start();

/* ================== KẾT NỐI DB ================== */
$conn = new mysqli("localhost", "root", "", "noithat");
if ($conn->connect_error) {
    die("Lỗi kết nối");
}

/* ================== CHECK LOGIN ================== */
if(!isset($_SESSION['user_id'])){
    echo "<script>
        alert('Vui lòng đăng nhập!');
        window.location.href='login.php';
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

/* ================== ĐẾM SỐ ĐƠN ================== */
$sql_count = "SELECT COUNT(*) as total FROM don_hang WHERE taikhoan_id=?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $user_id); // nếu user_id là chữ → đổi thành "s"
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$total_orders = $row_count['total'];

/* ================== LẤY ĐƠN HÀNG ================== */
$sql = "SELECT * FROM don_hang WHERE taikhoan_id=? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // nếu user_id là chữ → đổi thành "s"
$stmt->execute();
$result = $stmt->get_result();
// Đếm đơn hàng của user
$total_orders = 0;

if(isset($_SESSION['user_id'])){
    $uid = $_SESSION['user_id'];

    $sql = "SELECT COUNT(*) as total FROM don_hang WHERE taikhoan_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid); // nếu là chuỗi → đổi "s"
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    $total_orders = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đơn hàng của tôi</title>

<style>
body{
    font-family: Arial;
    background:#f5f5f5;
    padding:20px;
}

.back-home{
    display:inline-block;
    margin-bottom:15px;
    font-size:22px;
    color:black;
    text-decoration:none;
}

.title{
    font-size:22px;
    margin-bottom:20px;
}

.order-box{
    background:white;
    border-radius:10px;
    padding:15px;
    margin-bottom:20px;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
}

.order-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.status{
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}

.product{
    display:flex;
    gap:12px;
    margin:10px 0;
    border-top:1px solid #eee;
    padding-top:10px;
}

.product img{
    width:70px;
    height:70px;
    object-fit:cover;
    border-radius:8px;
}

.product-name{
    font-weight:bold;
}

.product-price{
    color:#666;
    font-size:14px;
}

.total{
    text-align:right;
    font-weight:bold;
    color:#e53935;
}

.btn-view{
    padding:6px 12px;
    background:#6b442d;
    color:white;
    border-radius:6px;
    text-decoration:none;
}
</style>
</head>

<body>

<a href="users.php" class="back-home">⬅</a>

<!-- ✅ HIỂN THỊ SỐ ĐƠN -->
<div class="title">
    📦 Đơn hàng của bạn (<?= $total_orders ?>)
</div>

<?php
if($total_orders == 0){
    echo "<p>Chưa có đơn hàng nào</p>";
}
?>

<?php while($row = $result->fetch_assoc()) { ?>

<div class="order-box">

    <div class="order-top">
        <div>
            <b>Mã đơn:</b> #<?= $row['id'] ?><br>
            <small><?= $row['ngay_dat'] ?></small>
        </div>

        <div>
            <span class="status"><?= $row['trang_thai'] ?></span>
            <a href="TT_don_hang.php?id=<?= $row['id'] ?>" class="btn-view">Xem</a>
        </div>
    </div>

    <!-- SẢN PHẨM -->
    <?php
    $sql_ct = "
    SELECT ctdh.*, sp.ten_san_pham, sp.anh_san_pham
    FROM chi_tiet_don_hang ctdh
    JOIN san_pham sp ON ctdh.san_pham_id = sp.id
    WHERE ctdh.don_hang_id = ?
    ";

    $stmt_ct = $conn->prepare($sql_ct);
    $stmt_ct->bind_param("i", $row['id']);
    $stmt_ct->execute();
    $ct = $stmt_ct->get_result();

    while($item = $ct->fetch_assoc()){
    ?>

    <div class="product">
        <img src="<?= $item['anh_san_pham'] ?>">
        <div>
            <div class="product-name"><?= $item['ten_san_pham'] ?></div>
            <div class="product-price">
                <?= $item['so_luong'] ?> × <?= number_format($item['gia']) ?>đ
            </div>
        </div>
    </div>

    <?php } ?>

    <div class="total">
        Tổng tiền: <?= number_format($row['tong_tien']) ?>đ
    </div>

</div>

<?php } ?>

</body>
</html>
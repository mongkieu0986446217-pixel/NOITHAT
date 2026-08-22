<?php 
session_start();
include "connect.php";

// lấy id từ URL
$id = $_GET['id'] ?? 0;

if($id <= 0){
    die("Thiếu ID đơn hàng!");
}

// lấy trạng thái từ DB
$stmt = $conn->prepare("SELECT trang_thai FROM don_hang WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if(!$order){
    die("Không tìm thấy đơn hàng!");
}

// map text → số
switch($order['trang_thai']){
    case "Chờ thanh toán": $status = 4; break;
    case "Chờ xác nhận": $status = 1; break;
    case "Đang giao": $status = 2; break;
    case "Hoàn thành": $status = 3; break;
    case "Đã hủy": $status = 0; break;
    default: $status = 1;
}

// tính progress
$progress = 0;
if($status == 4) $progress = 20;
elseif($status == 1) $progress = 40;
elseif($status == 2) $progress = 60;
elseif($status == 3) $progress = 80;
elseif($status == 0) $progress = 100;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trạng thái đơn hàng</title>

<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: #f4f6f9;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
}

.box {
  width: 500px;
  background: white;
  padding: 45px 40px;
  border-radius: 18px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

h2 {
   font-size: 20px;       /* nhỏ tiêu đề */
  margin-bottom: 20px;
}

/* Timeline */
.timeline {
  display: flex;
  justify-content: space-between;
  position: relative;
  margin: 30px 0;
}

/* Line */
.timeline::before {
  content: "";
  position: absolute;
  top: 18px;
  left: 0;
  right: 0;
  height: 4px;
  background: #ddd;
  z-index: 0;
}
.timeline::after {
  content: "";
  position: absolute;
  top: 18px;
  left: 0;
  height: 4px;
  background: #28a745;
  z-index: 0;
  width: var(--progress);
  transition: 0.4s;
}
/* Step */
.step {
  text-align: center;
  position: relative;
  z-index: 1;
  width: 20%;
}

/* Circle */
.circle {
  width: 35px;
  height: 35px;
  background: #ddd;
  border-radius: 50%;
  line-height: 35px;
  margin: auto;
  color: white;
  font-size: 16px;
}

/* Active step */
.active .circle {
  background: #28a745;
}

/* Done step */
.done .circle {
  background: #28a745;
}

/* Text */
.label {
  margin-top: 8px;
  font-size: 13px;
}

/* Button */
.btn {
  display: block;
  width: 70%;
  margin: 20px auto 0;
  padding: 10px;
  text-align: center;
  border-radius: 8px;
  background: #6b442d;
  color: white;
  text-decoration: none;
}

.btn:hover {
  background: #5a331d
}

</style>

</head>

<body>

<div class="box">

  <h2>THEO DÕI ĐƠN HÀNG</h2>


<div class="timeline <?= $status == 0 ? 'cancel' : '' ?>" style="--progress: <?= $progress ?>%">

<!-- Chờ thanh toán -->
<div class="step <?php if($status == 4) echo 'done'; ?>">
    <div class="circle">💰</div>
    <div class="label">Chờ thanh toán</div>
  </div>

<!-- Chờ xác nhận -->
<div class="step <?php if($status >= 1 && $status != 4) echo 'done'; ?>">
    <div class="circle">✔</div>
    <div class="label">Chờ xác nhận</div>
  </div>
<!-- Đang giao -->
<div class="step <?php if($status >= 2 && $status != 4) echo 'done'; ?>">
    <div class="circle">🚚</div>
    <div class="label">Đang giao</div>
  </div>

<!-- Hoàn thành -->
<div class="step <?php if($status >= 3 && $status != 4) echo 'done'; ?>">
    <div class="circle">🏠</div>
    <div class="label">Hoàn thành</div>
  </div>

  <!-- Đã hủy -->
  <div class="step <?php if($status == 0) echo 'done'; ?>">
    <div class="circle" style="background:#dc3545;">❌</div>
    <div class="label">Đã hủy</div>
  </div>

</div>

<?php if($status == 4){ ?>
  <p style="text-align:center;color:#f39c12;margin-top:10px;">
    Vui lòng thanh toán để xử lý đơn hàng
  </p>
<?php } ?>

<?php if($status == 0){ ?>
  <p style="text-align:center;color:#dc3545;margin-top:10px;">
    Đơn hàng đã bị hủy
  </p>
<?php } ?>


  <a href="users.php" class="btn">Quay về trang chủ</a>

</div>

</body>
</html>
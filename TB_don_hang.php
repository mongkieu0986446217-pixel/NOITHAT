<?php session_start();

// mặc định
if(!isset($_SESSION['order_status'])) {
    $_SESSION['order_status'] = 1; // 1: chờ
}

// nếu hủy
if(isset($_POST['cancel'])) {
    $_SESSION['order_status'] = 0; // 0: đã hủy
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Theo dõi đơn hàng</title>

<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: #f4f6f9;
  margin: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.box {
  width: 500px;
  background: #fff;
  padding: 45px 40px;
  border-radius: 18px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.08);
  text-align: center;
}

h2 {
  font-size: 24px;
  font-weight: 600;        /* 👈 giảm từ bold xuống 600 cho mượt */
  letter-spacing: 0.5px;   /* 👈 giảm lại */
  text-align: center;
}

/* Nút */
.btn {
  display: block;
  width: 85%;
  padding: 14px;   
  margin: 12px auto;        
  border-radius: 10px;   
  font-size: 16px;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: 0.2s;
  box-sizing: border-box;
  font-weight: 500;   /* 👈 nhẹ hơn */
  letter-spacing: 0.3px;
}

/* Màu */
.view {
  background: #6b442d;
  color: white;
}

.cancel {
  background: #6b442d;
  color: white;
}

.home {
  background: #6b442d;
  color: white;
}

/* Hover nhẹ */
.btn:hover {
  background: #5a331d
}
body {
  font-family: 'Segoe UI', Arial, sans-serif;
  -webkit-font-smoothing: antialiased;  /* 👈 mượt chữ */
  -moz-osx-font-smoothing: grayscale;
}
</style>

</head>

<body>

<div class="box">

  <h2>ĐANG CHỜ THANH TOÁN</h2>

  <a href="TT_don_hang.php" class="btn view">Xem trạng thái đơn hàng</a>

  <form method="POST" onsubmit="return confirmCancel()">
  <button name="cancel" class="btn cancel">Hủy đơn hàng</button>
  </form>

  <a href="users.php" class="btn home">Quay về trang chủ</a>

</div>
  <script>
  function confirmCancel() {
    return confirm("Bạn có chắc chắn muốn hủy đơn hàng không?");
  }
  </script>
  
</body>
</html>
<?php
session_start();

include "connect.php";

if(!isset($_SESSION['user_id'])){
    echo "<script>
        alert('Bạn cần đăng nhập để thanh toán!');
        window.location.href = 'trangchu.php';
    </script>";
    exit();
}


// ================== PHP XỬ LÝ ĐẶT HÀNG ==================
if(isset($_POST['dat_hang'])){

    mysqli_begin_transaction($conn); // ✅ bắt đầu transaction

    try {

        $cart_data = isset($_POST['cart_data']) ? $_POST['cart_data'] : '[]';
        $cart = json_decode($cart_data, true);

        if(empty($cart)){
            throw new Exception("Giỏ hàng trống!");
        }

        $user_id = $_SESSION['user_id'];
        $ten = $_POST['ten'];
        $sdt = $_POST['sdt'];
        $diachi = $_POST['diachi'];

        $tong = 0;

        // ================== TÍNH TỔNG ==================
       foreach($cart as $item){
    $sp_id = $item['id'];
    $sl = $item['quantity'];

$stmt = $conn->prepare("SELECT gia, so_luong FROM san_pham WHERE id=?");
$stmt->bind_param("i", $sp_id);
$stmt->execute();
$res_sp = $stmt->get_result();
$row_sp = $res_sp->fetch_assoc();

    if(!$row_sp){
        throw new Exception("Sản phẩm không tồn tại!");
    }

    if($row_sp['so_luong'] < $sl){
        throw new Exception("Sản phẩm ID $sp_id không đủ hàng!");
    }

    $gia = $row_sp['gia'];
    $tong += $gia * $sl;
}
        // ================== SHIP + GIẢM ==================
        $shipping_method = $_POST['shipping'] ?? 'standard';
        $shipping = ($shipping_method == "fast") ? 50000 : 30000;

        $tong += $shipping;

        $discount = isset($_POST['discount']) ? (int)$_POST['discount'] : 0;
        $tong -= $discount;

        if($tong < 0) $tong = 0;
        // ================== THANH TOÁN ==================
        $pay = $_POST['pay'] ?? 'cod';
        $pay_text = ($pay == "bank") ? "Chuyển khoản" : "Tiền mặt";

        $trangthai = ($pay == "bank") ? "Chờ thanh toán" : "Chờ xác nhận";

// ================== INSERT ĐƠN ==================
$stmtDH = $conn->prepare("INSERT INTO don_hang
(taikhoan_id, ten_khach, sdt, dia_chi, tong_tien, trang_thai, phuong_thuc_tt)
VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmtDH->bind_param("isssiss",
    $user_id,
    $ten,
    $sdt,
    $diachi,
    $tong,
    $trangthai,
    $pay_text
);

if(!$stmtDH->execute()){
    throw new Exception("Lỗi SQL: " . $stmtDH->error);
}

$donhang_id = $stmtDH->insert_id;

      // ================== CHI TIẾT + TRỪ KHO ==================
foreach($cart as $item){

    $sp_id = $item['id'];
    $sl = $item['quantity'];

    // ❌ KHÔNG cần query lại DB nữa

    // trừ kho
    $update = "UPDATE san_pham 
               SET so_luong = so_luong - ?
               WHERE id = ? AND so_luong >= ?";

    $stmt = $conn->prepare($update);
    $stmt->bind_param("iii", $sl, $sp_id, $sl);
    $stmt->execute();

    if($stmt->affected_rows == 0){
        throw new Exception("Sản phẩm ID $sp_id vừa hết hàng!");
    }

    // ⚠️ cần lấy giá (nếu chưa lưu trước đó)
    $stmtGia = $conn->prepare("SELECT gia FROM san_pham WHERE id=?");
    $stmtGia->bind_param("i", $sp_id);
    $stmtGia->execute();
    $resGia = $stmtGia->get_result();
    $rowGia = $resGia->fetch_assoc();

    $gia = $rowGia['gia'];

    // insert chi tiết
    $stmt2 = $conn->prepare("INSERT INTO chi_tiet_don_hang
        (don_hang_id, san_pham_id, so_luong, gia)
        VALUES (?, ?, ?, ?)");

    $stmt2->bind_param("iiii",
        $donhang_id,
        $sp_id,
        $sl,
        $gia
    );

    $stmt2->execute();

    if($stmt2->affected_rows == 0){
        throw new Exception("Lỗi lưu chi tiết đơn hàng!");
    }
}

mysqli_commit($conn); // ✅ thành công thì commit


// ================== SAU KHI ĐẶT HÀNG THÀNH CÔNG ==================
$_SESSION['order_status'] = 1;

echo "<script>
    alert('Đặt hàng thành công!');

    const currentUserId = '" . addslashes($_SESSION['user_id']) . "';

    // Xóa sản phẩm đã mua khỏi giỏ hàng
    let cart = JSON.parse(localStorage.getItem('cart_' + currentUserId)) || [];
    let buyNow = JSON.parse(localStorage.getItem('buyNow')) || [];
    let selectedCart = JSON.parse(localStorage.getItem('selectedCart')) || [];

    // Ưu tiên xóa theo buyNow hoặc selectedCart
    if (buyNow.length > 0) {
        const buyNowIds = buyNow.map(item => String(item.id));
        cart = cart.filter(item => !buyNowIds.includes(String(item.id)));
    } 
    else if (selectedCart.length > 0) {
        cart = cart.filter(item => !selectedCart.includes(String(item.id)));
    }

    localStorage.setItem('cart_' + currentUserId, JSON.stringify(cart));

    // Xóa tạm thời
    localStorage.removeItem('buyNow');
    localStorage.removeItem('selectedCart');

    window.location.href = 'TT_don_hang.php?id=".$donhang_id."';
</script>";

    } catch (Exception $e){

        mysqli_rollback($conn); // ❌ lỗi thì rollback

        echo "<script>alert('Lỗi: ".$e->getMessage()."');history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nội Thất LEM</title>

  <!-- FONT -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;700&display=swap" rel="stylesheet">

  <!-- ICON -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- CSS -->
  <link rel="stylesheet" href="thanhtoan2.css">
</head>
<body>

<!-- ================= HEADER ================= -->
<header class="header">

  <!-- TOP -->
  <div class="header-top">

    <!-- LOGO -->
    <div class="logo">
      <img src="img/logo.png" alt="Nội Thất LEM">
    </div>

    <!-- SEARCH -->
    <div class="search-box">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" placeholder="Tìm kiếm sản phẩm .....">
    </div>

    
      <!-- LOGIN -->
        <div class="header-right">
      <?php if(isset($_SESSION['user_name'])) { ?>
        <div class="user-box">
          <div class="user-name">
            <?php echo $_SESSION['user_name']; ?>
          </div>
          <a href="logout.php" class="logout">
            <span>Đăng xuất</span>
            <i class="fa-solid fa-right-from-bracket"></i>
          </a>
        </div>
      <?php } else { ?>
        <div class="auth-box">
          <a href="login.php">Đăng nhập</a><br>
          <a href="dangky.php">Đăng ký</a>
        </div>
      <?php } ?>
    </div>
    
    </div>
  </div>

  <!-- ================= MENU ================= -->
   <nav class="menu">
    <div class="menu-inner">
      <a href="users.php">TRANG CHỦ</a>
    <div class="dropdown">
      <a href="#">SẢN PHẨM</a>
    <div class="dropdown-menu">
      <a href="menusofa.php">SOFA</a>
      <a href="menuden.php">ĐÈN</a>
      <a href="menugiuongngu.php">GIƯỜNG NGỦ</a>
      <a href="menutuke.php">TỦ, KỆ</a>
      <a href="menubanvaghe.php">CÁC LOẠI BÀN VÀ GHẾ</a>
    </div>
    </div>
    <div class="dropdown">
      <a href="#">BỘ SƯU TẬP</a>
    <div class="dropdown-menu">
      <a href="bosutap.php">BỘ SƯU TẬP ELYSIAN</a>
      <a href="bosutap.php">BỘ SƯU TẬP HARMONY</a>
      <a href="bosutap.php">BỘ SƯU TẬP BlUE SKY</a>
      <a href="bosutap.php">BỘ SƯU TẬP LUXENEST</a>
      <a href="bosutap.php">BỘ SƯU TẬP ZENSPACE</a>
    </div>
    </div>
      <a href="setnoithat.php">SET NỘI THẤT</a>
      <a href="camhung.php">CẢM HỨNG</a>
      <a href="thietke.php">THIẾT KẾ</a>
    </div>
  </nav>
</header>

 <!-- ================= CHECKOUT ================= -->
<form method="POST" action="">
  
  <section class="checkout-container">

  <!-- LEFT -->
  <div class="checkout-left">

    <h3>Thông tin giao hàng</h3>

<!-- Họ tên -->
<input type="text" name="ten" placeholder="Họ và tên" required
pattern="^[A-Za-zÀ-ỹ\s]+$"
oninvalid="this.setCustomValidity('Vui lòng nhập họ tên (không chứa số)')"
oninput="this.setCustomValidity('')">

<!-- Số điện thoại -->
<input type="text" name="sdt" placeholder="Số điện thoại" required
pattern="(03|05|07|08|09)[0-9]{8}"
oninvalid="this.setCustomValidity('Số điện thoại phải đúng định dạng VN (10 số)')"
oninput="this.setCustomValidity('')">

<!-- Địa chỉ -->
<input type="text" name="diachi" placeholder="Địa chỉ" required
oninvalid="this.setCustomValidity('Vui lòng nhập địa chỉ')"
oninput="this.setCustomValidity('')">

<!-- Địa điểm -->
<div class="location">

  <select id="city" required
    oninvalid="this.setCustomValidity('Vui lòng chọn Tỉnh/TP')"
    oninput="this.setCustomValidity('')">
    <option value="">Tỉnh/TP</option>
    <option value="cantho">Cần Thơ</option>
    <option value="hcm">TP.HCM</option>
    <option value="hanoi">Hà Nội</option>
    <option value="danang">Đà Nẵng</option>
    <option value="binhduong">Bình Dương</option>
    <option value="angiang">An Giang</option>
    <option value="dongthap">Đồng Tháp</option>
    <option value="haiphong">Hải Phòng</option>
    <option value="khanhhoa">Khánh Hòa</option>
  </select>

  <select id="district" required
    oninvalid="this.setCustomValidity('Vui lòng chọn Quận/Huyện')"
    oninput="this.setCustomValidity('')">
    <option value="">Quận/Huyện</option>
  </select>

  <select id="ward" required
    oninvalid="this.setCustomValidity('Vui lòng chọn Phường/Xã')"
    oninput="this.setCustomValidity('')">
    <option value="">Phường/Xã</option>
  </select>

</div>

    <h3>Phương thức giao hàng</h3>

<select name="shipping" required>
  <option value="">Chọn phương thức</option>
  <option value="standard">Giao hàng tiêu chuẩn (30k)</option>
  <option value="fast">Giao nhanh (50k)</option>
</select>

    <h3>Phương thức thanh toán</h3>

    <div class="payment">
      <label>
        <div class="pay-left">
          <i class="fa-solid fa-building-columns"></i>
          <span>Chuyển khoản ngân hàng</span>
        </div>

        <!-- THÊM NGAY ĐÂY -->
<div id="bank-qr" style="display:none; margin-top:15px; text-align:center;">
    <p>Quét mã để thanh toán</p>
    <img src="img/QR.png" width="200">
</div>


        <input type="radio" name="pay" value="bank">
      </label>

      <label>
        <div class="pay-left">
          <i class="fa-solid fa-truck"></i>
          <span>Thanh toán khi nhận hàng (COD)</span>
        </div>
        <input type="radio" name="pay" value="cod" checked>
      </label>
    </div>

  </div>
  

  <!-- RIGHT -->
  <div class="checkout-right">

    <h3>Hàng hóa</h3>
    <div id="product-list"></div>

   <h4>Mã khuyến mãi</h4>
<div class="discount">
    <input type="text" id="coupon-code" placeholder="Nhập mã">
    <button type="button" onclick="applyCoupon(event)">Áp dụng</button>
</div>

<div id="coupon-suggest"></div> 

    <div class="summary">
  <p>Tổng tiền hàng <span id="total-price">0đ</span></p>
  <p>Phí vận chuyển <span id="shipping-fee">0đ</span></p>
  <h4>Tổng thanh toán <span id="final-price">0đ</span></h4>
</div>

    <input type="hidden" name="cart_data" id="cart_data">
    <input type="hidden" name="discount" id="discount_input">
    
    <!-- NÚT SUBMIT ĐẶT Ở ĐÂY -->
  <button type="submit" id="submit-btn" name="dat_hang" class="order-btn">
      Đặt hàng
  </button>

  </div>
</section>
</form>
<!-- ================= FOOTER ================= -->
<footer class="footer">
  <div class="footer-top">
    <div class="footer-container">

      <!-- CỘT 1 -->
      <div class="footer-col">
        <h3>NỘI THẤT LEM</h3>
        <p>
          Nội Thất LEM là thương hiệu đến từ Savimex với gần 20 năm kinh nghiệm
          trong việc sản xuất và xuất khẩu nội thất đạt chuẩn quốc tế.
        </p>

        <h3 class="mt">BẢN ĐỒ</h3>
        <div class="footer-map">
          <iframe
            src="https://www.google.com/maps?q=Savimex%20Vietnam&output=embed"
            width="100%"
            height="160"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>

      <!-- CỘT 2 -->
      <div class="footer-col">
        <h3>THÔNG TIN</h3>
        <ul>
          <li>Chính Sách Bán Hàng</li>
          <li>Chính Sách Giao Hàng & Lắp Đặt</li>
          <li>Chính Sách Bảo Hành & Bảo Trì</li>
          <li>Chính Sách Đổi Trả</li>
          <li>Chính Sách Đối Tác Bán Hàng</li>
        </ul>
      </div>

      <!-- CỘT 3 -->
      <div class="footer-col">
        <h3>THÔNG TIN LIÊN HỆ</h3>
        <p>📩 cskhnoithatlem@gmail.com</p>
        <p>📞 0367852599 (Hotline/Zalo)</p>
        <p>📞 0372351532 (Đội giao hàng)</p>
      </div>

    </div>

    <!-- ICON SOCIAL -->
    <div class="footer-social">
      <a href="#" title="Facebook"><svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g clip-path="url(#clip0_390_2352)">
      <circle cx="22" cy="22" r="22" fill="white"/>
      <path d="M22 0C9.84958 0 0 9.84958 0 22C0 34.1504 9.84958 44 22 44C34.1504 44 44 34.1504 44 22C44 9.84958 34.1504 0 22 0ZM27.2113 15.2029H23.9044C23.5125 15.2029 23.0771 15.7185 23.0771 16.4037V18.7917H27.2135L26.5879 22.1971H23.0771V32.4202H19.1744V22.1971H15.6337V18.7917H19.1744V16.7887C19.1744 13.915 21.1681 11.5798 23.9044 11.5798H27.2113V15.2029Z" fill="#126ECB"/>
      </g>
      <defs>
      <clipPath id="clip0_390_2352">
      <rect width="44" height="44" fill="white"/>
      </clipPath>
      </defs>
      </svg>
      </a>

      <a href="#" title="YouTube"><svg width="30" height="21" viewBox="0 0 30 21" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g clip-path="url(#clip0_390_2354)">
      <path d="M29.3374 3.27542C29.1654 2.64233 28.8297 2.06517 28.3639 1.6014C27.8981 1.13763 27.3183 0.803438 26.6824 0.6321C24.3544 0 14.9848 0 14.9848 0C14.9848 0 5.61471 0.0191333 3.28666 0.651233C2.65075 0.822582 2.07101 1.15679 1.60519 1.62058C1.13937 2.08437 0.80372 2.66156 0.631663 3.29467C-0.0725165 7.41277 -0.345681 13.6878 0.650999 17.6412C0.823074 18.2743 1.15873 18.8514 1.62455 19.3152C2.09037 19.779 2.6701 20.1131 3.306 20.2845C5.63405 20.9166 15.0039 20.9166 15.0039 20.9166C15.0039 20.9166 24.3736 20.9166 26.7015 20.2845C27.3375 20.1132 27.9172 19.779 28.3831 19.3152C28.8489 18.8514 29.1846 18.2743 29.3567 17.6412C30.0994 13.5172 30.3283 7.24605 29.3374 3.27542Z" fill="#FF0000"/>
      <path d="M12.0024 14.9403L19.7753 10.4582L12.0024 5.97614V14.9403Z" fill="white"/>
      </g>
      <defs>
      <clipPath id="clip0_390_2354">
      <rect width="30" height="21" fill="white"/>
      </clipPath>
      </defs>
      </svg>
      </a>

      <a href="#" title="Instagram"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g clip-path="url(#clip0_390_2357)">
      <path d="M18.375 0H5.625C2.5184 0 0 2.5184 0 5.625V18.375C0 21.4816 2.5184 24 5.625 24H18.375C21.4816 24 24 21.4816 24 18.375V5.625C24 2.5184 21.4816 0 18.375 0Z" fill="url(#paint0_radial_390_2357)"/>
      <path d="M18.375 0H5.625C2.5184 0 0 2.5184 0 5.625V18.375C0 21.4816 2.5184 24 5.625 24H18.375C21.4816 24 24 21.4816 24 18.375V5.625C24 2.5184 21.4816 0 18.375 0Z" fill="url(#paint1_radial_390_2357)"/>
      <path d="M12.0008 2.625C9.45478 2.625 9.13519 2.63616 8.13525 2.68163C7.13719 2.72738 6.45591 2.88534 5.85984 3.11719C5.24316 3.35662 4.72013 3.67697 4.19906 4.19822C3.67753 4.71937 3.35719 5.24241 3.117 5.85881C2.8845 6.45506 2.72634 7.13662 2.68144 8.13422C2.63672 9.13425 2.625 9.45394 2.625 12.0001C2.625 14.5463 2.63625 14.8648 2.68163 15.8647C2.72756 16.8628 2.88553 17.5441 3.11719 18.1402C3.35681 18.7568 3.67716 19.2799 4.19841 19.8009C4.71938 20.3225 5.24241 20.6436 5.85862 20.883C6.45516 21.1148 7.13653 21.2728 8.13441 21.3186C9.13444 21.364 9.45375 21.3752 11.9997 21.3752C14.5461 21.3752 14.8646 21.364 15.8646 21.3186C16.8626 21.2728 17.5447 21.1148 18.1412 20.883C18.7576 20.6436 19.2799 20.3225 19.8007 19.8009C20.3223 19.2799 20.6425 18.7568 20.8828 18.1404C21.1133 17.5441 21.2715 16.8626 21.3184 15.8649C21.3633 14.865 21.375 14.5463 21.375 12.0001C21.375 9.45394 21.3633 9.13444 21.3184 8.13441C21.2715 7.13634 21.1133 6.45516 20.8828 5.85909C20.6425 5.24241 20.3223 4.71937 19.8007 4.19822C19.2793 3.67678 18.7578 3.35644 18.1406 3.11728C17.543 2.88534 16.8613 2.72728 15.8632 2.68163C14.8632 2.63616 14.5448 2.625 11.9979 2.625H12.0008ZM11.1598 4.31447C11.4095 4.31409 11.688 4.31447 12.0008 4.31447C14.5041 4.31447 14.8007 4.32347 15.7892 4.36838C16.7032 4.41019 17.1994 4.56291 17.5298 4.69125C17.9674 4.86112 18.2793 5.06428 18.6072 5.3925C18.9353 5.72062 19.1384 6.03309 19.3088 6.47062C19.4371 6.80062 19.59 7.29675 19.6316 8.21081C19.6765 9.19913 19.6863 9.49594 19.6863 11.9979C19.6863 14.4999 19.6765 14.7968 19.6316 15.7851C19.5898 16.6991 19.4371 17.1952 19.3088 17.5253C19.1389 17.9629 18.9353 18.2744 18.6072 18.6023C18.2791 18.9305 17.9676 19.1335 17.5298 19.3035C17.1997 19.4324 16.7032 19.5848 15.7892 19.6266C14.8009 19.6715 14.5041 19.6812 12.0008 19.6812C9.49753 19.6812 9.20081 19.6715 8.21259 19.6266C7.29853 19.5844 6.80241 19.4317 6.47166 19.3033C6.03422 19.1333 5.72166 18.9303 5.39353 18.6022C5.06541 18.274 4.86234 17.9623 4.692 17.5246C4.56366 17.1945 4.41075 16.6984 4.36913 15.7843C4.32422 14.796 4.31522 14.4992 4.31522 11.9956C4.31522 9.492 4.32422 9.19678 4.36913 8.20847C4.41094 7.29441 4.56366 6.79828 4.692 6.46781C4.86197 6.03028 5.06541 5.71781 5.39363 5.38969C5.72184 5.06156 6.03422 4.85841 6.47175 4.68816C6.80222 4.55925 7.29853 4.40691 8.21259 4.36491C9.07744 4.32581 9.41259 4.31409 11.1598 4.31212V4.31447ZM17.0052 5.87109C16.3841 5.87109 15.8802 6.37453 15.8802 6.99572C15.8802 7.61681 16.3841 8.12072 17.0052 8.12072C17.6263 8.12072 18.1302 7.61681 18.1302 6.99572C18.1302 6.37463 17.6263 5.87072 17.0052 5.87072V5.87109ZM12.0008 7.18556C9.34209 7.18556 7.18641 9.34125 7.18641 12.0001C7.18641 14.6589 9.34209 16.8136 12.0008 16.8136C14.6597 16.8136 16.8146 14.6589 16.8146 12.0001C16.8146 9.34134 14.6595 7.18556 12.0007 7.18556H12.0008ZM12.0008 8.87503C13.7267 8.87503 15.1259 10.2741 15.1259 12.0001C15.1259 13.7259 13.7267 15.1252 12.0008 15.1252C10.275 15.1252 8.87588 13.7259 8.87588 12.0001C8.87588 10.2741 10.2749 8.87503 12.0008 8.87503Z" fill="white"/>
      </g>
      <defs>
      <radialGradient id="paint0_radial_390_2357" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(6.375 25.8485) rotate(-90) scale(23.7858 22.1227)">
      <stop stop-color="#FFDD55"/>
      <stop offset="0.1" stop-color="#FFDD55"/>
      <stop offset="0.5" stop-color="#FF543E"/>
      <stop offset="1" stop-color="#C837AB"/>
      </radialGradient>
      <radialGradient id="paint1_radial_390_2357" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(-4.02009 1.72884) rotate(78.681) scale(10.6324 43.827)">
      <stop stop-color="#3771C8"/>
      <stop offset="0.128" stop-color="#3771C8"/>
      <stop offset="1" stop-color="#6600FF" stop-opacity="0"/>
      </radialGradient>
      <clipPath id="clip0_390_2357">
      <rect width="24" height="24" fill="white"/>
      </clipPath>
      </defs>
      </svg>
      </a>

      <a href="#" title="TikTok"><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g clip-path="url(#clip0_390_2362)">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M6.79164 0.882152C8.78621 0.659294 10.8681 0.464294 13.0001 0.464294C15.1321 0.464294 17.2139 0.659294 19.2085 0.882152C20.7245 1.05598 22.1367 1.73898 23.2141 2.8195C24.2916 3.90001 24.9706 5.3141 25.1402 6.83058C25.3538 8.81587 25.5358 10.8829 25.5358 13C25.5358 15.1172 25.3538 17.1842 25.1402 19.1694C24.9706 20.6859 24.2916 22.1 23.2141 23.1805C22.1367 24.261 20.7245 24.944 19.2085 25.1179C17.2139 25.3407 15.1321 25.5357 13.0001 25.5357C10.8681 25.5357 8.78621 25.3407 6.79164 25.1179C5.27565 24.944 3.86347 24.261 2.78599 23.1805C1.70852 22.1 1.02949 20.6859 0.859927 19.1694C0.646355 17.1842 0.464355 15.1172 0.464355 13C0.464355 10.8829 0.646355 8.81587 0.859927 6.83058C1.02949 5.3141 1.70852 3.90001 2.78599 2.8195C3.86347 1.73898 5.27565 1.05598 6.79164 0.882152ZM15.4738 5.35229C15.4097 5.07441 15.2455 4.82986 15.0126 4.6653C14.7797 4.50073 14.4944 4.42766 14.211 4.46004C13.9277 4.49241 13.6662 4.62796 13.4764 4.84083C13.2866 5.05369 13.1818 5.32897 13.1821 5.61415V16.3429C13.1821 16.9129 13.013 17.4702 12.6963 17.9442C12.3796 18.4182 11.9295 18.7876 11.4028 19.0058C10.8761 19.2239 10.2966 19.281 9.73748 19.1698C9.17837 19.0586 8.66479 18.784 8.2617 18.3809C7.8586 17.9779 7.58409 17.4643 7.47288 16.9052C7.36167 16.3461 7.41875 15.7665 7.6369 15.2399C7.85505 14.7132 8.22448 14.263 8.69847 13.9463C9.17246 13.6296 9.72972 13.4606 10.2998 13.4606C10.6076 13.4606 10.9029 13.3383 11.1205 13.1206C11.3382 12.9029 11.4605 12.6077 11.4605 12.2999C11.4605 11.992 11.3382 11.6968 11.1205 11.4791C10.9029 11.2614 10.6076 11.1392 10.2998 11.1392C9.27059 11.1392 8.2645 11.4443 7.40875 12.0161C6.55301 12.5879 5.88604 13.4006 5.49218 14.3515C5.09832 15.3023 4.99527 16.3486 5.19606 17.3581C5.39684 18.3675 5.89245 19.2947 6.6202 20.0224C7.34795 20.7502 8.27517 21.2458 9.28459 21.4466C10.294 21.6474 11.3403 21.5443 12.2912 21.1505C13.242 20.7566 14.0547 20.0896 14.6265 19.2339C15.1983 18.3781 15.5035 17.3721 15.5035 16.3429V9.86144C16.6178 10.8086 18.0552 11.3286 19.7415 11.3286C20.0493 11.3286 20.3446 11.2063 20.5622 10.9886C20.7799 10.7709 20.9022 10.4757 20.9022 10.1679C20.9022 9.86002 20.7799 9.56479 20.5622 9.34712C20.3446 9.12944 20.0493 9.00715 19.7415 9.00715C18.5158 9.00715 17.6058 8.62829 16.9354 8.03215C16.2519 7.42301 15.7449 6.51672 15.4738 5.35229Z" fill="black"/>
      </g>
      <defs>
      <clipPath id="clip0_390_2362">
      <rect width="26" height="26" fill="white"/>
      </clipPath>
      </defs>
      </svg>
      </a>
      </div>
    </div>

  <!-- COPYRIGHT -->
  <div class="footer-bottom">
    Copyright © 2026 Nội Thất LEM.
  </div>
</footer>


<script>
let total = 0;
let discount = 0;
</script>
<script>
document.addEventListener("DOMContentLoaded", function(){

  let productList = document.getElementById("product-list");
  let buyNow = JSON.parse(localStorage.getItem("buyNow")) || [];
  const currentUser = "<?php echo $_SESSION['user_id']; ?>";
let cart = JSON.parse(localStorage.getItem("cart_" + currentUser)) || [];
  let selected = JSON.parse(localStorage.getItem("selectedCart")) || [];

// ✅ ưu tiên mua ngay
let selectedItems;

if (buyNow.length > 0) {
  selectedItems = buyNow;
} else {
  selectedItems = selected.length > 0
    ? cart.filter(item => selected.includes(String(item.id)))
    : cart;
}
  function renderCart(){
    total = 0;
    productList.innerHTML = "";

    selectedItems.forEach(item => {
      let div = document.createElement("div");
      div.classList.add("product");

      div.innerHTML = `
        <img src="${item.img}" width="60">
        <div>
          <p>${item.name}</p>
          <span>${item.price.toLocaleString()}đ x${item.quantity}</span>
        </div>
      `;

      productList.appendChild(div);
      total += item.price * item.quantity;
    });

    updateTotal();
  }
  renderCart();

});

</script>
<script>
function getShipping(){
  let method = document.querySelector("select[name='shipping']").value;

  if(method === "fast") return 50000;
  if(method === "standard") return 30000;

  return 0;
}

function updateTotal(){
  let shipping = getShipping();
  let final = total + shipping - discount;

  if(final < 0) final = 0;

  document.getElementById("total-price").innerText = total.toLocaleString() + "đ";
  document.getElementById("shipping-fee").innerText = shipping.toLocaleString() + "đ";
  document.getElementById("final-price").innerText = final.toLocaleString() + "đ";

  suggestCoupon();
}
</script>


<script>
document.addEventListener("DOMContentLoaded", function(){
  document.querySelector("form").addEventListener("submit", function(){
    document.querySelector("select[name='shipping']")
    .addEventListener("change", function(){
      updateTotal();
  });

});

    let buyNow = JSON.parse(localStorage.getItem("buyNow")) || [];
    let selected = JSON.parse(localStorage.getItem("selectedCart")) || [];
    const currentUser = "<?php echo $_SESSION['user_name']; ?>";
    let cart = JSON.parse(localStorage.getItem("cart_" + currentUser)) || [];

    let selectedItems;

    // ✅ Ưu tiên mua ngay
    if (buyNow.length > 0) {
      selectedItems = buyNow;
    } else {
      selectedItems = selected.length > 0
        ? cart.filter(item => selected.includes(String(item.id)))
        : cart;
    }

    document.getElementById("cart_data").value = JSON.stringify(selectedItems);
    document.getElementById("discount_input").value = discount;
  });

</script>
<script>
let radios = document.querySelectorAll('input[name="pay"]');
let qrBox = document.getElementById("bank-qr");
let submitBtn = document.getElementById("submit-btn");

radios.forEach(radio => {
    radio.addEventListener("change", function(){

        if(this.value === "bank"){
            qrBox.style.display = "block";
            submitBtn.innerText = "Tôi đã thanh toán";
        } else {
            qrBox.style.display = "none";
            submitBtn.innerText = "Đặt hàng";
        }

    });
});
</script>
<script>
document.querySelectorAll("input[required]").forEach(input => {
    input.addEventListener("invalid", function() {
        this.setCustomValidity("Vui lòng điền thông tin này");
    });

    input.addEventListener("input", function() {
        this.setCustomValidity("");
    });
});
</script>
<script>
function applyCoupon(e){
  e.preventDefault();
  let code = document.getElementById("coupon-code").value.trim();

  if(code === ""){
    alert("Vui lòng nhập mã!");
    return;
  }

 if(code === "AUTO"){
  if(total >= 20000000){
    discount = Math.round(total * 0.1);
    alert("Giảm 10%");
  }
  else if(total >= 10000000){
    discount = Math.round(total * 0.05);
    alert("Giảm 5%");
  }
  else{
    discount = Math.round(total * 0.02);
    alert("Giảm 2%");
  }
}
  else{
    alert("Mã không hợp lệ!");
    discount = 0;
  }

  updateTotal();
}
</script>
<script>
function suggestCoupon(){
  let text = "";

  if(total >= 20000000){
    text = "🔥 Bạn có mã AUTO giảm 10%";
  }
  else if(total >= 10000000){
    text = "🎁 Bạn có mã AUTO giảm 5%";
  }
  else if(total > 0){
    text = "💡 Nhập AUTO để giảm 2%";
  }

  document.getElementById("coupon-suggest").innerText = text;
}
</script>
<script>
const data = {
  cantho: {
    "Ninh Kiều": ["An Khánh", "An Bình"],
    "Bình Thủy": ["Long Hòa", "Trà An"]
  },
  hcm: {
    "Quận 1": ["Bến Nghé", "Bến Thành"],
    "Quận 7": ["Tân Phú", "Tân Thuận"],
    "Thủ Đức": ["Linh Trung", "Hiệp Bình"]
  },
  hanoi: {
    "Ba Đình": ["Kim Mã", "Ngọc Hà"],
    "Cầu Giấy": ["Dịch Vọng", "Nghĩa Đô"]
  },
  danang: {
    "Hải Châu": ["Hòa Thuận Đông", "Hòa Thuận Tây"],
    "Sơn Trà": ["An Hải Bắc", "Mân Thái"]
  },

  binhduong: {
    "Thủ Dầu Một": ["Hiệp Thành", "Phú Cường"],
    "Dĩ An": ["An Bình", "Tân Đông Hiệp"]
  },

  angiang: {
    "Long Xuyên": ["Mỹ Bình", "Mỹ Phước"],
    "Châu Đốc": ["Vĩnh Mỹ", "Châu Phú A"]
  },

  dongthap: {
    "Cao Lãnh": ["Phường 1", "Mỹ Phú"],
    "Sa Đéc": ["An Hòa", "Tân Quy Đông"]
  },

  haiphong: {
    "Lê Chân": ["An Biên", "Cát Dài"],
    "Ngô Quyền": ["Máy Chai", "Cầu Tre"]
  },

  khanhhoa: {
    "Nha Trang": ["Vĩnh Hải", "Phước Long"],
    "Cam Ranh": ["Cam Lợi", "Cam Phú"]
  }
};

let city = document.getElementById("city");
let district = document.getElementById("district");
let ward = document.getElementById("ward");

// chọn tỉnh
city.addEventListener("change", function () {
  let c = this.value;

  district.innerHTML = '<option value="">Quận/Huyện</option>';
  ward.innerHTML = '<option value="">Phường/Xã</option>';

  if (!data[c]) return;

  Object.keys(data[c]).forEach(d => {
    let opt = document.createElement("option");
    opt.value = d;
    opt.textContent = d;
    district.appendChild(opt);
  });
});

// chọn quận
district.addEventListener("change", function () {
  let c = city.value;
  let d = this.value;

  ward.innerHTML = '<option value="">Phường/Xã</option>';

  if (!data[c][d]) return;

  data[c][d].forEach(w => {
    let opt = document.createElement("option");
    opt.value = w;
    opt.textContent = w;
    ward.appendChild(opt);
  });
});
</script>

</body>
</html>

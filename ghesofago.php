<?php session_start(); 
// ================== XỬ LÝ USER CHO GIỎ HÀNG ==================
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'guest_' . session_id();
}

if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'Khách hàng';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nội Thất LEM</title>

  <!-- FONT -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;700&display=swap" rel="stylesheet">

  <!-- ICON -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- CSS -->
  <link rel="stylesheet" href="sanpham5.css">
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

    <!-- RIGHT -->
    <div class="header-right">

      <!-- LOGIN -->
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

      <div class="user-box guest"> 
        <a href="trangchu.php">
          <strong>Đăng nhập<br>Đăng ký</strong>
        </a>
      </div>

   <?php } ?>
   
      <!-- CART -->
      <a href="giohang.php" class="cart">
        <i class="fa-solid fa-cart-shopping"></i>
        <span id="cart-count">0</span>
      </a>

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
      <a href="bosutap.php">BỘ SƯU TẬP BLUE SKY</a>
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

 <!-- ================= SẢN PHẨM ================= -->
<section class="product-detail">

  <div class="product-container" id="product" data-id="2">

    <!-- LEFT -->
    <div class="product-gallery">
      <div class="thumbnail-list">
        <img src="img/sofa/Ghe-sofa-go1.png">
        <img src="img/sofa/Ghe-sofa-go2.png">
        <img src="img/sofa/Ghe-sofa-go3.png">
      </div>

      <div class="main-image">
        <img id="mainProductImage" src="img/sofa/Ghe-sofa-go.png">
      </div>
    </div>

    <!-- RIGHT -->
    <div class="product-info">

      <h1 id="product-name">Ghế sofa gỗ</h1>

      <div class="price">
        <span class="new-price">12.000.000đ</span>
        <span class="old-price">12.700.000đ</span>
        <!-- GIÁ THẬT (dùng cho JS) -->
        <span id="product-price" style="display:none;">12000000</span>
      </div>

      <div class="description">
        <p><strong>Kích thước:</strong> Dài 220 x Rộng 90 x Cao 85 cm (có thể thay đổi theo yêu cầu)</p>
        <p><strong>Chất liệu chính:</strong> Gỗ tự nhiên cao cấp kết hợp nệm bông ép và vải bố cao cấp</p>
        <p><strong>Màu sắc:</strong> Nâu gỗ tự nhiên - Xám khói</p>
      </div>

      <div class="quantity">
        <button onclick="decreaseQty()">-</button>
        <input type="text" id="qty" value="1">
        <button onclick="increaseQty()">+</button>
      </div>

      <button class="add-cart" onclick="addToCart()">Thêm vào giỏ hàng</button>
      <button class="buy-now" onclick="buyNow()">Mua ngay</button>
  
      <div class="policy">
        <p>Miễn phí giao hàng & lắp đặt tại TP.HCM, Hà Nội...</p>
        <p>Bảo hành 5 năm - Bảo trì trọn đời</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= BẢNG TAB ================= -->
<section class="product-tabs">

    <div class="tab-header">
        <button class="tab-link active" onclick="openTab(event,'description')">Mô tả</button>
        <button class="tab-link" onclick="openTab(event,'reviews')">Đánh giá (30)</button>
    </div>

    <!-- MÔ TẢ -->
    <div id="description" class="tab-body active">
        <h2>Ghế Sofa Gỗ</h2>
        <p>
            Ghế sofa gỗ cao cấp với thiết kế hiện đại, mang đến sự sang trọng và ấm cúng cho không gian phòng khách. 
            Khung gỗ tự nhiên chắc chắn, kết hợp đệm ngồi êm ái và lớp vải cao cấp.
        </p>

        <div class="product-info">
            <div><strong>Chất liệu khung:</strong> Gỗ cao su tự nhiên</div>
            <div><strong>Chất liệu đệm:</strong> Bông ép cao cấp + Lò xo túi</div>
            <div><strong>Màu sắc:</strong> Nâu gỗ tự nhiên</div>
            <div><strong>Bảo hành:</strong> 5 năm khung gỗ - 12 tháng vải & đệm</div>
        </div>
    </div>

    <!-- ĐÁNH GIÁ -->
    <div id="reviews" class="tab-body">
        <div class="review-item">
            <div class="review-top">
                <strong>Nguyễn Văn A</strong>
                <span class="stars">★★★★★</span>
            </div>
            <p>Sofa rất đẹp, chất lượng tốt, ngồi rất êm.</p>
        </div>

        <div class="review-item">
            <div class="review-top">
                <strong>Trần Thị B</strong>
                <span class="stars">★★★★☆</span>
            </div>
            <p>Đúng như hình, giao hàng nhanh, sẽ mua thêm sản phẩm khác.</p>
        </div>

        <div class="review-form">
            <h3>Viết đánh giá</h3>
            <input type="text" placeholder="Họ và tên">
            <textarea placeholder="Nội dung đánh giá"></textarea>
            <button>Gửi đánh giá</button>
        </div>
    </div>
</section>

<!-- ================= SẢN PHẨM LIÊN QUAN ================= -->
<section class="suggest">
  <h2 class="suggest-title">SẢN PHẨM LIÊN QUAN</h2>
  <div class="suggest-line"></div>
  <div class="suggest-product-grid" id="relatedProducts">
    <p>Đang tải sản phẩm liên quan...</p>
  </div>
</section>

<!-- ================= REVIEWS ================= -->
<section class="reviews">
  <div class="reviews-header">
    <h2>Đánh giá thực tế</h2>
    <div class="line"></div>
  </div>

  <div class="reviews-list">
    <div class="review-item">
      <img src="img/anhdanhgia1.png" alt="">
      <h3>Nguyễn Anh Vũ</h3>
      <div class="stars">★★★★★</div>
      <p>Shop bán đồ khá đẹp, đã mua nhiều lần rất ưng ý.</p>
    </div>
    <!-- Các review khác giữ nguyên hoặc bạn có thể bổ sung sau -->
  </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="footer">
  <!-- Giữ nguyên phần footer như cũ -->
  <div class="footer-top">
    <div class="footer-container">
      <div class="footer-col">
        <h3>NỘI THẤT LEM</h3>
        <p>Nội Thất LEM là thương hiệu đến từ Savimex với gần 20 năm kinh nghiệm trong việc sản xuất và xuất khẩu nội thất đạt chuẩn quốc tế.</p>
        <h3 class="mt">BẢN ĐỒ</h3>
        <div class="footer-map">
          <iframe src="https://www.google.com/maps?q=Savimex%20Vietnam&output=embed" width="100%" height="160" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
      </div>
      <div class="footer-col">
        <h3>THÔNG TIN</h3>
        <ul>
          <li>Chính Sách Bán Hàng</li>
          <li>Chính Sách Giao Hàng & Lắp Đặt</li>
          <li>Chính Sách Bảo Hành & Bảo Trì</li>
          <li>Chính Sách Đổi Trả</li>
        </ul>
      </div>
      <div class="footer-col">
        <h3>THÔNG TIN LIÊN HỆ</h3>
        <p>📩 cskhnoithatlem@gmail.com</p>
        <p>📞 0367852599 (Hotline/Zalo)</p>
        <p>📞 0372351532 (Đội giao hàng)</p>
      </div>
    </div>
  </div>

  <div class="footer-social">
    <!-- Các icon mạng xã hội giữ nguyên -->
  </div>

  <div class="footer-bottom">
    Copyright © 2026 Nội Thất LEM.
  </div>
</footer>

<script>
// Các script giữ nguyên (openTab, gallery, cart functions...)
function openTab(evt, tabName) {
    let i, tabBody, tabLinks;
    tabBody = document.getElementsByClassName("tab-body");
    for (i = 0; i < tabBody.length; i++) tabBody[i].classList.remove("active");

    tabLinks = document.getElementsByClassName("tab-link");
    for (i = 0; i < tabLinks.length; i++) tabLinks[i].classList.remove("active");

    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}

// Gallery click
const mainImage = document.getElementById("mainProductImage");
const thumbnails = document.querySelectorAll(".thumbnail-list img");
thumbnails.forEach(img => {
  img.addEventListener("click", function() {
    mainImage.src = this.src;
  });
});

// ================== CART FUNCTIONS ==================
const currentUserId = "<?php echo addslashes($_SESSION['user_id']); ?>";

function addToCart() {
    let qty = parseInt(document.getElementById("qty").value) || 1;
    if (qty < 1) qty = 1;

    const product = {
        id: parseInt(document.getElementById("product").dataset.id),
        name: document.getElementById("product-name").innerText.trim(),
        price: parseInt(document.getElementById("product-price").textContent),
        img: document.getElementById("mainProductImage").src,
        quantity: qty
    };

    let cart = JSON.parse(localStorage.getItem("cart_" + currentUserId)) || [];
    const existingIndex = cart.findIndex(item => item.id === product.id);

    if (existingIndex !== -1) {
        cart[existingIndex].quantity += qty;
    } else {
        cart.push(product);
    }

    localStorage.setItem("cart_" + currentUserId, JSON.stringify(cart));
    updateCartCount();
    showNotification(`✓ Đã thêm <strong>${product.name}</strong> (${qty} cái) vào giỏ hàng!`);
}

function buyNow() {
    let qty = parseInt(document.getElementById("qty").value) || 1;
    if (qty < 1) qty = 1;

    const product = {
        id: parseInt(document.getElementById("product").dataset.id),
        name: document.getElementById("product-name").innerText.trim(),
        price: parseInt(document.getElementById("product-price").textContent),
        img: document.getElementById("mainProductImage").src,
        quantity: qty
    };

    localStorage.setItem("buyNow", JSON.stringify([product]));
    window.location.href = "thanhtoan.php";
}

function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem("cart_" + currentUserId)) || [];
    let total = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
    document.getElementById("cart-count").innerText = total;
}

function showNotification(message) {
    const notif = document.createElement("div");
    notif.className = "cart-notification";
    notif.innerHTML = message;
    document.body.appendChild(notif);
    setTimeout(() => {
        notif.style.opacity = "0";
        setTimeout(() => notif.remove(), 400);
    }, 2500);
}

function increaseQty() {
    let qtyInput = document.getElementById("qty");
    let value = parseInt(qtyInput.value) || 1;
    qtyInput.value = value + 1;
}

function decreaseQty() {
    let qtyInput = document.getElementById("qty");
    let value = parseInt(qtyInput.value) || 1;
    if (value > 1) qtyInput.value = value - 1;
}

document.addEventListener("DOMContentLoaded", function() {
    updateCartCount();
});
</script>

<script>
// Load sản phẩm liên quan (giữ nguyên)
fetch("getSanPham.php?custom_order=131,122,123,124,125,126,127,128")
  .then(res => res.json())
  .then(data => {
    let html = "";
    data.forEach(sp => {
      html += `
        <div class="product-card">
          <img src="${sp.anh_san_pham}" alt="">
          <h3>${sp.ten_san_pham}</h3>
          <p class="price">${Number(sp.gia).toLocaleString()}đ</p>
        </div>
      `;
    });
    document.getElementById("relatedProducts").innerHTML = html;
  });
</script>

</body>
</html>
<?php
include "connect.php";

if(isset($_POST['ten']) && isset($_POST['gia']) && isset($_POST['anh'])){

    $ten = $_POST['ten'];
    $gia = $_POST['gia'];
    $anh = $_POST['anh'];

    // 👇 thêm dòng này (tránh lỗi nếu không nhập)
    $so_luong = isset($_POST['so_luong']) ? $_POST['so_luong'] : 0;

    // 🔥 THÊM so_luong vào SQL
    $sql = "INSERT INTO san_pham 
    (danh_muc_id, ten_san_pham, gia, anh_san_pham, trang_thai, so_luong)
    VALUES (1, '$ten', '$gia', '$anh', 1, '$so_luong')";

    if(mysqli_query($conn, $sql)){
        echo "success";
    } else {
        echo mysqli_error($conn);
    }

} else {
    echo "missing";
}
?>
<?php
include "connect.php";

if(isset($_POST['id'])){

    $id = $_POST['id'];
    $gia = $_POST['gia'];
    $so_luong = $_POST['so_luong'];
    $trang_thai = $_POST['trang_thai'];

    $sql = "UPDATE san_pham 
            SET gia='$gia', 
                so_luong='$so_luong', 
                trang_thai='$trang_thai'
            WHERE id='$id'";

    if(mysqli_query($conn, $sql)){
        echo "success";
    } else {
        echo mysqli_error($conn);
    }

} else {
    echo "missing";
}
?>
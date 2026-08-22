<?php
include "connect.php";

// ===== XÓA NHIỀU =====
if (isset($_POST['ids'])) {

    $ids = $_POST['ids']; // "1,2,3"

    $arr = explode(",", $ids);
    $arr = array_map('intval', $arr);

    $ids_str = implode(",", $arr);

    $sql = "DELETE FROM san_pham WHERE id IN ($ids_str)";

    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "error: " . $conn->error; // 👈 QUAN TRỌNG
    }

}

// ===== XÓA 1 =====
else if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $sql = "DELETE FROM san_pham WHERE id = $id";

    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "error: " . $conn->error; // 👈 QUAN TRỌNG
    }

}

// ===== KHÔNG CÓ DATA =====
else {
    echo "no data";
}
?>
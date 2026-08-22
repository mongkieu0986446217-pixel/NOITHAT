<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

include "connect.php";

$email = trim($_POST['email'] ?? '');

if(empty($email)){
    echo json_encode(["status" => "empty"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM taikhoan WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

    // ================== ĐÃ TỐI ƯU ==================
    $token  = bin2hex(random_bytes(32));           // 64 ký tự - đủ dài và chuẩn
    $expire = date("Y-m-d H:i:s", strtotime("+24 hours"));

    $stmt = $conn->prepare("UPDATE taikhoan SET reset_token = ?, token_expire = ? WHERE email = ?");
    $stmt->bind_param("sss", $token, $expire, $email);
    $stmt->execute();

    $link = "http://localhost/WNT/reset_password.php?token=" . $token;

    // ===== GỬI MAIL =====
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nguyenanhvu280620@gmail.com';
        $mail->Password   = 'acrw nrdf xpzu lutw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Nên dùng hằng số này
        $mail->Port       = 587;

        $mail->setFrom('nguyenanhvu280620@gmail.com', 'Noi That LEM');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Khôi phục mật khẩu - Noi That LEM';

        $mail->Body = "
            <h3>Khôi phục mật khẩu</h3>
            <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình.</p>
            <p>Vui lòng click vào link bên dưới để tiếp tục (link có hiệu lực trong 24 giờ):</p>
            <p><a href='{$link}' style='font-size:16px; color:#0066cc;'>{$link}</a></p>
            <p style='color:#d32f2f; margin-top:20px;'>
                Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.
            </p>
        ";

        $mail->send();

        echo json_encode(["status" => "success"]);

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "msg"    => $mail->ErrorInfo
        ]);
    }

} else {
    // Bảo mật: không tiết lộ email không tồn tại
    echo json_encode(["status" => "success"]);
}
?>
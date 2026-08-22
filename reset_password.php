<?php
include "connect.php";

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Token không hợp lệ!");
}

$sql = "SELECT reset_token, token_expire, NOW() as server_time 
        FROM taikhoan 
        WHERE reset_token = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Lỗi prepare SQL: " . $conn->error);
}

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Token không tồn tại!");
}

$row = $result->fetch_assoc();

$expire_ts = strtotime($row['token_expire']);
$current_ts = strtotime($row['server_time']);

if ($expire_ts <= $current_ts) {
    die("<h2 style='color:red; text-align:center;'>Link đã hết hạn hoặc không hợp lệ!</h2>
         <p style='text-align:center;'><a href='forgot_password.php'>Yêu cầu link mới</a></p>");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - Noi That LEM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8B5A2B 0%, #5C4033 100%); /* Nền nâu gỗ */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        h2 {
            color: #3C2F2F;
            margin-bottom: 8px;
        }
        p {
            color: #555;
            margin-bottom: 25px;
            font-size: 15px;
        }
        input {
            width: 100%;
            padding: 14px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        input:focus {
            outline: none;
            border-color: #8B5A2B;
            box-shadow: 0 0 0 3px rgba(139, 90, 43, 0.15);
        }
        button {
            width: 100%;
            padding: 14px;
            background: #8B5A2B;     
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }
        button:hover {
            background: #5C4033;        
        }
        .success {
            color: #28a745;
            font-size: 18px;
            margin: 20px 0;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔑</div>
        <h2>Đặt lại mật khẩu</h2>
        <p>Nhập mật khẩu mới cho tài khoản của bạn</p>

        <?php if (isset($_POST['new_password']) && !empty($_POST['new_password'])): 
            $newPass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

            $update_stmt = $conn->prepare("UPDATE taikhoan SET 
                password = ?, reset_token = NULL, token_expire = NULL 
                WHERE reset_token = ?");
            
            $update_stmt->bind_param("ss", $newPass, $token);
            $update_stmt->execute();
        ?>
            <div class="success">
                ✅ Đổi mật khẩu thành công!<br><br>
                <a href="trangchu.php" style="color:#8B5A2B; text-decoration:none; font-size:17px;">
                    → Đăng nhập ngay
                </a>
            </div>
        <?php else: ?>
            <form method="POST">
                <input type="password" 
                       name="new_password" 
                       required 
                       minlength="6"
                       placeholder="Mật khẩu mới (ít nhất 6 ký tự)">
                
                <button type="submit">Đổi mật khẩu</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
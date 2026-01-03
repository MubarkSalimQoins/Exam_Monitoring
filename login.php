<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل دخول النظام</title>

    <!-- خط عربي -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- أيقونات Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            margin: 0;
            height: 100vh;
            direction: rtl;
            background: url("images/students-bg.png") no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: #fff;
            width: 360px;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .icon {
            width: 60px;
            height: 60px;
            background: #2f6fed;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 26px;
        }

        h2 {
            margin: 10px 0 5px;
            font-weight: 700;
        }

        p {
            margin-bottom: 25px;
            color: #666;
            font-size: 14px;
        }

        .alert {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            background: #fdecea;
            color: #b71c1c;
            border: 1px solid #f5c6cb;
        }

        .input-group {
            position: relative;
            margin-bottom: 15px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 40px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
            outline: none;
        }

        .left-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .toggle-password {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2f6fed;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #2558c7;
        }

        .footer-text {
            margin-top: 15px;
            font-size: 13px;
            color: #555;
        }
    </style>
</head>
<body>

<div class="login-box">

    <div class="icon">
        <i class="fa-solid fa-user-shield"></i>
    </div>

    <h2>تسجيل دخول النظام</h2>
    <p>قم بتسجيل الدخول حسب صلاحيتك</p>

    <!-- رسالة الخطأ -->
    <?php if (isset($_SESSION["login_attempt"]) && isset($_SESSION["error"])): ?>
        <div class="alert">
            <?= htmlspecialchars($_SESSION["error"]) ?>
        </div>
        <?php
        unset($_SESSION["error"]);
        unset($_SESSION["login_attempt"]);
        ?>
    <?php endif; ?>

    <!-- ✅ autocomplete معطل -->
    <form action="check_login.php" method="POST" autocomplete="off">

        <div class="input-group">
            <i class="fa-solid fa-user-tie left-icon"></i>
            <input type="text"
                   name="login"
                   placeholder="أدخل اسم المستخدم أو البريد الإلكتروني"
                   autocomplete="off"
                   required>
        </div>

        <div class="input-group">
            <i class="fa-solid fa-lock left-icon"></i>
            <input type="password"
                   id="password"
                   name="password"
                   placeholder="أدخل كلمة المرور"
                   autocomplete="new-password"
                   required>

            <span class="toggle-password" onclick="togglePassword()">
                <i class="fa-solid fa-eye"></i>
            </span>
        </div>

        <button type="submit">تسجيل الدخول</button>
    </form>

    <div class="footer-text">
        نظام مراقبة الاختبارات
    </div>
</div>

<script>
    // إظهار / إخفاء كلمة المرور
    function togglePassword() {
        const password = document.getElementById("password");
        const icon = document.querySelector(".toggle-password i");

        if (password.type === "password") {
            password.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            password.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }

    // ✅ تفريغ الحقول عند تحديث الصفحة
    window.onload = function () {
        document.querySelector("form").reset();
    };
</script>

</body>
</html>

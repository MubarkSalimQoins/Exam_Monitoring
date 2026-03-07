<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول النظام</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- خط عربي -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            font-family: 'Cairo', sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* خلفية متحركة احترافية */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url("images/students-bg.png") no-repeat center/cover;
            opacity: 0.1;
            z-index: 0;
        }

        /* دوائر متحركة في الخلفية */
        .bg-circles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite ease-in-out;
        }

        .circle:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
            animation-delay: 0s;
        }

        .circle:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -100px;
            animation-delay: 4s;
        }

        .circle:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 10%;
            animation-delay: 2s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-30px) rotate(180deg);
            }
        }

        .container {
            position: relative;
            z-index: 1;
        }

        /* Glassmorphism Card */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
        }

        /* أيقونة تسجيل الدخول */
        .login-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: -45px auto 20px;
            box-shadow: 0 15px 35px rgba(13, 110, 253, 0.4);
            position: relative;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 15px 35px rgba(13, 110, 253, 0.4);
            }
            50% {
                box-shadow: 0 15px 45px rgba(13, 110, 253, 0.6);
            }
        }

        .login-icon::before {
            content: '';
            position: absolute;
            width: 110%;
            height: 110%;
            border-radius: 50%;
            border: 2px solid rgba(13, 110, 253, 0.3);
            animation: ripple 2s infinite;
        }

        @keyframes ripple {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        /* العنوان */
        .login-title {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 25px;
        }

        /* رسالة الخطأ */
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* حقول الإدخال */
        .input-group {
            margin-bottom: 20px;
        }

        .input-group-text {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
            color: white;
            border-radius: 12px 0 0 12px;
            padding: 12px 15px;
            font-size: 18px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 0 12px 12px 0;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            background: white;
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            transform: translateY(-2px);
        }

        .input-group:has(.form-control:focus) .input-group-text {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            transform: translateY(-2px);
        }

        /* أيقونة إظهار كلمة المرور */
        .input-group-text[style*="cursor"] {
            border-radius: 0 12px 12px 0;
            background: #f8f9fa;
            color: #0d6efd;
            border: 2px solid #e9ecef;
            border-left: none;
            transition: all 0.3s ease;
        }

        .input-group-text[style*="cursor"]:hover {
            background: #0d6efd;
            color: white;
        }

        /* زر تسجيل الدخول */
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(13, 110, 253, 0.5);
            background: linear-gradient(135deg, #0a58ca 0%, #0d6efd 100%);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        /* نص التذييل */
        .footer-text {
            color: #6c757d;
            font-size: 13px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        /* تحسينات للشاشات الصغيرة */
        @media (max-width: 576px) {
            .login-card {
                border-radius: 20px;
                margin: 15px;
            }

            .login-title {
                font-size: 1.5rem;
            }

            .login-icon {
                width: 75px;
                height: 75px;
                font-size: 30px;
                margin: -37px auto 15px;
            }
        }

        /* تأثير التحميل */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .login-card {
            animation: fadeIn 0.5s ease;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <!-- دوائر الخلفية -->
    <div class="bg-circles">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-9 col-md-7 col-lg-5 col-xl-4">
                <div class="card login-card shadow-lg border-0 pt-5">
                    <div class="login-icon">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="card-body text-center px-4 px-sm-5">
                        <h4 class="login-title">تسجيل دخول النظام</h4>
                        <p class="login-subtitle">قم بتسجيل الدخول حسب صلاحيتك</p>

                        <!-- رسالة الخطأ -->
                        <?php if (isset($_SESSION["login_attempt"]) && isset($_SESSION["error"])): ?>
                        <div class="alert alert-danger py-2">
                            <?= htmlspecialchars($_SESSION["error"]) ?>
                        </div>
                        <?php
                        unset($_SESSION["error"]);
                        unset($_SESSION["login_attempt"]);
                        ?>
                        <?php endif; ?>

                        <form action="check_login.php" method="POST" autocomplete="off">
                            <!-- اسم المستخدم -->
                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-user-tie"></i>
                                </span>
                                <input type="text"
                                       name="login"
                                       class="form-control"
                                       placeholder="اسم المستخدم أو البريد الإلكتروني"
                                       autocomplete="off"
                                       required>
                            </div>

                            <!-- كلمة المرور -->
                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-control"
                                       placeholder="كلمة المرور"
                                       autocomplete="new-password"
                                       required>
                                <span class="input-group-text" style="cursor:pointer" onclick="togglePassword()">
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                تسجيل الدخول
                            </button>
                        </form>

                        <div class="footer-text">
                            نظام مراقبة الاختبارات
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const icon = event.currentTarget.querySelector("i");
            if (password.type === "password") {
                password.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                password.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        window.onload = function () {
            document.querySelector("form").reset();
        };
    </script>
</body>
</html>

<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل دخول النظام</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- خط عربي -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(
                rgba(0,0,0,0.6),
                rgba(0,0,0,0.6)
            ),
            url("images/students-bg.png") no-repeat center/cover;
            font-family: 'Cairo', sans-serif;
        }

        .login-icon {
            width: 70px;
            height: 70px;
            background: #0d6efd;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: -60px auto 15px;
            box-shadow: 0 10px 25px rgba(13,110,253,.5);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">

            <div class="card shadow-lg border-0 rounded-4 pt-5">
                <div class="login-icon">
                    <i class="fa-solid fa-user-shield"></i>
                </div>

                <div class="card-body text-center px-4">

                    <h4 class="fw-bold mb-1">تسجيل دخول النظام</h4>
                    <p class="text-muted mb-4">قم بتسجيل الدخول حسب صلاحيتك</p>

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

                    <div class="text-muted small mt-3">
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

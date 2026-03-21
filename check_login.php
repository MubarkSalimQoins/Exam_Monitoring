<?php
session_start();
require "db.php";

$_SESSION["login_attempt"] = true;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$login = trim($_POST["login"] ?? "");
$password = $_POST["password"] ?? "";

if ($login === "" || $password === "") {
    $_SESSION["error"] = "يرجى إدخال جميع الحقول";
    header("Location: login.php");
    exit;
}

/* جلب المستخدم */
$stmt = $pdo->prepare("
    SELECT * FROM supervisors
    WHERE email = :login OR name = :login
    LIMIT 1
");
$stmt->execute(["login" => $login]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION["error"] = "اسم المستخدم أو البريد الإلكتروني غير موجود";
    header("Location: login.php");
    exit;
}

/* التحقق من كلمة المرور */
if (!password_verify($password, $user["password"])) {
    $_SESSION["error"] = "كلمة المرور غير صحيحة";
    header("Location: login.php");
    exit;
}

/* ✅ نجاح تسجيل الدخول */
$_SESSION["supervisor_id"]   = $user["supervisor_id"];
$_SESSION["supervisor_name"] = $user["name"];
$_SESSION["role"]            = $user["role"];

/* 🔐 التوجيه حسب الدور */
if ($user["role"] === "supervisor") {

    header("Location: notifications.php");
    exit;

} elseif ($user["role"] === "admin") {

    header("Location: notifications_admin.php");
    exit;

} else {

    $_SESSION["error"] = "ليس لديك صلاحية الدخول";
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

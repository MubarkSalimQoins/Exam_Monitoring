<?php
session_start();

/* التحقق من تسجيل الدخول */
if (!isset($_SESSION["supervisor_id"])) {
    header("Location: login.php");
    exit;
}

/* التحقق من الدور (مراقب فقط) */
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "supervisor") {
    echo "<h3 style='color:red; text-align:center; margin-top:50px;'>
            🚫 ليس لديك صلاحية الدخول لهذه الصفحة
          </h3>";
    echo "<p style='text-align:center;'>سيتم إعادتك لصفحة تسجيل الدخول...</p>";
    header("refresh:3;url=login.php");
    exit;
}

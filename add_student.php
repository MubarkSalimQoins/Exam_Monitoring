<?php
session_start();
require "db.php";

/* لاحقًا نضيف حماية المراقب */
// require "auth_supervisor.php";

/* جلب الطلاب */
$stmt = $pdo->query("
    SELECT student_id, name, student_number, level, major, image_path
    FROM students
    ORDER BY student_id ASC
");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إدارة الطلاب</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="students.css">
<link rel="stylesheet" href="close.css">
</head>
<body>

<div class="container">

    <!-- ===== بطاقة الطالب ===== -->
    <div class="student-card">

        <h3>إضافة طالب</h3>

        <!-- ✅ رسائل الخطأ / النجاح -->
        <?php if (!empty($_SESSION["error"])): ?>
            <div class="alert error">
                <i class="fa fa-circle-xmark"></i>
                <?= $_SESSION["error"]; unset($_SESSION["error"]); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION["success"])): ?>
            <div class="alert success">
                <i class="fa fa-circle-check"></i>
                <?= $_SESSION["success"]; unset($_SESSION["success"]); ?>
            </div>
        <?php endif; ?>

        <form action="save_student.php" method="POST" enctype="multipart/form-data">

            <div class="avatar" id="avatarPreview">
                <i class="fa-solid fa-user"></i>
            </div>

            <input type="file" name="student_image" id="imageInput" accept="image/*" hidden>
            <video id="camera" autoplay hidden></video>
            <canvas id="snapshot" hidden></canvas>

            <div class="buttons">
                <button type="button" onclick="openCamera()" class="btn-camera">
                    <i class="fa fa-camera"></i> كاميرا
                </button>

                <button type="button" onclick="openUpload()" class="btn-upload">
                    <i class="fa fa-upload"></i> رفع صورة
                </button>
            </div>

            <!-- زر التقاط صورة -->
            <div class="buttons" id="captureBox" style="display:none; margin-top:8px; gap:8px;">
    
          <button type="button" onclick="capturePhoto()" class="btn-camera">
        <i class="fa fa-camera-retro"></i> التقاط صورة
           </button>

    <!-- زر إغلاق الكاميرا -->
           <button type="button" onclick="closeCamera()" class="btn-close-camera">
        <i class="fa fa-times"></i> إغلاق الكاميرا
            </button>

</div>


            <div class="form-group">
                <label>اسم الطالب</label>
                <input type="text" name="name" placeholder="ادخل اسم الطالب" required>
            </div>

            <div class="form-group">
                <label>رقم القيد</label>
                <input type="text" name="student_number" placeholder="ادخل رقم القيد" required>
            </div>

            <div class="form-group">
                <label>المستوى</label>
                <select name="level" required>
                    <option value="">اختر المستوى</option>
                    <option>الأول</option>
                    <option>الثاني</option>
                    <option>الثالث</option>
                    <option>الرابع</option>
                </select>
            </div>

            <div class="form-group">
                <label>التخصص</label>
                <select name="major" required>
                    <option value="">اختر التخصص</option>
                    <option value="IT">IT</option>
                    <option value="CS">CS</option>
                </select>
            </div>

            <button type="submit" class="btn-save">
                <i class="fa fa-save"></i> حفظ الطالب
            </button>

        </form>
    </div>

    <!-- ===== جدول الطلاب ===== -->
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>رقم الطالب</th>
                    <th>الاسم</th>
                    <th>رقم القيد</th>
                    <th>المستوى</th>
                    <th>التخصص</th>
                    <th>الصورة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($students): foreach ($students as $student): ?>
                <tr>
                    <td><?= $student["student_id"] ?></td>
                    <td><?= htmlspecialchars($student["name"]) ?></td>
                    <td><?= htmlspecialchars($student["student_number"]) ?></td>
                    <td><?= htmlspecialchars($student["level"]) ?></td>
                    <td><?= htmlspecialchars($student["major"]) ?></td>
                    <td>
                        <?php if ($student["image_path"]): ?>
                            <img src="<?= $student["image_path"] ?>" class="student-img">
                        <?php else: ?>
                            <i class="fa fa-user-circle"></i>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="actions">
                            <button class="btn-edit"><i class="fa fa-edit"></i></button>
                            <button class="btn-delete"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="7">لا يوجد طلاب</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
<script src="students.js"></script>
</body>
</html>

<?php
session_start();
require "db.php";
//من اجل يسترجع القيم مثلا رقم القيد اقل من ثمانيه احرف يطبع رساله ويمسح حقل القيد فقط
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

// من اجل تلوين حقل القيد في حاله الخطا
// $old = $_SESSION['old'] ?? [];
// $errorField = $_SESSION['error_field'] ?? null;

// unset($_SESSION['old'], $_SESSION['error_field']);

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
<link rel="stylesheet" href="alerts.css">
<link rel="stylesheet" href="coo.css">
</head>
<body>
<!-- ✅  الرسائل  مثل تم حفظ الطالب او تكرار رقم القيد-->
<?php if (!empty($_SESSION['flash'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    showAlert(
        <?= json_encode($_SESSION['flash']['message']) ?>,
        <?= json_encode($_SESSION['flash']['type']) ?>
    );
});
</script>
<?php unset($_SESSION['flash']); endif; ?>


<div class="container">

<!-- ===== بطاقة إضافة طالب ===== -->
<div class="student-card">

<h3>إضافة طالب</h3>
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

    <!-- التقاط / إغلاق -->
    <div class="buttons" id="captureBox" style="display:none; gap:8px;">
        <button type="button" onclick="capturePhoto()" class="btn-camera">
            <i class="fa fa-camera-retro"></i> التقاط صورة
        </button>

        <button type="button" onclick="closeCamera()" class="btn-close-camera">
            <i class="fa fa-times"></i> إغلاق الكاميرا
        </button>
    </div>

    <div class="form-group">
        <label>اسم الطالب</label>
        <input type="text" name="name" required  value="<?= htmlspecialchars($old['name'] ?? '') ?>">

    </div>

    <div class="form-group">
        <label>رقم القيد</label>
<div class="form-group">
    <label>رقم القيد</label>
    <input 
        type="text"
        name="student_number"
        class="<?= $errorField === 'student_number' ? 'input-error' : '' ?>"
        maxlength="8"
        autocomplete="off"
        required
    >
</div>


    </div>

    <div class="form-group">
        <label>المستوى</label>
        <select name="level" required>
    <option value="">اختر المستوى</option>
    <option <?= ($old['level'] ?? '') === 'الأول' ? 'selected' : '' ?>>الأول</option>
    <option <?= ($old['level'] ?? '') === 'الثاني' ? 'selected' : '' ?>>الثاني</option>
    <option <?= ($old['level'] ?? '') === 'الثالث' ? 'selected' : '' ?>>الثالث</option>
    <option <?= ($old['level'] ?? '') === 'الرابع' ? 'selected' : '' ?>>الرابع</option>
</select>

    </div>

    <div class="form-group">
        <label>التخصص</label>
        <select name="major" required>
    <option value="">اختر التخصص</option>
    <option value="IT" <?= ($old['major'] ?? '') === 'IT' ? 'selected' : '' ?>>IT</option>
    <option value="CS" <?= ($old['major'] ?? '') === 'CS' ? 'selected' : '' ?>>CS</option>
</select>

    </div>

    <button type="submit" class="btn-save">
        <i class="fa fa-save"></i> حفظ الطالب
    </button>

</form>
</div>

<!-- ===== جدول الطلاب ===== -->
<div class="table-card">

<div class="search-box">
    <input type="text" id="studentSearch" placeholder="ابحث بالاسم أو رقم القيد">
    <i class="fa fa-search"></i>
</div>

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
            <img src="<?= htmlspecialchars($student["image_path"]) ?>" class="student-img">
        <?php else: ?>
            <i class="fa fa-user-circle"></i>
        <?php endif; ?>
    </td>
    <td>
        <div class="actions">
            <button class="btn-edit" data-id="<?= $student['student_id'] ?>">
                <i class="fa fa-edit"></i>
            </button>

          <button type="button" class="btn-delete" data-id="<?= $student['student_id'] ?>">
         <i class="fa fa-trash"></i>
     </button>

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
<script src="search.js"></script>
<script src="delete.js"></script>
<script src="main.js"></script>
</body>
</html>

<?php
session_start();
require "db.php";

/* لاحقًا نضيف حماية المراقب */
// require "auth_supervisor.php";

/* جلب الطلاب من قاعدة البيانات
   ASC = الأقدم في الأعلى، الجديد في الأسفل */
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
</head>
<body>

<div class="container">

    <!-- ===== بطاقة الطالب (يمين) ===== -->
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

            <div class="form-group">
                <label>اسم الطالب</label>
                <input  placeholder="ادخل اسم الطالب" type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>رقم القيد</label>
                <input  placeholder="ادخل رقم القيد " type="text" name="student_number" required>
            </div>

            <div class="form-group">
                <label>المستوى</label>
                <select name="level" required>
                    <option value=""> اختر المستوى</option>
                    <option value="الأول">الأول</option>
                    <option value="الثاني">الثاني</option>
                    <option value="الثالث">الثالث</option>
                    <option value="الرابع">الرابع</option>
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

    <!-- ===== جدول الطلاب (يسار) ===== -->
    <div class="table-card">

        <div class="search-box">
            <input type="text" placeholder="ابحث بالاسم أو رقم القيد">
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
            <?php if ($students): ?>
                <?php foreach ($students as $student): ?>
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

                    <!-- ✅ الإجراءات -->
                    <td>
                        <div class="actions">
                            <button 
                                class="btn-edit"
                                data-id="<?= $student['student_id'] ?>"
                                title="تعديل الطالب">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button 
                                class="btn-delete"
                                data-id="<?= $student['student_id'] ?>"
                                title="حذف الطالب">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;">لا يوجد طلاب</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

    </div>

</div>
</body>
</html>

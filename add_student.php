<?php
session_start();
/* من اجل تلوين الحقل اذا كان اقل من 8 ارقام مع الحفاظ ع بقيه القيم*/
$old = $_SESSION['old'] ?? [];
$errorField = $_SESSION['error_field'] ?? null;

unset($_SESSION['old'], $_SESSION['error_field']);
require "db.php";

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
<style>
    .modal-content{
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
    }
     .avatar-buttons{
    display: flex !important;
    justify-content: space-evenly !important;
}
.avatar-buttons{
    display: flex !important;
    justify-content: space-evenly !important;
}
/* زر تغيير الصورة - سماوي */
.edit-avatar .btn-change {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 18px;
  border: none;
  border-radius:24px;

  background-color: #3498db; /* سماوي */
  color: #fff;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;

  box-shadow: 0 4px 6px rgba(52, 152, 219, 0.4);
  transition: all 0.2s ease;
}

/* أيقونة داخل الزر */
.edit-avatar .btn-change i {
  font-size: 15px;
}

/* عند hover أو الضغط */
.edit-avatar .btn-change:hover {
  background-color: #5dade2; /* سماوي فاتح */
  box-shadow: 0 6px 12px rgba(52, 152, 219, 0.45);
  transform: translateY(-2px);
}

.edit-avatar .btn-change:active {
  background-color: #85c1e9; /* سماوي خفيف */
  box-shadow: 0 3px 6px rgba(52, 152, 219, 0.35);
  transform: translateY(0);
}

/* زر الحذف - أحمر */
/* زر الحذف - أحمر */
.edit-avatar .btn-remove {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 18px;
  border: none;
  border-radius: 24px;

  background-color: #e74c3c; /* أحمر */
  color: #fff;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;

  box-shadow: 0 4px 6px rgba(231, 76, 60, 0.4);
  transition: all 0.2s ease;
}

.edit-avatar .btn-remove i {
  font-size: 15px;
}

.edit-avatar .btn-remove:hover {
  background-color: #ec7063; /* أحمر فاتح */
  box-shadow: 0 6px 12px rgba(231, 76, 60, 0.45);
  transform: translateY(-2px);
}

.edit-avatar .btn-remove:active {
  background-color: #f1948a; /* أحمر خفيف */
  box-shadow: 0 3px 6px rgba(231, 76, 60, 0.35);
  transform: translateY(0);
}

.btn-save-edit {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;

  background: linear-gradient(135deg, #2ecc71, #27ae60);
  color: #fff;

  padding: 12px 22px;
  border-radius: 10px;
  border: none;

  font-size: 15px;
  font-weight: 600;
  letter-spacing: 0.3px;

  cursor: pointer;

  box-shadow: 0 10px 25px rgba(39, 174, 96, 0.35);
  transition:
    transform 0.25s ease,
    box-shadow 0.25s ease,
    background 0.25s ease;
    width: 100%;
}

/* الأيقونة */
.btn-save-edit i {
  font-size: 16px;
}

/* Hover */
.btn-save-edit:hover {
  background: linear-gradient(135deg, #4fe38a, #2ecc71);
  transform: translateY(-2px);
  box-shadow: 0 14px 32px rgba(39, 174, 96, 0.45);
}

/* Active */
.btn-save-edit:active {
  transform: translateY(0);
  box-shadow: 0 6px 15px rgba(39, 174, 96, 0.3);
}

/* Disabled */
.btn-save-edit:disabled {
  background: linear-gradient(135deg, #a5d6a7, #81c784);
  cursor: not-allowed;
  box-shadow: none;
  opacity: 0.8;
}


</style>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="students.css">
<link rel="stylesheet" href="alerts.css">
<link rel="stylesheet" href="coo.css">
<link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- ===== نافذة تعديل الطالب ===== -->
     <!-- ===== نافذة تعديل الطالب ===== -->
<!-- ===== نافذة تعديل الطالب ===== -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>✏️ تعديل بيانات الطالب</h3>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form id="editForm" enctype="multipart/form-data">
            <!-- الحقول المخفية -->
            <input type="hidden" name="student_id" id="edit_student_id">
            <input type="hidden" id="delete_image_flag" name="delete_image" value="0">
            <input type="hidden" id="has_original_image" value="0">
            
            <!-- قسم الصورة -->
            <div class="edit-avatar">
                <img id="edit_avatar_preview" src="assets/img/default.png" alt="صورة الطالب" class="avatar-image">
                
                <input type="file" name="student_image" id="edit_image_input" hidden accept="image/jpeg,image/jpg,image/png">
                
                <div class="avatar-buttons">
                    <button type="button" onclick="openEditUpload()" class="btn-change">
                        <i class="fa fa-image"></i> تغيير الصورة
                    </button>
                    
                    <button type="button" id="removeImageBtn" onclick="removeEditImage()" class="btn-remove">
                        <i class="fa fa-trash"></i> حذف الصورة
                    </button>
                </div>
                
                <!-- رسالة التحذير -->
                <div id="editImageWarning" class="image-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span id="warningText">يجب إضافة صورة جديدة للمتابعة</span>
                </div>
            </div>
            
            <!-- حقل الاسم -->
            <div class="form-group">
                <label><i class="fa fa-user"></i> اسم الطالب</label>
                <input type="text" name="name" id="edit_name" required>
            </div>
            
            <!-- حقل رقم القيد -->
            <div class="form-group">
                <label><i class="fa fa-id-card"></i> رقم القيد</label>
                <input type="text" id="edit_student_number" disabled>
            </div>
            
            <!-- حقل المستوى -->
            <div class="form-group">
                <label><i class="fa fa-graduation-cap"></i> المستوى</label>
                <select name="level" id="edit_level" required>
                    <option value="">-- اختر المستوى --</option>
                    <option value="الأول">الأول</option>
                    <option value="الثاني">الثاني</option>
                    <option value="الثالث">الثالث</option>
                    <option value="الرابع">الرابع</option>
                </select>
            </div>
            
            <!-- حقل التخصص -->
            <div class="form-group">
                <label><i class="fa fa-book"></i> التخصص</label>
                <select name="major" id="edit_major" required>
                    <option value="">-- اختر التخصص --</option>
                    <option value="IT">تقنية المعلومات (IT)</option>
                    <option value="CS">علوم الحاسب (CS)</option>
                </select>
            </div>
            <!-- div -->
               <button type="submit" id="edit_save_btn" class="btn-save-edit">
          <i class="fa fa-save"></i> حفظ التعديلات
           </button>
            <!-- </div> -->
        </form>
    </div>
</div>
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
<form id="addStudentForm" data-form-action="add" action="save_student.php" method="POST" enctype="multipart/form-data">
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
         <label><i class="fa fa-user"></i> اسم الطالب</label>
        <input type="text" name="name" required  
        placeholder="يجب ان يكون الاسم ع الاقل 4 اسماء مع مسافه "
        value="<?= htmlspecialchars($old['name'] ?? '') ?>"
       class="<?= $errorField === 'name' ? 'input-error' : '' ?>"
        >

    </div>

    <div class="form-group">
     <label><i class="fa fa-id-card"></i> رقم القيد</label>
    <input 
        type="text"
        name="student_number"
          value="<?= htmlspecialchars($old['student_number'] ?? '') ?>"
          class="<?= ($errorField === 'student_number') ? 'input-error' : '' ?>"
        maxlength="8"
        autocomplete="off"
        required 
        placeholder="يجب ان يكون رقم القيد(8 ارقام فقط)"

    >
</div>

    <div class="form-group">
        <label><i class="fa fa-graduation-cap"></i> المستوى</label>
        <select name="level" required>
    <option value="">اختر المستوى</option>
    <option <?= ($old['level'] ?? '') === 'الأول' ? 'selected' : '' ?>>الأول</option>
    <option <?= ($old['level'] ?? '') === 'الثاني' ? 'selected' : '' ?>>الثاني</option>
    <option <?= ($old['level'] ?? '') === 'الثالث' ? 'selected' : '' ?>>الثالث</option>
    <option <?= ($old['level'] ?? '') === 'الرابع' ? 'selected' : '' ?>>الرابع</option>
</select>

    </div>

    <div class="form-group">
       <label><i class="fa fa-book"></i> التخصص</label>
        <select name="major" required>
    <option value="">اختر التخصص</option>
    <option value="IT" <?= ($old['major'] ?? '') === 'IT' ? 'selected' : '' ?>>IT</option>
    <option value="CS" <?= ($old['major'] ?? '') === 'CS' ? 'selected' : '' ?>>CS</option>
</select>

    </div>

    <button type="submit" class="btn-save">
        <i class="fa fa-save"></i> اضافة الطالب
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
<script src="add.js"></script>
<script src="students.js"></script>
<script src="search.js"></script>
<script src="edit.js"></script>
<script src="delete.js"></script>
<script src="main.js"></script>
</body>
</html>

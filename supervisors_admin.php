<!-- صفحه اضافه المراقبين -->
 <?php
session_start();
require "db.php";

$stmt = $pdo->query("SELECT supervisor_id, name, role FROM supervisors ORDER BY supervisor_id ASC");
$supervisors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total     = count($supervisors);
$adminCnt  = count(array_filter($supervisors, fn($s) => $s['role'] === 'admin'));
$supCnt    = count(array_filter($supervisors, fn($s) => $s['role'] === 'supervisor'));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المراقبين</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="supervisors_admin.css">
</head>
<body>

<!-- Hamburger -->
<!-- <div class="hamburger-btn" onclick="toggleSidebar()">
    <span></span><span></span><span></span>
</div> -->

<!-- Overlay -->
<!-- <div class="sidebar-overlay" onclick="toggleSidebar()"></div> -->

<!-- Sidebar -->
<!-- <div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <h3 class="sidebar-title">جامعة شبوة</h3>
        <p class="sidebar-subtitle">نظام مراقبة الاختبارات</p>
    </div>
    <div class="sidebar-menu">
        <a href="notifications.php">
            <i class="fa-solid fa-bell"></i>
            <span>الإشعارات</span>
        </a>
        <a href="reports.php">
            <i class="fa-solid fa-chart-line"></i>
            <span>التقارير</span>
        </a>
        <a href="student.php">
            <i class="fa-solid fa-user-plus"></i>
            <span>إضافة طالب</span>
        </a>
        <a href="supervisors_admin.php" class="active">
            <i class="fa-solid fa-user-shield"></i>
            <span>إدارة المراقبين</span>
        </a>
        <a href="login.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>تسجيل الخروج</span>
        </a>
    </div>
</div> -->

<!-- Modal تعديل -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa fa-user-edit"></i> تعديل بيانات المراقب</h3>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form id="editForm" autocomplete="off">
            <input type="hidden" name="supervisor_id" id="edit_supervisor_id">
            <div class="form-group">
                <label><i class="fa fa-user"></i> الاسم</label>
                <input type="text" name="name" id="edit_name" required placeholder="أدخل الاسم">
            </div>
            <div class="form-group">
                <label><i class="fa fa-lock"></i> كلمة المرور الجديدة</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="edit_password" placeholder="اتركها فارغة إن لم تريد تغييرها" autocomplete="new-password">
                    <span class="toggle-pass" onclick="togglePass('edit_password', this)">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label><i class="fa fa-shield-halved"></i> الصلاحية</label>
                <select name="role" id="edit_role" required>
                    <option value="supervisor">مراقب</option>
                    <option value="admin">مدير</option>
                </select>
            </div>
            <button type="submit" class="btn-save-edit">
                <i class="fa fa-save"></i> حفظ التعديلات
            </button>
        </form>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="toast"></div>

<!-- Main Content -->
<div class="main-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-text">
            <h1><i class="fa fa-user-shield"></i> إدارة المراقبين والمديرين</h1>
            <p>إضافة وتعديل وحذف حسابات المراقبين والمديرين</p>
        </div>
        <div class="header-stats">
            <div class="stat-box">
                <div class="stat-icon"><i class="fa fa-users"></i></div>
                <div class="stat-info">
                    <span class="stat-num" id="totalCount"><?= $total ?></span>
                    <span class="stat-label">إجمالي</span>
                </div>
            </div>
            <div class="stat-box stat-admin">
                <div class="stat-icon"><i class="fa fa-crown"></i></div>
                <div class="stat-info">
                    <span class="stat-num" id="adminCount"><?= $adminCnt ?></span>
                    <span class="stat-label">مديرون</span>
                </div>
            </div>
            <div class="stat-box stat-sup">
                <div class="stat-icon"><i class="fa fa-eye"></i></div>
                <div class="stat-info">
                    <span class="stat-num" id="supCount"><?= $supCnt ?></span>
                    <span class="stat-label">مراقبون</span>
                </div>
            </div>
        </div>
    </div>

    <div class="content-grid">

        <!-- فورم الإضافة -->
        <div class="add-card">
            <div class="add-card-header">
                <div class="add-card-icon"><i class="fa fa-user-plus"></i></div>
                <h3>إضافة مراقب جديد</h3>
            </div>
            <form id="addSupervisorForm" autocomplete="off">
                <div class="form-group">
                    <label><i class="fa fa-user"></i> الاسم الكامل</label>
                    <input type="text" name="name" id="add_name" required placeholder="أدخل الاسم الكامل" autocomplete="off">
                </div>
                <div class="form-group">
                    <label><i class="fa fa-lock"></i> كلمة المرور</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="add_password" required placeholder="أدخل كلمة المرور" autocomplete="new-password">
                        <span class="toggle-pass" onclick="togglePass('add_password', this)">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fa fa-shield-halved"></i> الصلاحية</label>
                    <select name="role" id="add_role" required>
                        <option value="">-- اختر الصلاحية --</option>
                        <option value="supervisor">مراقب</option>
                        <option value="admin">مدير</option>
                    </select>
                </div>
                <button type="submit" class="btn-save">
                    <i class="fa fa-plus-circle"></i> إضافة المراقب
                </button>
            </form>
        </div>

        <!-- جدول المراقبين -->
        <div class="table-card">
            <div class="table-toolbar">
                <div class="search-box">
                    <i class="fa fa-search"></i>
                    <input type="text" id="supervisorSearch" placeholder="ابحث بالاسم أو رقم المراقب...">
                </div>
                <div class="filter-btns">
                    <button class="filter-btn active" data-filter="all">
                        <i class="fa fa-list"></i> الكل
                    </button>
                    <button class="filter-btn" data-filter="admin">
                        <i class="fa fa-crown"></i> مديرون
                    </button>
                    <button class="filter-btn" data-filter="supervisor">
                        <i class="fa fa-eye"></i> مراقبون
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width:60px">رقم</th>
                            <th>الاسم</th>
                            <th>الصلاحية</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="supervisorsTable">
                        <?php if (empty($supervisors)): ?>
                        <tr>
                            <td colspan="4" class="no-data">
                                <i class="fa fa-users-slash"></i>
                                <p>لا يوجد مراقبون مضافون بعد</p>
                            </td>
                        </tr>
                        <?php else: foreach ($supervisors as $sup): ?>
                        <tr data-role="<?= $sup['role'] ?>">
                            <td><span class="id-badge"><?= $sup['supervisor_id'] ?></span></td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar <?= $sup['role'] === 'admin' ? 'avatar-admin' : 'avatar-sup' ?>">
                                        <?= mb_substr($sup['name'], 0, 1) ?>
                                    </div>
                                    <span class="user-name"><?= htmlspecialchars($sup['name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge <?= $sup['role'] === 'admin' ? 'badge-admin' : 'badge-supervisor' ?>">
                                    <i class="fa <?= $sup['role'] === 'admin' ? 'fa-crown' : 'fa-eye' ?>"></i>
                                    <?= $sup['role'] === 'admin' ? 'مدير' : 'مراقب' ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn-edit"
                                        data-id="<?= $sup['supervisor_id'] ?>"
                                        data-name="<?= htmlspecialchars($sup['name']) ?>"
                                        data-role="<?= $sup['role'] ?>"
                                        title="تعديل">
                                        <i class="fa fa-edit"></i> تعديل
                                    </button>
                                    <button class="btn-delete" data-id="<?= $sup['supervisor_id'] ?>" title="حذف">
                                        <i class="fa fa-trash"></i> حذف
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
    document.querySelector('.sidebar-overlay').classList.toggle('active');
    document.querySelector('.hamburger-btn').classList.toggle('active');
}

function togglePass(id, el) {
    const input = document.getElementById(id);
    const icon  = el.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function showToast(message, type) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast toast-' + type + ' show';
    setTimeout(() => toast.classList.remove('show'), 3500);
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.addEventListener('click', e => {
    if (e.target === document.getElementById('editModal')) closeEditModal();
});
</script>
<script src="supervisors_add.js"></script>
<script src="supervisors_edit.js"></script>
<script src="supervisors_delete.js"></script>
<script src="supervisors_search.js"></script>
</body>
</html>

<?php
session_start();
require "db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT id AS setting_id, setting_key, setting_value FROM settings ORDER BY id ASC");
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الإعدادات</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="settings_admin.css">
</head>
<body>

<!-- Hamburger -->
<div class="hamburger-btn" onclick="toggleSidebar()">
    <span></span><span></span><span></span>
</div>

<!-- Overlay -->
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo"><i class="fa-solid fa-graduation-cap"></i></div>
        <h3 class="sidebar-title">جامعة شبوة</h3>
        <p class="sidebar-subtitle">نظام مراقبة الاختبارات</p>
    </div>
    <div class="sidebar-menu">
        <a href="notifications.php"><i class="fa-solid fa-bell"></i><span>الإشعارات</span></a>
        <a href="reports.php"><i class="fa-solid fa-chart-line"></i><span>التقارير</span></a>
        <a href="student.php"><i class="fa-solid fa-user-plus"></i><span>إدارة الطلاب</span></a>
        <a href="supervisors_admin.php"><i class="fa-solid fa-user-shield"></i><span>إدارة المراقبين</span></a>
        <a href="settings_admin.php" class="active"><i class="fa-solid fa-gear"></i><span>الإعدادات</span></a>
        <a href="login.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i><span>تسجيل الخروج</span></a>
    </div>
</div>

<!-- Modal تعديل -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-header-info">
                <div class="modal-header-icon"><i class="fa fa-pen-to-square"></i></div>
                <div>
                    <h3>تعديل الإعداد</h3>
                    <p id="modal_key_label">المفتاح</p>
                </div>
            </div>
            <button class="modal-close" onclick="closeModal()"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body">
            <!-- بدون method POST — يُرسل عبر AJAX فقط -->
            <form id="editForm" autocomplete="off">
                <input type="hidden" id="edit_setting_id">
                <div class="form-group">
                    <label><i class="fa fa-key"></i> المفتاح</label>
                    <input type="text" id="edit_key" disabled>
                </div>
                <div class="form-group">
                    <label><i class="fa fa-pen"></i> القيمة</label>
                    <input type="text" id="edit_value" required placeholder="أدخل القيمة الجديدة">
                </div>
                <button type="submit" class="btn-save-edit">
                    <i class="fa fa-save"></i> حفظ التعديلات
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="toast"></div>

<!-- Main -->
<div class="main-wrapper">

    <div class="page-header">
        <div class="page-header-text">
            <h1><i class="fa fa-gear"></i> إعدادات النظام</h1>
            <p>إدارة وتعديل إعدادات نظام مراقبة الاختبارات</p>
        </div>
        <div class="header-stats">
            <div class="stat-box">
                <div class="stat-icon"><i class="fa fa-sliders"></i></div>
                <div class="stat-info">
                    <span class="stat-num"><?= count($settings) ?></span>
                    <span class="stat-label">إعداد</span>
                </div>
            </div>
        </div>
    </div>

    <div class="settings-grid">
        <?php foreach ($settings as $s):
            $key   = htmlspecialchars((string)($s['setting_key']   ?? ''));
            $value = htmlspecialchars((string)($s['setting_value'] ?? ''));
            $id    = (int)$s['setting_id'];
        ?>
        <div class="setting-card">
            <div class="setting-icon"><i class="fa fa-sliders"></i></div>
            <div class="setting-info">
                <span class="setting-key"><?= $key ?></span>
                <span class="setting-value"><?= $value ?></span>
            </div>
            <button class="btn-edit"
                data-id="<?= $id ?>"
                data-key="<?= $key ?>"
                data-value="<?= $value ?>">
                <i class="fa fa-edit"></i> تعديل
            </button>
        </div>
        <?php endforeach; ?>

        <?php if (empty($settings)): ?>
        <div class="no-data">
            <i class="fa fa-gear"></i>
            <p>لا توجد إعدادات مضافة بعد</p>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
    document.querySelector('.sidebar-overlay').classList.toggle('active');
    document.querySelector('.hamburger-btn').classList.toggle('active');
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.addEventListener('click', e => {
    if (e.target === document.getElementById('editModal')) closeModal();
});

function showToast(message, type) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast toast-' + (type || 'success') + ' show';
    setTimeout(() => toast.classList.remove('show'), 3500);
}
</script>
<script src="settings_edit.js"></script>
</body>
</html>

// ============================================
// edit_student_final.js (نسخة نهائية مدمجة)
// ============================================

document.addEventListener("DOMContentLoaded", function () {

    // ============================================
    // 🔔 نظام الرسائل بالأنيميشن
    // ============================================
    function showAlert(message, type = "error") {

        let container = document.querySelector(".alert-container");

        if (!container) {
            container = document.createElement("div");
            container.className = "alert-container";
            document.body.appendChild(container);
        }

        const alert = document.createElement("div");
        alert.className = `alert ${type}`;
        alert.innerHTML = `
            <i class="fa ${type === "success" ? "fa-check-circle" : "fa-times-circle"}"></i>
            <span>${message}</span>
        `;

        container.appendChild(alert);

        setTimeout(() => alert.classList.add("show"), 50);

        setTimeout(() => {
            alert.classList.remove("show");
            setTimeout(() => alert.remove(), 400);
        }, 3000);
    }

    // ============================================
    // العناصر
    // ============================================
    const editModal = document.getElementById("editModal");
    const editForm = document.getElementById("editForm");
    const editImageInput = document.getElementById("edit_image_input");
    const editAvatarPreview = document.getElementById("edit_avatar_preview");
    const removeImageBtn = document.getElementById("removeImageBtn");
    const editImageWarning = document.getElementById("editImageWarning");
    const hasOriginalImage = document.getElementById("has_original_image");
    const editSaveBtn = document.getElementById("edit_save_btn");

    let originalData = {};
    let originalImagePath = "";
    let imageWasDeleted = false;
    let hasNewImage = false;

    // ============================================
    // فتح نافذة التعديل
    // ============================================
    document.addEventListener("click", function (e) {

        const editBtn = e.target.closest(".btn-edit");
        if (!editBtn) return;

        e.preventDefault();

        const studentId = editBtn.dataset.id;
        if (!studentId) {
            showAlert("رقم الطالب غير موجود");
            return;
        }

        resetEditState();

        fetch(`get_student.php?id=${studentId}`)
            .then(res => res.json())
            .then(data => {

                if (data.status !== "success") {
                    showAlert(data.message);
                    return;
                }

                const s = data.data;

                // حفظ البيانات الأصلية للمقارنة
                originalData = {
                    name: s.name || "",
                    level: s.level || "",
                    major: s.major || ""
                };

                originalImagePath = s.image_path || "";
                hasOriginalImage.value =
                    (originalImagePath && !originalImagePath.includes("default.png")) ? "1" : "0";

                document.getElementById("edit_student_id").value = s.student_id;
                document.getElementById("edit_name").value = s.name || "";
                document.getElementById("edit_level").value = s.level || "";
                document.getElementById("edit_major").value = s.major || "";

                if (hasOriginalImage.value === "1") {
                    editAvatarPreview.src = originalImagePath;
                    removeImageBtn.style.display = "block";
                } else {
                    editAvatarPreview.src = "assets/img/default.png";
                    editImageWarning.style.display = "block";
                }

                editModal.style.display = "block";
                document.body.style.overflow = "hidden";
            });
    });

    // ============================================
    // إعادة تعيين الحالة
    // ============================================
    function resetEditState() {
        imageWasDeleted = false;
        hasNewImage = false;
        document.getElementById("delete_image_flag").value = "0";
        if (editImageWarning) editImageWarning.style.display = "none";
        if (removeImageBtn) removeImageBtn.style.display = "none";
        if (editImageInput) editImageInput.value = "";
    }

    // ============================================
    // رفع صورة جديدة
    // ============================================
    window.openEditUpload = () => editImageInput.click();

    editImageInput.addEventListener("change", function () {
        if (!this.files[0]) return;

        const reader = new FileReader();
        reader.onload = e => {
            editAvatarPreview.src = e.target.result;
            removeImageBtn.style.display = "block";
            editImageWarning.style.display = "none";
        };
        reader.readAsDataURL(this.files[0]);

        hasNewImage = true;
        imageWasDeleted = false;
        document.getElementById("delete_image_flag").value = "0";
    });

    // ============================================
    // حذف الصورة
    // ============================================
    window.removeEditImage = function () {
        editAvatarPreview.src = "assets/img/default.png";
        removeImageBtn.style.display = "none";
        editImageWarning.style.display = "block";
        editImageWarning.innerText = "⚠️ يجب إضافة صورة جديدة";
        document.getElementById("delete_image_flag").value = "1";
        imageWasDeleted = true;
        hasNewImage = false;
    };

    // ============================================
    // حفظ التعديلات
    // ============================================
    editForm.addEventListener("submit", function (e) {

        e.preventDefault();

        const name = document.getElementById("edit_name").value.trim();
        const level = document.getElementById("edit_level").value;
        const major = document.getElementById("edit_major").value;

        // 🔴 الاسم أقل من 4 كلمات
        if (name.split(/\s+/).length < 4) {
            showAlert("يجب إدخال الاسم الرباعي كاملًا");
            return;
        }

        // 🔴 لم يتم تعديل أي شيء
        const noTextChange =
            name === originalData.name &&
            level === originalData.level &&
            major === originalData.major;

        if (noTextChange && !hasNewImage && !imageWasDeleted) {
            showAlert("لم تقم بتعديل أي شيء في بيانات الطالب");
            return;
        }

        // 🔴 تحقق الصورة
        if (hasOriginalImage.value === "1" && imageWasDeleted && !hasNewImage) {
            showAlert("لا يمكن حذف الصورة دون إضافة صورة جديدة");
            return;
        }

        if (hasOriginalImage.value === "0" && !hasNewImage) {
            showAlert("يجب إضافة صورة للطالب");
            return;
        }

        const formData = new FormData(editForm);

        fetch("update_student.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    showAlert("تم التعديل بنجاح", "success");
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showAlert(data.message);
                }
            })
            .catch(() => {
                showAlert("خطأ في الاتصال بالخادم");
            });
    });

    // ============================================
    // إغلاق النافذة
    // ============================================
    window.closeEditModal = function () {
        editModal.style.display = "none";
        document.body.style.overflow = "auto";
    };

});

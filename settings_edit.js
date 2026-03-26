document.addEventListener("DOMContentLoaded", function () {

    // فتح المودال
    document.querySelectorAll(".btn-edit").forEach(btn => {
        btn.addEventListener("click", function () {
            document.getElementById("edit_setting_id").value      = this.dataset.id;
            document.getElementById("edit_key").value             = this.dataset.key;
            document.getElementById("edit_value").value           = this.dataset.value;
            document.getElementById("modal_key_label").textContent = this.dataset.key;
            document.getElementById("editModal").style.display    = "block";
        });
    });

    // حفظ التعديل عبر AJAX
    document.getElementById("editForm").addEventListener("submit", function (e) {
        e.preventDefault();

        const id    = document.getElementById("edit_setting_id").value;
        const value = document.getElementById("edit_value").value.trim();

        if (!value) {
            showToast("القيمة لا يمكن أن تكون فارغة", "error");
            return;
        }

        fetch("update_setting.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `setting_id=${encodeURIComponent(id)}&setting_value=${encodeURIComponent(value)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, "success");
                // تحديث الكارد مباشرة
                const btn = document.querySelector(`.btn-edit[data-id="${id}"]`);
                if (btn) {
                    btn.dataset.value = value;
                    btn.closest(".setting-card").querySelector(".setting-value").textContent = value;
                }
                closeModal();
            } else {
                showToast(data.message, "error");
            }
        })
        .catch(() => showToast("حدث خطأ في الاتصال", "error"));
    });

});

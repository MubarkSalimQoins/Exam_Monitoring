document.querySelectorAll(".btn-delete").forEach(btn => {
    btn.addEventListener("click", function () {
        const id = this.dataset.id;
        if (!confirm("هل أنت متأكد من حذف هذا المراقب؟")) return;

        fetch("delete_supervisor.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `supervisor_id=${id}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, "success");
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message, "error");
            }
        })
        .catch(() => showToast("حدث خطأ في الاتصال", "error"));
    });
});

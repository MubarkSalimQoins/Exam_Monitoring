document.querySelectorAll(".btn-edit").forEach(btn => {
    btn.addEventListener("click", function () {
        document.getElementById("edit_supervisor_id").value = this.dataset.id;
        document.getElementById("edit_name").value          = this.dataset.name;
        document.getElementById("edit_role").value          = this.dataset.role;
        document.getElementById("edit_password").value      = "";
        document.getElementById("editModal").style.display  = "block";
    });
});

function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}

document.getElementById("editForm").addEventListener("submit", function (e) {
    e.preventDefault();

    fetch("edit_supervisor.php", {
        method: "POST",
        body: new FormData(this)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, "success");
            closeEditModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, "error");
        }
    })
    .catch(() => showToast("حدث خطأ في الاتصال", "error"));
});

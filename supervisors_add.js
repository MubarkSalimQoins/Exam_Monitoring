document.getElementById("addSupervisorForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch("save_supervisor.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, "success");
            form.reset();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, "error");
        }
    })
    .catch(() => showToast("حدث خطأ في الاتصال", "error"));
});

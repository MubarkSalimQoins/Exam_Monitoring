/*************************************************
 * دالة عرض الرسائل بشكل صندوق منزلق
 *************************************************/
function showAlert(message, type) {
    // إنشاء حاوية الرسائل إذا لم تكن موجودة
    let container = document.querySelector(".alert-container");
    if (!container) {
        container = document.createElement("div");
        container.className = "alert-container";
        document.body.appendChild(container);
    }

    // إنشاء الرسالة
    const div = document.createElement("div");
    div.className = "alert " + type;
    div.innerHTML = `
        ${type === "success" ? '<i class="fa fa-circle-check"></i>' : '<i class="fa fa-circle-xmark"></i>'}
        <span>${message}</span>
    `;

    container.appendChild(div);

    // تفعيل الانزلاق
    setTimeout(() => div.classList.add("show"), 10);

    // إزالة الرسالة بعد 3 ثواني
    setTimeout(() => {
        div.classList.remove("show");
        setTimeout(() => div.remove(), 400);
    }, 3000);
}
document.addEventListener("click", function(e) {
    const button = e.target.closest(".btn-delete");
    if (!button) return;

    const studentId = button.dataset.id;
    const row = button.closest("tr");

    if (!studentId) return;

    if (!confirm("⚠️ هل أنت متأكد من حذف الطالب؟")) return;

    fetch("delete_student.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "student_id=" + encodeURIComponent(studentId)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            if (row) row.remove();
            showAlert(data.message, "success"); // هنا عرض الرسالة
        } else {
            showAlert(data.message, "error");   // هنا عرض الخطأ
        }
    })
    .catch(() => {
        showAlert("❌ حدث خطأ أثناء الحذف", "error");
    });
});

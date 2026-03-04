//main
function showAlert(message, type = "success") {

    // إنشاء الحاوية لو مش موجودة
    let container = document.querySelector(".toast-container");
    if (!container) {
        container = document.createElement("div");
        container.className = "toast-container";
        document.body.appendChild(container);
    }

    // إنشاء الرسالة
    const toast = document.createElement("div");
    toast.className = `toast ${type}`;

    const icon = type === "success"
        ? '<i class="fa fa-circle-check"></i>'
        : '<i class="fa fa-circle-xmark"></i>';

    toast.innerHTML = `${icon}<span>${message}</span>`;

    container.appendChild(toast);

    // الإخفاء بعد 3 ثواني
    setTimeout(() => {
        toast.style.animation = "toastOut 0.4s ease forwards";
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}
// من اجل رقم القيد لا يكون احرف ولا اكثر او اقل من 8 ارقام
document.addEventListener("DOMContentLoaded", function () {
    const studentNumber = document.getElementById("student_number");

    if (studentNumber) {
        studentNumber.addEventListener("input", function () {
            // السماح بالأرقام فقط
            this.value = this.value.replace(/\D/g, "");

            // منع أكثر من 8 أرقام
            if (this.value.length > 8) {
                this.value = this.value.slice(0, 8);
            }
        });
    }
});

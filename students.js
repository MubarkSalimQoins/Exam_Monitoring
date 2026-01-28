// ===============================
// ===============================
// دالة عرض الرسائل داخل .student-card
// ===============================
// ===============================
// دالة عرض الرسائل داخل .student-card
// ===============================
// رسائل التنبيه (تختفي بعد 3 ثواني)
// ===============================
document.addEventListener("DOMContentLoaded", function () {

    const alerts = document.querySelectorAll(".alert");

    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.animation = "slideFadeOut 0.4s ease forwards";
                setTimeout(() => alert.remove(), 400);
            });
        }, 3000);
    }

    // ===============================
    // منع الحفظ بدون صورة
    // ===============================
    const form = document.querySelector("form");
    const imageInput = document.getElementById("imageInput");

    if (form) {
        form.addEventListener("submit", function (e) {
            if (!imageInput || imageInput.files.length === 0) {
                e.preventDefault();
                alert("❌ يجب إضافة صورة للطالب");
            }
        });
    }
});

// ===============================
// متغيرات عامة للكاميرا
// ===============================
let cameraStream = null;

const imageInput   = document.getElementById("imageInput");
const avatarPreview = document.getElementById("avatarPreview");
const camera       = document.getElementById("camera");
const snapshot     = document.getElementById("snapshot");
const captureBox   = document.getElementById("captureBox");

// ===============================
// رفع صورة من الجهاز
// ===============================
function openUpload() {
    imageInput.click();
}

if (imageInput) {
    imageInput.addEventListener("change", function () {
        if (!this.files.length) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            avatarPreview.innerHTML = `<img src="${e.target.result}">`;
        };
        reader.readAsDataURL(this.files[0]);
    });
}

// ===============================
// فتح الكاميرا
// ===============================
function openCamera() {
    camera.hidden = false;

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            cameraStream = stream;
            camera.srcObject = stream;
            captureBox.style.display = "flex";
        })
        .catch(() => {
            alert("❌ لم يتم السماح باستخدام الكاميرا");
        });
}

// ===============================
// التقاط صورة من الكاميرا
// ===============================
function capturePhoto() {
    if (!cameraStream) return;

    snapshot.width  = camera.videoWidth;
    snapshot.height = camera.videoHeight;

    const ctx = snapshot.getContext("2d");
    ctx.drawImage(camera, 0, 0);

    const imageData = snapshot.toDataURL("image/png");
    avatarPreview.innerHTML = `<img src="${imageData}">`;

    fetch(imageData)
        .then(res => res.blob())
        .then(blob => {
            const file = new File([blob], "camera.png", { type: "image/png" });
            const dt = new DataTransfer();
            dt.items.add(file);
            imageInput.files = dt.files;
        });

    cameraStream.getTracks().forEach(track => track.stop());
    cameraStream = null;

    camera.hidden = true;
    captureBox.style.display = "none";
}
//  * إغلاق الكاميرا
function closeCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }

    camera.srcObject = null;
    camera.hidden = true;

    captureBox.style.display = "none";
}

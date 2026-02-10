// ============================================
// add_student.js
// خاص بإضافة طالب فقط (لا يعمل في التعديل)
// ============================================

document.addEventListener("DOMContentLoaded", function () {

    // ===============================
    // التحقق: هل نحن في صفحة الإضافة؟
    // ===============================
    const addForm = document.getElementById("addStudentForm");
    if (!addForm) return; // 🔥 خروج نهائي لو صفحة تعديل

    // ===============================
    // عناصر الإضافة
    // ===============================
    const imageInputAdd = document.getElementById("imageInput");
    const avatarPreview = document.getElementById("avatarPreview");
    const camera        = document.getElementById("camera");
    const snapshot      = document.getElementById("snapshot");
    const captureBox    = document.getElementById("captureBox");

    // ===============================
    // رسائل التنبيه
    // ===============================
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
    // منع الإرسال بدون صورة (إضافة فقط)
    // ===============================
//     addForm.addEventListener("submit", function (e) {

//     if (!imageInputAdd || imageInputAdd.files.length === 0) {
//         e.preventDefault(); // يمنع الإرسال
//         alert("❌ يجب إضافة صورة للطالب");
//         return false; // ⬅️ هذا المهم
//     }

//   });


    // ===============================
    // رفع صورة من الجهاز
    // ===============================
    window.openUpload = function () {
        imageInputAdd.click();
    };

    imageInputAdd.addEventListener("change", function () {
        if (!this.files.length) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            avatarPreview.innerHTML = `<img src="${e.target.result}">`;
        };
        reader.readAsDataURL(this.files[0]);
    });

    // ===============================
    // متغيرات الكاميرا
    // ===============================
    let cameraStream = null;

    // ===============================
    // فتح الكاميرا
    // ===============================
    window.openCamera = function () {
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
    };

    // ===============================
    // التقاط صورة
    // ===============================
    window.capturePhoto = function () {
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
                imageInputAdd.files = dt.files;
            });

        closeCamera();
    };

    // ===============================
    // إغلاق الكاميرا
    // ===============================
    window.closeCamera = function () {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }

        camera.srcObject = null;
        camera.hidden = true;
        captureBox.style.display = "none";
    };

});

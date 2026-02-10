// ============================================
// add_student.js
// خاص بإضافة طالب فقط
// ============================================

// document.addEventListener("DOMContentLoaded", function () {

//     const addForm    = document.getElementById("addStudentForm");
//     const imageInput = document.getElementById("imageInput");

//     if (!addForm) return;

//     addForm.addEventListener("submit", function (e) {
//         if (!imageInput || imageInput.files.length === 0) {
//             e.preventDefault();
//             alert("❌ يجب إضافة صورة للطالب");
//         }
//     });

// });

// ============================================
// add_student.js
// خاص بإضافة طالب فقط
// ============================================

// add_student.js
// add_student.js
// add.js
// add_only.js - خاص بالإضافة فقط
// add_only.js - خاص فقط بإضافة الطالب
document.addEventListener("DOMContentLoaded", function () {
    console.log("add_only.js: جارِ التحقق من نموذج الإضافة...");
    
    // الطريقة الأكيدة: البحث عن النموذج بطريقة فريدة
    const addForm = document.querySelector('form[action="save_student.php"]');
    
    if (!addForm) {
        console.log("add_only.js: نموذج الإضافة غير موجود");
        return;
    }
    
    // تأكد أن هذا هو نموذج الإضافة وليس التعديل
    const formId = addForm.getAttribute('id');
    if (formId !== 'addStudentForm') {
        console.log("add_only.js: هذا ليس نموذج الإضافة (ID مختلف)");
        return;
    }
    
    // تأكد أنه ليس داخل المودال
    const isInModal = addForm.closest('.modal, #editModal');
    if (isInModal) {
        console.log("add_only.js: النموذج داخل مودال، هذا هو نموذج التعديل");
        return;
    }
    
    console.log("add_only.js: تم العثور على نموذج الإضافة، تفعيل التحقق");
    
    const imageInput = document.getElementById("imageInput");
    
    addForm.addEventListener("submit", function (e) {
        console.log("add_only.js: تحقق من صورة الإضافة...");
        
        // تحقق من وجود صورة
        if (!imageInput || imageInput.files.length === 0) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // عرض رسالة خطأ
            showAlert("يجب إضافة صورة للطالب قبل حفظ البيانات", "error");
            
            // جعل زر رفع الصورة يلمع
            const uploadBtn = document.querySelector('.btn-upload');
            if (uploadBtn) {
                uploadBtn.classList.add('required-field');
                setTimeout(() => uploadBtn.classList.remove('required-field'), 2000);
            }
            
            return false;
        }
        
        console.log("add_only.js: الصورة موجودة، متابعة الإرسال");
        return true;
    });
});
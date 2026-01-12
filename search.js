// كود البحث مع التميز
// document.addEventListener("DOMContentLoaded", function () {
//     const searchInput = document.getElementById("studentSearch");
//     if (!searchInput) return;

//     const tableRows = document.querySelectorAll(".table-card table tbody tr");

//     // أعمدة النصوص
//     const columns = {
//         name: 1,
//         studentNumber: 2,
//         level: 3,
//         major: 4
//     };

//     searchInput.addEventListener("input", function () {
//         const filter = this.value.toLowerCase().trim();

//         tableRows.forEach(row => {
//             let rowMatches = false;

//             // الأعمدة التي تحتوي نصوص فقط
//             Object.values(columns).forEach(index => {
//                 const td = row.children[index]; // العمود المحدد
//                 const originalText = td.textContent;
//                 td.innerHTML = originalText; // إعادة النص الأصلي

//                 if (filter !== "" && originalText.toLowerCase().includes(filter)) {
//                     td.innerHTML = originalText.replace(
//                         new RegExp(`(${filter})`, "gi"),
//                         `<mark>$1</mark>`
//                     );
//                     rowMatches = true;
//                 }
//             });

//             // الأعمدة الأخرى مثل الصور والأزرار: نتحقق فقط لفلترة الصف
//             [0,5,6].forEach(index => {
//                 const td = row.children[index];
//                 if (td.textContent.toLowerCase().includes(filter)) {
//                     rowMatches = true;
//                 }
//             });

//             // إظهار أو إخفاء الصف
//             row.style.display = rowMatches ? "" : "none";
//         });
//     });
// });

document.addEventListener("DOMContentLoaded", function () { 
    // ننتظر حتى يتم تحميل كامل الصفحة قبل تنفيذ الكود

    const searchInput = document.getElementById("studentSearch"); 
    // نحدد مربع البحث الذي يكتبه المستخدم

    if (!searchInput) return; 
    // إذا لم نجد مربع البحث (ID خاطئ أو غير موجود) نوقف الكود

    const tableRows = document.querySelectorAll(".table-card table tbody tr"); 
    // نجلب جميع صفوف الجدول داخل tbody

    // الأعمدة النصية التي نطبق عليها البحث وتمييز النصوص
    const textColumns = {
        name: 1,          // العمود 1 = الاسم
        studentNumber: 2, // العمود 2 = رقم القيد
        level: 3,         // العمود 3 = المستوى
        major: 4          // العمود 4 = التخصص
    };

    searchInput.addEventListener("input", function () { 
        // عند كتابة أي شيء في مربع البحث
        const filter = this.value.toLowerCase().trim(); 
        // نص البحث بعد تحويله إلى أحرف صغيرة وإزالة المسافات الزائدة

        tableRows.forEach(row => { 
            // نكرر على كل صف في الجدول
            let rowMatches = false; 
            // متغير لتحديد ما إذا كان الصف يحتوي أي تطابق

            row.style.backgroundColor = ""; 
            // إعادة لون الصف الافتراضي قبل أي تغييرات

            // معالجة الأعمدة النصية فقط
            Object.values(textColumns).forEach(index => { 
                const td = row.children[index]; 
                // نحدد العمود النصي في هذا الصف
                const originalText = td.textContent; 
                // نأخذ النص الأصلي
                td.innerHTML = originalText; 
                // نعيد النص الأصلي أولاً لإزالة أي علامات <mark> قديمة

                if (filter !== "" && originalText.toLowerCase().includes(filter)) { 
                    // إذا وجد النص المطابق للبحث
                    const regex = new RegExp(`(${filter})`, "gi"); 
                    // إنشاء تعبير منتظم لتحديد النص المطابق
                    td.innerHTML = originalText.replace(regex, `<mark>$1</mark>`); 
                    // نغلف النص المطابق بعلامة <mark> لتمييزه
                    rowMatches = true; 
                    // الصف يحتوي تطابق
                }
            });

            // الأعمدة الأخرى مثل الصور والأزرار: نتحقق منها فقط لفلترة الصف
            [0,5,6].forEach(index => { 
                const td = row.children[index];
                if (td.textContent.toLowerCase().includes(filter)) { 
                    rowMatches = true; 
                    // إذا ظهر النص في الأعمدة غير النصية، نعتبر الصف مطابقًا لكن لا نغيره
                }
            });

            // إظهار أو إخفاء الصف بناءً على وجود أي تطابق
            row.style.display = rowMatches ? "" : "none"; 

            // تلوين الصف كامل عند وجود أي تطابق
            if (rowMatches && filter !== "") { 
                row.style.backgroundColor = "#ffeaa7"; 
                // لون الخلفية للصف كامل
                row.style.transition = "background-color 0.3s ease"; 
                // انتقال سلس عند تغيير اللون
            }
        });
    });
});

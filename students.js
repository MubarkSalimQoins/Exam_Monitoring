// let studentCounter = 1;
// let selectedImage = "";

// /* ===== رفع صورة ===== */
// function openUpload() {
//     document.getElementById("imageInput").click();
// }

// document.getElementById("imageInput").addEventListener("change", function (e) {
//     const file = e.target.files[0];
//     if (!file) return;

//     const reader = new FileReader();
//     reader.onload = function () {
//         selectedImage = reader.result;
//         document.getElementById("avatarPreview").innerHTML =
//             `<img src="${reader.result}">`;
//     };
//     reader.readAsDataURL(file);
// });

// /* ===== فتح الكاميرا (معاينة فقط الآن) ===== */
// function openCamera() {
//     alert("📷 سيتم ربط الكاميرا لاحقًا (الواجهة جاهزة)");
// }

// /* ===== إضافة صف للجدول ===== */
// function addStudentRow() {
//     const name   = document.getElementById("student_name").value;
//     const number = document.getElementById("student_number").value;
//     const level  = document.getElementById("student_level").value;
//     const major  = document.getElementById("student_major").value;

//     if (!name || !number) {
//         alert("⚠️ يرجى إدخال اسم الطالب ورقم القيد");
//         return;
//     }

//     const table = document.getElementById("studentsTable");

//     const row = `
//         <tr>
//             <td>${studentCounter++}</td>
//             <td>${name}</td>
//             <td>${number}</td>
//             <td>${level}</td>
//             <td>${major}</td>
//             <td>
//                 ${selectedImage 
//                     ? `<img src="${selectedImage}" class="student-img">`
//                     : `<div class="student-img"></div>`
//                 }
//             </td>
//         </tr>
//     `;

//     table.insertAdjacentHTML("beforeend", row);

//     // تفريغ الحقول
//     document.getElementById("student_name").value = "";
//     document.getElementById("student_number").value = "";
//     document.getElementById("avatarPreview").innerHTML =
//         `<i class="fa-solid fa-user"></i>`;
//     selectedImage = "";
// }

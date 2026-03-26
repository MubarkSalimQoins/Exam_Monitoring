// البحث النصي
document.getElementById("supervisorSearch").addEventListener("input", filterTable);

// أزرار الفلتر
document.querySelectorAll(".filter-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
        this.classList.add("active");
        filterTable();
    });
});

function filterTable() {
    const query      = document.getElementById("supervisorSearch").value.trim().toLowerCase();
    const activeBtn  = document.querySelector(".filter-btn.active");
    const roleFilter = activeBtn ? activeBtn.dataset.filter : "all";
    const rows       = document.querySelectorAll("#supervisorsTable tr");

    rows.forEach(row => {
        const name = row.cells[1]?.textContent.toLowerCase() ?? "";
        const id   = row.cells[0]?.textContent.toLowerCase() ?? "";
        const role = row.dataset.role ?? "";

        const matchSearch = name.includes(query) || id.includes(query);
        const matchRole   = roleFilter === "all" || role === roleFilter;

        row.style.display = (matchSearch && matchRole) ? "" : "none";
    });
}

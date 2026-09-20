document.addEventListener("DOMContentLoaded", function () {
    const editModal = document.getElementById("editModal");
    const deleteModal = document.getElementById("deleteModal");

    document.querySelectorAll(".edit-btn").forEach(function (button) {
        button.addEventListener("click", function () {
            document.getElementById("editId").value = this.dataset.id;
            document.getElementById("editTitle").value = this.dataset.title;
            document.getElementById("editSubject").value = this.dataset.subject;
            document.getElementById("editSemester").value = this.dataset.semester;
            document.getElementById("editType").value = this.dataset.type;
            document.getElementById("editUploaded").value = this.dataset.uploaded;
            editModal.classList.remove("hidden");
        });
    });

    document.querySelectorAll(".delete-btn").forEach(function (button) {
        button.addEventListener("click", function () {
            document.getElementById("deleteId").value = this.dataset.id;
            deleteModal.classList.remove("hidden");
        });
    });

    document.getElementById("closeEdit").addEventListener("click", function () {
        editModal.classList.add("hidden");
    });

    document.getElementById("cancelDelete").addEventListener("click", function () {
        deleteModal.classList.add("hidden");
    });

    window.addEventListener("click", function (event) {
        if (event.target === editModal) editModal.classList.add("hidden");
        if (event.target === deleteModal) deleteModal.classList.add("hidden");
    });

    // Extra feature: search/filter the material table
    const searchBox = document.getElementById("searchBox");
    searchBox.addEventListener("input", function () {
        const search = this.value.toLowerCase();
        document.querySelectorAll("#materialTable tbody tr").forEach(function (row) {
            row.style.display = row.innerText.toLowerCase().includes(search) ? "" : "none";
        });
    });
});
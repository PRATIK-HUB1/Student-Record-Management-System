document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("studentSearch");
    const tableBody = document.getElementById("studentsTableBody");

    if (!searchInput || !tableBody) {
        return;
    }

    const originalTable = tableBody.innerHTML;

    searchInput.addEventListener("keyup", function () {
        const query = this.value.trim();

        if (query === "") {
            tableBody.innerHTML = originalTable;
            return;
        }

        fetch("/Student_Record_Management_System/public/ajax/search_students.php?q=" + encodeURIComponent(query))


            .then(response => response.text())
            .then(html => {
                tableBody.innerHTML = html;
            })
            .catch(error => {
                console.error("Search error:", error);
            });
    });

});

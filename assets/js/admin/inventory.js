document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("table tbody");
    const searchInput = document.getElementById("inventory-search");
    const categoryFilter = document.querySelector(".tools-dropdown");

    // Modals
    const inventoryModal = document.getElementById("inventory-modal");
    const deleteModal = document.getElementById("delete-modal");
    const modalTitle = document.getElementById("modal-title");
    const closeBtns = document.querySelectorAll(".close-btn");

    // Form inputs
    const form = document.getElementById("inventory-form");
    const itemId = document.getElementById("item-id");
    const itemName = document.getElementById("item-name");
    const itemQty = document.getElementById("item-qty");
    const itemCategory = document.getElementById("item-category");
    const itemStatus = document.getElementById("item-status");

    // Edit search
    const editSearchGroup = document.getElementById("edit-search-group");
    const editSearch = document.getElementById("edit-search");
    const searchResults = document.getElementById("search-results");

    let editRow = null;

    // 🔍 Search (outside table)
    searchInput.addEventListener("keyup", () => {
        const filter = searchInput.value.toLowerCase();
        Array.from(tableBody.rows).forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });

    // 📂 Category Filter
    categoryFilter.addEventListener("change", () => {
        const value = categoryFilter.value;
        Array.from(tableBody.rows).forEach(row => {
            if (value === "all") {
                row.style.display = "";
            } else {
                const category = row.cells[3].innerText.toLowerCase();
                row.style.display = category.includes(value) ? "" : "none";
            }
        });
    });

    // ➕ Add Item
    document.getElementById("add-item-btn").addEventListener("click", () => {
        modalTitle.textContent = "Add Item";
        form.reset();
        itemId.disabled = false;
        editRow = null;
        editSearchGroup.style.display = "none"; // hide search field
        inventoryModal.classList.remove("hidden");
    });

    // ✏️ Edit Item
    document.getElementById("edit-item-btn").addEventListener("click", () => {
        modalTitle.textContent = "Edit Item";
        form.reset();
        itemId.disabled = true; // prevent changing ID
        editRow = null;

        editSearchGroup.style.display = "block"; // show search field
        editSearch.value = "";
        searchResults.style.display = "none";

        inventoryModal.classList.remove("hidden");
    });

    // 🔎 Live Search inside Edit Modal
    editSearch.addEventListener("input", () => {
        const term = editSearch.value.toLowerCase();
        searchResults.innerHTML = "";
        if (term.length < 1) {
            searchResults.style.display = "none";
            return;
        }

        let matches = Array.from(tableBody.rows).filter(row => {
            const id = row.cells[0].innerText.toLowerCase();
            const name = row.cells[1].innerText.toLowerCase();
            return id.includes(term) || name.includes(term);
        });

        if (matches.length) {
            matches.forEach(row => {
                const option = document.createElement("div");
                option.textContent = `${row.cells[0].innerText} - ${row.cells[1].innerText}`;
                option.addEventListener("click", () => {
                    // Autofill form
                    itemId.value = row.cells[0].innerText;
                    itemName.value = row.cells[1].innerText;
                    itemQty.value = row.cells[2].innerText;
                    itemCategory.value = row.cells[3].innerText.toLowerCase();
                    itemStatus.value = row.cells[4].innerText.toLowerCase();
                    editRow = row;

                    searchResults.style.display = "none";
                });
                searchResults.appendChild(option);
            });
            searchResults.style.display = "block";
        } else {
            searchResults.style.display = "none";
        }
    });

    // 🗑️ Delete Item
    document.getElementById("delete-item-btn").addEventListener("click", () => {
        document.getElementById("delete-item-id").value = "";
        deleteModal.classList.remove("hidden");
    });

    document.getElementById("confirm-delete").addEventListener("click", () => {
        const id = document.getElementById("delete-item-id").value;
        const row = Array.from(tableBody.rows).find(r => r.cells[0].innerText === id);

        if (row) {
            row.remove();
            deleteModal.classList.add("hidden");
        } else {
            alert("Item ID not found!");
        }
    });

    // Close modals
    closeBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            inventoryModal.classList.add("hidden");
            deleteModal.classList.add("hidden");
        });
    });

    window.addEventListener("click", e => {
        if (e.target === inventoryModal) inventoryModal.classList.add("hidden");
        if (e.target === deleteModal) deleteModal.classList.add("hidden");
    });

    // Save form
    form.addEventListener("submit", e => {
        e.preventDefault();

        if (editRow) {
            // update existing row
            editRow.cells[1].innerText = itemName.value;
            editRow.cells[2].innerText = itemQty.value;
            editRow.cells[3].innerHTML = `<span class="category-${itemCategory.value}">${capitalize(itemCategory.value)}</span>`;
            editRow.cells[4].innerHTML = `<span class="status-${itemStatus.value}">${capitalize(itemStatus.value)}</span>`;
        } else {
            // add new row
            const row = tableBody.insertRow();
            row.innerHTML = `
                <td>${itemId.value}</td>
                <td>${itemName.value}</td>
                <td>${itemQty.value}</td>
                <td><span class="category-${itemCategory.value}">${capitalize(itemCategory.value)}</span></td>
                <td><span class="status-${itemStatus.value}">${capitalize(itemStatus.value)}</span></td>
            `;
        }

        inventoryModal.classList.add("hidden");
    });

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
});

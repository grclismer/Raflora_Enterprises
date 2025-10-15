<<<<<<< HEAD
// SIMPLE INVENTORY MANAGEMENT - WORKING VERSION
console.log("📦 Inventory JS loaded");

// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function() {
    console.log("✅ DOM ready - initializing inventory system");
    initializeInventory();
});

function initializeInventory() {
    console.log("🔄 Initializing inventory system...");
    
    // Get all elements
    const addBtn = document.getElementById('add-item-btn');
    const editBtn = document.getElementById('edit-item-btn');
    const deleteBtn = document.getElementById('delete-item-btn');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    const inventoryModal = document.getElementById('inventory-modal');
    const deleteModal = document.getElementById('delete-modal');
    const form = document.getElementById('inventory-form');
    const modalTitle = document.getElementById('modal-title');
    const searchInput = document.getElementById('inventory-search');
    const categoryFilter = document.getElementById('category-filter');
    
    console.log("🔍 Elements found:", {
        addBtn: !!addBtn,
        editBtn: !!editBtn,
        deleteBtn: !!deleteBtn,
        inventoryModal: !!inventoryModal,
        deleteModal: !!deleteModal,
        form: !!form,
        searchInput: !!searchInput,
        categoryFilter: !!categoryFilter
    });

    // Check if essential elements exist
    if (!addBtn || !editBtn || !deleteBtn || !inventoryModal) {
        console.error("❌ Essential elements missing!");
        alert("Some page elements failed to load. Please refresh the page.");
        return;
    }

    let currentAction = 'add';

    // ➕ ADD ITEM BUTTON - FIXED MODAL
    addBtn.addEventListener('click', function() {
        console.log("➕ Add button clicked");
        currentAction = 'add';
        modalTitle.textContent = "Add Item";
        form.reset();
        document.getElementById('item-id').readOnly = false;
        document.getElementById('edit-search-group').style.display = 'none';
        showModal(inventoryModal);
    });

    // ✏️ EDIT ITEM BUTTON - FIXED MODAL
    editBtn.addEventListener('click', function() {
        console.log("✏️ Edit button clicked");
        currentAction = 'edit';
        modalTitle.textContent = "Edit Item";
        form.reset();
        document.getElementById('item-id').readOnly = true;
        document.getElementById('edit-search-group').style.display = 'block';
        document.getElementById('edit-search').value = '';
        document.getElementById('search-results').style.display = 'none';
        showModal(inventoryModal);
    });

    // 🗑️ DELETE ITEM BUTTON - FIXED MODAL
    deleteBtn.addEventListener('click', function() {
        console.log("🗑️ Delete button clicked");
        document.getElementById('delete-item-id').value = '';
        showModal(deleteModal);
    });

            // ✅ CONFIRM DELETE BUTTON - IMPROVED VERSION
    confirmDeleteBtn.addEventListener('click', function() {
        const itemId = document.getElementById('delete-item-id').value.trim();
        console.log("✅ Confirm delete for:", itemId);
        
        if (!itemId) {
            alert('Please enter or select an Item ID');
            return;
        }
        
        // Try to get the item name for better confirmation
        fetch(`../api/inventory/search.php?q=${encodeURIComponent(itemId)}`)
            .then(response => response.json())
            .then(data => {
                let itemName = 'this item';
                if (data.success && data.data.length > 0) {
                    const item = data.data.find(i => i.item_id === itemId);
                    if (item) {
                        itemName = `"${item.item_name}" (ID: ${item.item_id})`;
                    }
                }
                
                if (confirm(`Set ${itemName} to zero stock?\n\nThis will set quantity to 0 and mark as unavailable.`)) {
                    setItemToZero(itemId);
                }
            })
            .catch(error => {
                // If search fails, just use the ID
                if (confirm(`Set item ${itemId} to zero stock?\n\nThis will set quantity to 0 and mark as unavailable.`)) {
                    setItemToZero(itemId);
                }
            });
    });

        // 📝 FORM SUBMISSION - DEBUG VERSION
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log("📝 Form submitted, action:", currentAction);
        
        // Get form data directly
        const itemId = document.getElementById('item-id').value.trim();
        const itemName = document.getElementById('item-name').value.trim();
        const quantity = parseInt(document.getElementById('item-qty').value) || 0;
        const category = document.getElementById('item-category').value;
        const status = document.getElementById('item-status').value;

        const formData = {
            item_id: itemId,
            item_name: itemName,
            quantity: quantity,
            category: category,
            status: status
        };

        console.log("📦 Form data to send:", formData);
        console.log("🔍 Checking form data types:");
        console.log("  item_id:", typeof formData.item_id, "value:", formData.item_id);
        console.log("  item_name:", typeof formData.item_name, "value:", formData.item_name);
        console.log("  quantity:", typeof formData.quantity, "value:", formData.quantity);
        console.log("  category:", typeof formData.category, "value:", formData.category);
        console.log("  status:", typeof formData.status, "value:", formData.status);

        // Validate
        if (!formData.item_id || !formData.item_name) {
            alert('Item ID and Name are required');
            return;
        }

        // Determine endpoint
        const endpoint = currentAction === 'add' 
            ? '../api/inventory/add_item.php'
            : '../api/inventory/update_item.php';

        console.log("🚀 Sending to:", endpoint);

        // Send to API with detailed debugging
        console.log("🔄 Making fetch request...");
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            console.log("📨 Response received");
            console.log("  Status:", response.status);
            console.log("  OK:", response.ok);
            console.log("  Headers:", response.headers);
            return response.text();
        })
        .then(rawText => {
            console.log("📨 Raw API response:", rawText);
            
            // Check if response looks like HTML (PHP error)
            if (rawText.includes('<br />') || rawText.includes('<b>') || rawText.includes('<?php')) {
                console.error("❌ PHP ERROR DETECTED - Response contains HTML:");
                console.error(rawText);
                alert("❌ Server PHP error occurred. Check browser console for details.");
                return;
            }
            
            // Check if response is empty
            if (!rawText || rawText.trim() === '') {
                console.error("❌ EMPTY RESPONSE - Server returned nothing");
                alert("❌ Server returned empty response. Check API file.");
                return;
            }
            
            try {
                const data = JSON.parse(rawText);
                console.log("📨 Parsed JSON response:", data);
                
                if (data.success) {
                    alert("✅ " + data.message);
                    hideModal(inventoryModal);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    console.error("❌ API returned error:", data.message);
                    alert("❌ Error: " + data.message);
                }
            } catch (e) {
                console.error("❌ JSON Parse Error:", e);
                console.error("❌ Raw response that failed to parse:", rawText);
                alert("❌ Server returned invalid JSON. Check console for details.");
            }
        })
        .catch(error => {
            console.error("❌ Fetch error:", error);
            console.error("❌ Error details:", error.message, error.stack);
            alert("❌ Network error: " + error.message);
        });
    });

    // 🔍 EDIT SEARCH FUNCTIONALITY
    document.getElementById('edit-search').addEventListener('input', function() {
        const searchTerm = this.value.trim();
        const searchResults = document.getElementById('search-results');
        
        if (searchTerm.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        console.log("🔍 Searching for:", searchTerm);
        
        fetch(`../api/inventory/search.php?q=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    searchResults.innerHTML = '';
                    data.data.forEach(item => {
                        const div = document.createElement('div');
                        div.textContent = `${item.item_id} - ${item.item_name} (Qty: ${item.quantity})`;
                        div.addEventListener('click', function() {
                            // Fill form with selected item
                            document.getElementById('item-id').value = item.item_id;
                            document.getElementById('item-name').value = item.item_name;
                            document.getElementById('item-qty').value = item.quantity;
                            document.getElementById('item-category').value = item.category;
                            document.getElementById('item-status').value = item.status;
                            document.getElementById('edit-search').value = `${item.item_id} - ${item.item_name}`;
                            searchResults.style.display = 'none';
                        });
                        searchResults.appendChild(div);
                    });
                    searchResults.style.display = 'block';
                } else {
                    searchResults.innerHTML = '<div>No items found</div>';
                    searchResults.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('🔍 Search error:', error);
            });
    });

            // 🔍 DELETE MODAL SEARCH - IMPROVED VERSION
    document.getElementById('delete-item-id').addEventListener('input', function() {
        const searchTerm = this.value.trim();
        const searchResults = document.getElementById('delete-search-results');
        
        if (searchTerm.length < 1) {
            if (searchResults) searchResults.style.display = 'none';
            return;
        }

        console.log("🔍 Delete search for:", searchTerm);
        
        fetch(`../api/inventory/search.php?q=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0 && searchResults) {
                    searchResults.innerHTML = '';
                    data.data.forEach(item => {
                        const div = document.createElement('div');
                        div.innerHTML = `
                            <strong>${item.item_id}</strong> - ${item.item_name} 
                            <br><small>Qty: ${item.quantity} | ${item.category} | ${item.status}</small>
                        `;
                        div.style.padding = '10px';
                        div.style.borderBottom = '1px solid #eee';
                        div.style.cursor = 'pointer';
                        div.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = '#f0f0f0';
                        });
                        div.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = '';
                        });
                        div.addEventListener('click', function() {
                            // Fill the input with just the ID, but show both in display
                            document.getElementById('delete-item-id').value = item.item_id;
                            // Update the placeholder to show what was selected
                            document.getElementById('delete-item-id').placeholder = `Selected: ${item.item_id} - ${item.item_name}`;
                            searchResults.style.display = 'none';
                        });
                        searchResults.appendChild(div);
                    });
                    searchResults.style.display = 'block';
                } else if (searchResults) {
                    searchResults.innerHTML = '<div style="padding: 10px;">No items found</div>';
                    searchResults.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('🔍 Delete search error:', error);
                if (searchResults) {
                    searchResults.innerHTML = '<div style="padding: 10px; color: red;">Search error</div>';
                    searchResults.style.display = 'block';
                }
            });
    });

    // 🔍 TABLE SEARCH FUNCTIONALITY
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#inventory-table tbody tr');
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    }

    // 📂 CATEGORY FILTER FUNCTIONALITY
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const value = this.value;
            const rows = document.querySelectorAll('#inventory-table tbody tr');
            rows.forEach(row => {
                if (value === 'all') {
                    row.style.display = '';
                } else {
                    const category = row.cells[3].textContent.toLowerCase();
                    row.style.display = category.includes(value) ? '' : 'none';
                }
            });
        });
    }

    // ❌ CLOSE MODALS
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            hideModal(inventoryModal);
            hideModal(deleteModal);
        });
    });

    // 👆 CLICK OUTSIDE TO CLOSE
    window.addEventListener('click', function(e) {
        if (e.target === inventoryModal) hideModal(inventoryModal);
        if (e.target === deleteModal) hideModal(deleteModal);
    });

    console.log("🎉 Inventory system initialized successfully!");
}

// 🗑️ SET ITEM TO ZERO
function setItemToZero(itemId) {
    console.log("🗑️ Setting to zero:", itemId);
    
    fetch('../api/inventory/set_zero.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ item_id: itemId })
    })
    .then(response => response.text())
    .then(rawText => {
        try {
            const data = JSON.parse(rawText);
            if (data.success) {
                alert("✅ " + data.message);
                hideModal(document.getElementById('delete-modal'));
                setTimeout(() => location.reload(), 1000);
            } else {
                alert("❌ " + data.message);
            }
        } catch (e) {
            console.error("❌ JSON parse error:", e);
            alert("❌ Server error - check console");
        }
    })
    .catch(error => {
        console.error("❌ Fetch error:", error);
        alert("❌ Network error: " + error.message);
    });
}

// 🪟 MODAL FUNCTIONS - FIXED!
function showModal(modal) {
    if (modal) {
        // Try both methods to ensure modal shows
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        console.log("🪟 Modal shown:", modal.id);
    }
}

function hideModal(modal) {
    if (modal) {
        // Try both methods to ensure modal hides
        modal.classList.add('hidden');
        modal.style.display = 'none';
        console.log("🪟 Modal hidden:", modal.id);
    }
}

=======
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
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="../assets/css/admin/admin_dashboard.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="raflora logo">
        </div>
        <ul class="sidebar-menu">
            <li class="active">
                <span class="icon"><img src="../assets/images/icon/tools_equipment.png" alt="inventory"></span>
                <span><a href="../admin_dashboard/inventory.php">Tools and Equipment</a></span>
            </li>
            <li>
                <span class="icon"><img src="../assets/images/icon/client_updates.png" alt="client updates"></span>
                <a href="../admin_dashboard/update.php"><span>Client updates</span></a>
            </li>
            <li>
                <span class="icon"><img src="../assets/images/icon/invoice.png" alt="invoice"></span>
                <span><a href="../admin_dashboard/invoice.php">Invoice</a></span>
            </li>
            <li>
                <span class="icon"><img src="../assets/images/icon/perfo_analy.png" alt="performance analytics"></span>
                <span><a href="../admin_dashboard/analytics.php">Performance Analytics</a></span>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Inventory and Event Management</h1>
            <button class="logout-btn"><a href="../api/logout.php">Log-out</a></button>
        </div>

        <div class="dashboard-content">
            <div class="section-header">
                <h2>Inventory</h2>
                <select class="tools-dropdown">
                    <option value="all">All</option>
                    <option value="tools">Tools</option>
                    <option value="equipment">Equipment</option>
                    <option value="supplies">Supplies</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="tools-actions">
                <button class="action-btn" id="add-item-btn">+ Add Item</button>
                <button class="action-btn edit-btn" id="edit-item-btn">✏️ Edit Item</button>
                <button class="action-btn delete-btn" id="delete-item-btn">🗑️ Delete Item</button>
                <input type="text" id="inventory-search" placeholder="🔍 Search items...">
            </div>

            <!-- Inventory Table -->
            <div class="tools-table">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Item ID</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>001</td><td>Wire Cutter</td><td>15</td><td><span class="category-tools">Tools</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>002</td><td>Hammer</td><td>10</td><td><span class="category-tools">Tools</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>003</td><td>Glue Gun</td><td>8</td><td><span class="category-tools">Tools</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>004</td><td>Clipper</td><td>12</td><td><span class="category-tools">Tools</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>005</td><td>Tucker (Staple Gun)</td><td>6</td><td><span class="category-tools">Tools</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>006</td><td>Scissors</td><td>20</td><td><span class="category-equipment">Equipment</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>007</td><td>Ladder</td><td>5</td><td><span class="category-equipment">Equipment</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>008</td><td>Floral Tape</td><td>50</td><td><span class="category-supplies">Supplies</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>009</td><td>Leafshine Spray</td><td>25</td><td><span class="category-supplies">Supplies</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>010</td><td>Floral Spray</td><td>30</td><td><span class="category-supplies">Supplies</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>011</td><td>Ribbon</td><td>40</td><td><span class="category-supplies">Supplies</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>012</td><td>Chicken Wire</td><td>18</td><td><span class="category-supplies">Supplies</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>013</td><td>Cable Wire</td><td>22</td><td><span class="category-supplies">Supplies</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>014</td><td>Cable Tie</td><td>35</td><td><span class="category-supplies">Supplies</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>015</td><td>Floral Paper</td><td>28</td><td><span class="category-supplies">Supplies</span></td><td><span class="status-available">Available</span></td></tr>
                            <tr><td>016</td><td>Floral Foam</td><td>32</td><td><span class="category-supplies">Supplies</span></td><td><span class="status-available">Available</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="inventory-modal" class="modal hidden">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3 id="modal-title">Add Item</h3>
            <form id="inventory-form">
                <!-- Search bar only for editing -->
                <div class="form-group" id="edit-search-group" style="display:none;">
                    <label for="edit-search">Search Item (ID or Name)</label>
                    <input type="text" id="edit-search" placeholder="Type to search...">
                    <div id="search-results" class="search-results"></div>
                </div>
                <div class="form-group">
                    <label for="item-id">Item ID</label>
                    <input type="text" id="item-id" required>
                </div>
                <div class="form-group">
                    <label for="item-name">Item Name</label>
                    <input type="text" id="item-name" required>
                </div>
                <div class="form-group">
                    <label for="item-qty">Quantity</label>
                    <input type="number" id="item-qty" min="0" required>
                </div>
                <div class="form-group">
                    <label for="item-category">Category</label>
                    <select id="item-category" class="item-category" required>
                        <option value="tools">Tools</option>
                        <option value="equipment">Equipment</option>
                        <option value="supplies">Supplies</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="item-status">Status</label>
                    <select id="item-status" class="item-status" required>
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
                <button type="submit" class="action-btn save-btn">Save</button>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="delete-modal" class="modal hidden">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Delete Item</h3>
            <p>Enter the Item ID to delete:</p>
            <input type="text" id="delete-item-id" placeholder="Item ID">
            <button class="action-btn delete-btn" id="confirm-delete">Delete</button>
        </div>
    </div>

    <!-- JS -->
    <script src="../assets/js/admin/inventory.js" defer></script>
</body>
</html>

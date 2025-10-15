<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="../assets/css/admin/admin_dashboard.css">
<<<<<<< HEAD
    <link rel="stylesheet" href="../assets/css/admin/inventory.css">
    

=======
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="raflora logo">
        </div>
        <ul class="sidebar-menu">
            <li class="active">
                <a href="../admin_dashboard/inventory.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/tools_equipment.png" alt="inventory"></span>
                    <span class="text">Tools and Equipment</span>
                </a>
            </li>
            <li>
                <a href="../admin_dashboard/update.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/client_updates.png" alt="client updates"></span>
                    <span class="text">Client updates</span>
                </a>
            </li>
            <li>
                <a href="../admin_dashboard/invoice.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/invoice.png" alt="invoice"></span>
                    <span class="text">Invoice</span>
                </a>
            </li>
            <li>
                <a href="../admin_dashboard/analytics.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/perfo_analy.png" alt="performance analytics"></span>
                    <span class="text">Performance Analytics</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
<<<<<<< HEAD
            <h1>Inventory and Event Management</h1>
=======
            <h1>Tools and Equipment</h1>
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08
            <button class="logout-btn"><a href="../api/logout.php">Log-out</a></button>
        </div>

        <div class="dashboard-content">
<<<<<<< HEAD
            <!-- Low Stock Alerts -->
            <div class="inventory-alerts" id="inventory-alerts" style="display: none;">
                <div class="alert-low-stock" style="background: #fff3cd; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <span>⚠️ <span id="low-stock-count">0</span> items running low</span>
                </div>
            </div>

            <div class="section-header">
                <h2>Inventory</h2>
                <select class="tools-dropdown" id="category-filter">
=======
            <div class="section-header">
                <h2>Inventory</h2>
                <select class="tools-dropdown">
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08
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
<<<<<<< HEAD
                <button class="action-btn delete-btn" id="delete-item-btn">🗑️ Set to Zero Stock</button>
=======
                <button class="action-btn delete-btn" id="delete-item-btn">🗑️ Delete Item</button>
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08
                <input type="text" id="inventory-search" placeholder="🔍 Search items...">
            </div>

            <!-- Inventory Table -->
            <div class="tools-table">
                <div class="table-container">
<<<<<<< HEAD
                    <table id="inventory-table">
=======
                    <table>
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08
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
<<<<<<< HEAD
                            <?php
                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "raflora_enterprises";

                            $conn = new mysqli($servername, $username, $password, $dbname);

                            if ($conn->connect_error) {
                                die('<tr><td colspan="5" style="text-align: center; padding: 20px; color: red;">Connection failed: ' . $conn->connect_error . '</td></tr>');
                            }
                            
                            $sql = "SELECT * FROM inventory_tbl ORDER BY item_id";
                            $result = $conn->query($sql);
                            
                            $lowStockCount = 0;
                            
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $categoryClass = 'category-' . $row['category'];
                                    $statusClass = 'status-' . $row['status'];
                                    $isLowStock = $row['quantity'] <= 5;
                                    
                                    if ($isLowStock && $row['status'] == 'available') {
                                        $lowStockCount++;
                                    }
                                    
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['item_id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['item_name']) . '</td>';
                                    echo '<td class="' . ($isLowStock ? 'low-stock' : '') . '">' . htmlspecialchars($row['quantity']) . '</td>';
                                    echo '<td><span class="' . $categoryClass . '">' . ucfirst($row['category']) . '</span></td>';
                                    echo '<td><span class="' . $statusClass . '">' . ucfirst($row['status']) . '</span></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5" style="text-align: center; padding: 20px;">No inventory items found.</td></tr>';
                            }
                            
                            if ($lowStockCount > 0) {
                                echo '<script>document.getElementById("inventory-alerts").style.display = "block"; document.getElementById("low-stock-count").textContent = "' . $lowStockCount . '";</script>';
                            }
                            
                            $conn->close();
                            ?>
=======
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
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08
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
<<<<<<< HEAD
=======
                <!-- Search bar only for editing -->
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08
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
<<<<<<< HEAD
                    <select id="item-category" required>
=======
                    <select id="item-category" class="item-category" required>
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08
                        <option value="tools">Tools</option>
                        <option value="equipment">Equipment</option>
                        <option value="supplies">Supplies</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="item-status">Status</label>
<<<<<<< HEAD
                    <select id="item-status" required>
=======
                    <select id="item-status" class="item-status" required>
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08
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
<<<<<<< HEAD
            <h3>Set Item to Zero Stock</h3>
            <p>Search by Item ID or Name:</p>
            <input type="text" id="delete-item-id" placeholder="Type to search items...">
            <div id="delete-search-results" class="search-results" style="display:none;"></div>
            <div style="margin-top: 10px; font-size: 12px; color: #666;">
                💡 Tip: You can search by ID (01, 02) or name (Wire, Hammer)
            </div>
            <button class="action-btn delete-btn" id="confirm-delete" style="margin-top: 15px;">
                🗑️ Set to Zero Stock
            </button>
        </div>
    </div>


    <!-- JavaScript -->
    <script src="../assets/js/admin/inventory.js"></script>
</body>
</html>
=======
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
>>>>>>> 1bc6967ee12901cb1317b6fd2339b702c67e1c08

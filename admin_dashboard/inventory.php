<?php
session_start();

// Check if the user is logged in AND is an admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['role'] !== 'admin_type') {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tools and Equipment</title>
    <link rel="stylesheet" href="..//assets/css/admin/inventory.css">
    <link rel="stylesheet" href="/css/inventory.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="raflora logo">
        </div>
        <ul class="sidebar-menu">
            <li class="active">
                <span class="icon"><img src="../assets/images/icon/tools_equipment.png" alt="inventory"></span>
                <span><a href="../admin_dashboard/inventory.php">Tools and Equipment</span></a>
            </li>
            <li>
                <span class="icon"><img src="../assets/images/icon/client_updates.png" alt="client updates"></span>
                <a href="../admin_dashboard/update.html"><span>Client updates</span></a>
            </li>
            <li>
                <span class="icon"><img src="../assets/images/icon/invoice.png" alt="invoice"></span>
                <span><a href="../admin_dashboard/invoice.html">Invoice</span></a>
            </li>
            <li>
                <span class="icon"><img src="../assets/images/icon/perfo_analy.png" alt="perforamance analytics"></span>
                <span><a href="../admin_dashboard/analytics.html">Performance Analytics</span></a>
                
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Inventory and Event Management</h1>
           <button class="logout-btn"><a href="../api/logout.php">Log-out</button></a>
        </div>

        <div class="dashboard-content">
            <div class="tools-header">
                <h2>Tools and Equipment</h2>
                <select class="tools-dropdown">
                    <option>Tools</option>
                    <option>Equipment</option>
                    <option>All Items</option>
                </select>
            </div>

            <div class="tools-actions">
                <button class="action-btn">Add tools or Equipment</button>
                <button class="action-btn edit-btn">Edit</button>
            </div>

            <div class="tools-table">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Item_ID</th>
                                <th>Item name</th>
                                <th>Quantity</th>
                                <th>Availability</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>001</td>
                                <td>Cable Wire</td>
                                <td>24</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-tools">Tools</span></td>
                            </tr>
                            <tr>
                                <td>002</td>
                                <td>Wire Cutter</td>
                                <td>6</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-tools">Tools</span></td>
                            </tr>
                            <tr>
                                <td>003</td>
                                <td>Floral Tape</td>
                                <td>23</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-equipment">Equipment</span></td>
                            </tr>
                            <tr>
                                <td>004</td>
                                <td>Leafshine</td>
                                <td>15</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-equipment">Equipment</span></td>
                            </tr>
                            <tr>
                                <td>005</td>
                                <td>Floral Spray</td>
                                <td>20</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-equipment">Equipment</span></td>
                            </tr>
                            <tr>
                                <td>006</td>
                                <td>Hammer</td>
                                <td>6</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-tools">Tools</span></td>
                            </tr>
                            <tr>
                                <td>007</td>
                                <td>Glue gun</td>
                                <td>4</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-tools">Tools</span></td>
                            </tr>
                            <tr>
                                <td>008</td>
                                <td>Tucker</td>
                                <td>5</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-tools">Tools</span></td>
                            </tr>
                            <tr>
                                <td>009</td>
                                <td>Cable tie</td>
                                <td>3</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-equipment">Equipment</span></td>
                            </tr>
                            <tr>
                                <td>010</td>
                                <td>Clipper</td>
                                <td>6</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-tools">Tools</span></td>
                            </tr>
                            <tr>
                                <td>011</td>
                                <td>Floral Paper</td>
                                <td>6</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-equipment">Equipment</span></td>
                            </tr>
                            <tr>
                                <td>012</td>
                                <td>Floral Foam</td>
                                <td>3</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-equipment">Equipment</span></td>
                            </tr>
                            <tr>
                                <td>013</td>
                                <td>Sissor</td>
                                <td>7</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-tools">Tools</span></td>
                            </tr>
                            <tr>
                                <td>014</td>
                                <td>Glue Stick</td>
                                <td>6</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-equipment">Equipment</span></td>
                            </tr>
                            <tr>
                                <td>015</td>
                                <td>Ribbon</td>
                                <td>8</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-equipment">Equipment</span></td>
                            </tr>
                            <tr>
                                <td>016</td>
                                <td>Chicken Wire</td>
                                <td>20</td>
                                <td><span class="status-available">Available</span></td>
                                <td><span class="category-equipment">Equipment</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
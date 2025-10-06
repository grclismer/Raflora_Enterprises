<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <!-- <link rel="stylesheet" href="../assets/css/admin/invoice.css"> -->
     <link rel="stylesheet" href="../assets/css/admin/admin_dashboard.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="raflora logo">
        </div>
        <ul class="sidebar-menu">
            <li>
                <span class="icon"><img src="../assets/images/icon/tools_equipment.png" alt="inventory"></span>
                <span><a href="../admin_dashboard/inventory.php">Tools and Equipment</span></a>
            </li>
            <li>
                <span class="icon"><img src="../assets/images/icon/client_updates.png" alt="client updates"></span>
                <a href="../admin_dashboard/update.php"><span>Client updates</span></a>
            </li>
            <li class="active">
                <span class="icon"><img src="../assets/images/icon/invoice.png" alt="invoice"></span>
                <span><a href="../admin_dashboard/invoice.php">Invoice</span></a>
            </li>
            <li>
                <span class="icon"><img src="../assets/images/icon/perfo_analy.png" alt="perforamance analytics"></span>
                <span><a href="../admin_dashboard/analytics.php">Performance Analytics</span></a>
                
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Inventory and Event Management</h1>
            <button class="logout-btn"><a href="../api/logout.php">Log-out</button></a>
        </div>

        <div class="dashboard-content">
            <div class="section-header">
                <h2>Invoice</h2>
                <select class="user-dropdown">
                    <option>John Doe</option>
                </select>
            </div>

            <div class="invoice-table">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Contact number</th>
                                <th>Event Theme</th>
                                <th>Date payment</th>
                                <th>Status</th>
                                <th>MOD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John Doe</td>
                                <td>2442 West st. sample,Makati City</td>
                                <td>johndoe@example.com</td>
                                <td>+69 9123456789</td>
                                <td>wedding</td>
                                <td>04-27-25</td>
                                <td><span class="status-half">Down Payment</span></td>
                                <td><span class="payment-online">Online Bank</span></td>
                            </tr>
                            <tr>
                                <td>Darwin</td>
                                <td>1228 West st. sample,Makati City</td>
                                <td>darwin@example.com</td>
                                <td>+69 9365215685</td>
                                <td>Hotel Venue</td>
                                <td>03-10-25</td>
                                <td><span class="status-half">Down Payment</span></td>
                                <td><span class="payment-online">Online Bank</span></td>
                            </tr>
                            <tr>
                                <td>Johnson</td>
                                <td>1239 Sam st. sample,Makati City</td>
                                <td>johnson@example.com</td>
                                <td>+69 9326152486</td>
                                <td>Christmas</td>
                                <td>12-24-24</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-ewallet">E-Wallet</span></td>
                            </tr>
                            <tr>
                                <td>Emerson</td>
                                <td>1831 Sam st. sample,Makati City</td>
                                <td>emerson@example.com</td>
                                <td>+69 9653156485</td>
                                <td>Reunion</td>
                                <td>02-27-24</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-online">Online Bank</span></td>
                            </tr>
                            <tr>
                                <td>Harrison</td>
                                <td>1632 Red st. sample,Makati City</td>
                                <td>harrison@example.com</td>
                                <td>+69 9624585672</td>
                                <td>Meetings</td>
                                <td>02-26-24</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-online">Online Bank</span></td>
                            </tr>
                            <tr>
                                <td>Lemerson</td>
                                <td>1132 Blue st. sample,Makati City</td>
                                <td>lemerson@example.com</td>
                                <td>+69 9653458246</td>
                                <td>Outdoor</td>
                                <td>02-23-24</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-ewallet">E-Wallet</span></td>
                            </tr>
                            <tr>
                                <td>Madison</td>
                                <td>4234 Green st. sample,Makati City</td>
                                <td>madison@example.com</td>
                                <td>+69 9642519875</td>
                                <td>Debut</td>
                                <td>02-21-24</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-online">Online Bank</span></td>
                            </tr>
                            <tr>
                                <td>Pearson</td>
                                <td>2264 Mink st. sample,Makati City</td>
                                <td>pearson@example.com</td>
                                <td>+69 9685234695</td>
                                <td>Conferences</td>
                                <td>02-15-24</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-online">Online Bank</span></td>
                            </tr>
                            <tr>
                                <td>Larson</td>
                                <td>3334 Wats st. sample,Makati City</td>
                                <td>larson@example.com</td>
                                <td>+69 9645823168</td>
                                <td>Church</td>
                                <td>01-06-24</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-online">Online Bank</span></td>
                            </tr>
                            <tr>
                                <td>Watson</td>
                                <td>4423 Volt st. sample,Makati City</td>
                                <td>watson@example.com</td>
                                <td>+69 9764582455</td>
                                <td>Hotel</td>
                                <td>01-27-23</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-ewallet">E-Wallet</span></td>
                            </tr>
                            <tr>
                                <td>Clarkson</td>
                                <td>2543 Hipon st. sample,Makati City</td>
                                <td>clarkson@example.com</td>
                                <td>+69 9641741852</td>
                                <td>Birthday Party</td>
                                <td>01-22-23</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-ewallet">E-Wallet</span></td>
                            </tr>
                            <tr>
                                <td>Edison</td>
                                <td>5234 Crab st. sample,Makati City</td>
                                <td>edison@example.com</td>
                                <td>+69 9638526582</td>
                                <td>Funeral</td>
                                <td>01-20-23</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-ewallet">E-Wallet</span></td>
                            </tr>
                            <tr>
                                <td>Jane Smith</td>
                                <td>5235 West st. sample,Makati City</td>
                                <td>janesmith@example.com</td>
                                <td>+69 6352419875</td>
                                <td>Birthday Party</td>
                                <td>04-27-25</td>
                                <td><span class="status-full">Full Payment</span></td>
                                <td><span class="payment-ewallet">E-Wallet</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/css/admin/admin_dashboard.js"></script>
</body>
</html>
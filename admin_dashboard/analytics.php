<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Analytics</title>
    <!-- <link rel="stylesheet" href="../assets/css/admin/analytics.css"> -->
     <link rel="stylesheet" href="../assets/css/admin/admin_dashboard.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="raflora logo">
        </div>
        <ul class="sidebar-menu">
            <li >
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
            <li class="active">
                <a href="../admin_dashboard/analytics.php" class="list-item-link">
                    <span class="icon"><img src="../assets/images/icon/perfo_analy.png" alt="performance analytics"></span>
                    <span class="text">Performance Analytics</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Performance Analytics</h1>
            <button class="logout-btn"><a href="../api/logout.php">Log-out</a></button>
        </div>

        <div class="dashboard-content">
            <div class="section-header">
                <h2>Performance Analytics</h2>
                <select class="user-dropdown">
                    <option>John Doe</option>
                </select>
            </div>

            <div class="content-grid">
                <div class="client-list">
                    <table class="client-table">
                        <tbody>
                            <tr><td>Lismer Palce</td></tr>
                            <tr><td>Jane Smith</td></tr>
                            <tr><td>Anderson</td></tr>
                            <tr><td>Darwin</td></tr>
                            <tr><td>Johnson</td></tr>
                            <tr><td>Emerson</td></tr>
                            <tr><td>Harrison</td></tr>
                            <tr><td>Lemerson</td></tr>
                            <tr><td>Madison</td></tr>
                            <tr><td>Pearson</td></tr>
                            <tr><td>Larson</td></tr>
                            <tr><td>Watson</td></tr>
                            <tr><td>Clarkson</td></tr>
                            <tr><td>Edison</td></tr>
                            <tr><td>Jackson</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="charts-section">
                    <div class="chart-container">
                        <div class="chart-title">Client's Usage Status</div>
                        <div class="chart-content">
                            <div class="bar-chart">
                                <div class="bar-group">
                                    <div class="bars">
                                        <div class="bar loyalty" style="height: 80px;"></div>
                                        <div class="bar normal" style="height: 60px;"></div>
                                        <div class="bar regular" style="height: 40px;"></div>
                                    </div>
                                    <div class="year-label">2022</div>
                                </div>
                                <div class="bar-group">
                                    <div class="bars">
                                        <div class="bar loyalty" style="height: 100px;"></div>
                                        <div class="bar normal" style="height: 80px;"></div>
                                        <div class="bar regular" style="height: 60px;"></div>
                                    </div>
                                    <div class="year-label">2023</div>
                                </div>
                                <div class="bar-group">
                                    <div class="bars">
                                        <div class="bar loyalty" style="height: 120px;"></div>
                                        <div class="bar normal" style="height: 70px;"></div>
                                        <div class="bar regular" style="height: 50px;"></div>
                                    </div>
                                    <div class="year-label">2024</div>
                                </div>
                                <div class="bar-group">
                                    <div class="bars">
                                        <div class="bar loyalty" style="height: 90px;"></div>
                                        <div class="bar normal" style="height: 50px;"></div>
                                        <div class="bar regular" style="height: 30px;"></div>
                                    </div>
                                    <div class="year-label">2025</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bottom-charts">
                <div class="chart-container">
                    <div class="chart-title">Client's Evaluation</div>
                    <div class="chart-placeholder">
                        Line Chart - Client Ratings Over Time
                    </div>
                </div>

                <div class="chart-container">
                    <div class="chart-title">Payment Methods</div>
                    <div class="pie-chart-container">
                        <div class="pie-chart"></div>
                        <div class="pie-label">
                            50% online banking and e-wallet
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
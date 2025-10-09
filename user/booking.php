<?php
// FILE: user/booking.php (CORRECTED VERSION: Modal Restored with New Fields)

// ----------------------------------------------------------------------------------------------------------------------
// A. PACKAGE DATA FETCHING (Using PDO to fetch from package_details_tbl)
// ----------------------------------------------------------------------------------------------------------------------

session_start();
// Check if the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../user/user_login.html");
    exit();
}
// Profile Picture Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

// Create connection for profile picture
$conn_profile = new mysqli($servername, $username, $password, $dbname);
$user_data = [];

if (!$conn_profile->connect_error && isset($_SESSION['user_id'])) {
    // IMPORTANT: Include profile_picture in the query
    $stmt = $conn_profile->prepare("SELECT user_name, profile_picture FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc() ?? [];
    $stmt->close();
}
$conn_profile->close();

// Temporary/Simulated DB config (Update this to your actual connection details)
$db_host = 'localhost'; 
$db_name = 'raflora_enterprises';
$db_user = 'root'; 
$db_pass = ''; 

$packages_data = [];
$packages_json = "{}";
$event_types_list = [];

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // CRITICAL: Fetching based on the NEW package_details_tbl columns
    $query = "SELECT event_type, package_name, fixed_price, inclusions FROM package_details_tbl WHERE is_active = TRUE ORDER BY event_type, package_name";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $event_type = trim(strtolower($row['event_type']));
        $package_name = trim($row['package_name']);
        
        // Group data by event type for the JS filter
        if (!isset($packages_data[$event_type])) {
            $packages_data[$event_type] = [];
        }
        
        $packages_data[$event_type][$package_name] = [ 
            'price' => '₱' . number_format($row['fixed_price'], 2), 
            'inclusions' => explode("\n", $row['inclusions']) 
        ];
        
        if (!in_array($event_type, $event_types_list)) {
            $event_types_list[] = $event_type;
        }
    }
    $packages_json = json_encode($packages_data);

} catch (PDOException $e) {
    error_log("DB Error (Package Fetch): " . $e->getMessage()); 
    $packages_json = "{}";
}


// ----------------------------------------------------------------------------------------------------------------------
// B. MODAL LOGIC (Receiving redirect from client_booking.php) - RESTORED
// ----------------------------------------------------------------------------------------------------------------------
$showModal = false;
$orderId = '';
$paymentMethod = '';
$paymentType = '';
$formattedAmount = '';
$specificDetails = ''; // NEW: To capture specific details

// Check if all necessary GET parameters are present to show the modal
if (isset($_GET['order_id']) && isset($_GET['payment_method']) && isset($_GET['payment_type']) && isset($_GET['amount_due']) && isset($_GET['payment_details'])) {
    $showModal = true;
    $orderId = htmlspecialchars($_GET['order_id']);
    $paymentMethod = htmlspecialchars($_GET['payment_method']);
    $paymentType = htmlspecialchars($_GET['payment_type']);
    $amountDue = (float)htmlspecialchars($_GET['amount_due']);
    $specificDetails = htmlspecialchars($_GET['payment_details']); // Capture new detail
    $formattedAmount = '₱' . number_format($amountDue, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link rel="stylesheet" href="../assets/css/user/booking.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="../assets/css/user/navbar.css">
    <script src="../assets/js/user/navbar.js"></script>
    <SCRipt src="../assets/js/user/booking" defer></SCRipt>


    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</head>
<body>
    <div class="booking-container">
        <nav class="navbar">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="logo" class="logo" />
            <div class="hamburger-menu">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links">
                <li><a href="../user/landing.php" class="nav-link">Home</a></li>
                <li><a href="../user/gallery.php" class="nav-link">Gallery</a></li>
                <li><a href="../user/about.php" class="nav-link">About</a></li>
                <li class="active"><a href="../user/booking.php" class="nav-link">Book</a></li>
                <li class="user-dropdown-toggle">
                    <div class="navbar-profile">
                        <?php if (!empty($user_data['profile_picture'])): ?>
                            <!-- Profile picture with CSS class -->
                            <img src="/raflora_enterprises/<?php echo ltrim($user_data['profile_picture'], '/'); ?>" 
                                alt="Profile" 
                                class="profile-picture profile-picture-small">
                        <?php else: ?>
                            <!-- Default icon with CSS class -->
                            <div class="profile-default-icon">
                                <i class="fa fa-user"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Username (optional) -->
                        <span class="navbar-username"><?php echo $user_data['user_name'] ?? 'User'; ?></span>
                    </div>
                    <ul class="user-dropdown-menu">
                        <li><a href="../user/account_settings.php">Account settings</a></li>
                        <li><a href="../user/my_bookings.php">My Bookings</a></li>
                        <li><a href="../api/logout.php">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        
        <div class="main-container">
            <h1 class="page-title">Please fill-up the form</h1>
            <div class="form-container">
                <form action="../config/client_booking.php" method="post" enctype="multipart/form-data" id="bookingForm">
                    <div class="form-group-row">
                        <div class="form-field">
                            <label for="full_name">Full name</label>
                            <input type="text" id="full_name" name="full_name" placeholder="full name" required>
                        </div>
                        <div class="form-field">
                            <label for="mobile_number">Contact number</label>
                            <input type="tel" id="mobile_number" name="mobile_number" placeholder="+69 " maxlength="11" required>
                        </div>
                        <div class="form-field">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="email address" required>
                        </div>
                    </div>
                    <div class="form-group-address">
                        <div class="form-field">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" placeholder="address" required>
                        </div>
                    </div>
                    <div class="form-group-row">
                        <div class="form-field" id="upload-form-field">
                            <label for="design_document_upload">Upload Design and Color Scheme</label>
                            <div class="file-upload-box" id="fileUploadBox">
                                <label for="design_document_upload" id="fileUploadLabel">
                                    <i class="fi fi-br-cloud-upload"></i>
                                    <input type="file" id="design_document_upload" name="design_document_upload" accept="image/*, .pdf" class="file-input">
                                </label>
                            </div>
                            <input type="hidden" name="design_uploaded_check" id="designUploadedCheck" required> 
                        </div>
                        <div class="form-field">
                            <label for="event-schedule">Event Schedule</label>
                            <div class="date-time-group">
                                <div class="date-field">
                                    <!-- <img src="../assets/images//icon/calendar.png" alt="Calendar icon"> -->
                                    <input type="date" name="event_date" id="event-date" required>
                                </div>
                                <div class="time-field">
                                    <!-- <img src="../assets/images//icon/clock.png" alt="Clock icon"> -->
                                    <input type="time" name="event_time" id="event-time" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-field form-field-regards">
                            <label for="regards">Your Recommendations :</label>
                            <textarea id="regards" name="recommendations" placeholder="Type your message here..." required></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group-row">
                        <div class="form-field">
                            <label for="theme">Event</label>
                            <select id="theme" name="event_theme" required> 
                                <option value="">Select an event</option>
                                <?php
                                $display_map = [
                                    'wedding' => 'Wedding', 
                                    'birthday party' => 'Birthday Party',
                                    'reunion' => 'Reunion',
                                    'hotel / corporate' => 'Hotel / Corporate',
                                ];
                                
                                foreach (array_unique(array_keys($packages_data)) as $event_type) {
                                    $display_name = $display_map[$event_type] ?? ucwords($event_type);
                                    echo "<option value=\"{$event_type}\">{$display_name}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-field" id="packages-field">
                            <label for="packages">Packages</label>
                            <select id="packages" name="packages" required> 
                                <option value="">Select Packages</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="package-details-container mt-4 mb-4">
                        <h3 class="text-xl font-bold mb-2">Package Details:</h3>
                        <div id="selected-package-info" class="p-3 border rounded ">
                            <p class="text-gray-500 dark:text-gray-400">Please select a package to view the price and inclusions.</p>
                        </div>
                    </div>
                    
                    <div class="payment-selection">
                        <label for="payment-method">Payment Method</label>
                        <select id="payment-method" name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="Online Bank">Online Bank</option>
                            <option value="E-Wallet">E-Wallet</option>
                        </select>
                        
                        <div id="paymentDetailsGroup">
                            <label for="payment_details_form">Specific Bank / E-Wallet</label>
                            <select name="payment_details" id="payment_details_form" class="form-control" required style="display: none;">
                                <option value="">Select Payment Channel</option>
                                <optgroup label="Online Banks">
                                    <option value="BDO Bank">BDO Bank</option>
                                    <option value="BPI Bank">BPI Bank</option>
                                    <option value="Metrobank">Metrobank</option>
                                </optgroup>
                                <optgroup label="E-Wallets">
                                    <option value="GCash">GCash</option>
                                    <option value="PayMaya">PayMaya</option>
                                </optgroup>
                                <option value="Other">Other / Unlisted</option>
                            </select>
                            <input type="hidden" name="payment_details" id="cash_payment_details_hidden" value="Not Applicable">
                        </div>
                        <div class="payment-type">
                            <label>Payment Type</label>
                            <div>
                                <input type="radio" name="payment_type" value="Down Payment" required checked /> Down Payment (50%)
                                <input type="radio" name="payment_type" value="Full Payment"required> Full Payment (100%)
                            </div>
                        </div>
                    </div>
                    <div class="form-action">
                        <button type="submit" class="submit-button" name="place_order_btn">Place order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Payment Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../config/client_booking.php" method="POST" id="referenceForm">
                    <div class="modal-body">
                        <div class="alert alert-info" role="alert">
                            Please pay the required amount to get the **Reference code**.
                        </div>
                        <p>Order ID: <strong id="modal-order-id"><?php echo $orderId; ?></strong></p>
                        <p>Amount Due Now: <strong class="text-xl text-success"><?php echo $formattedAmount; ?></strong></p>
                        <p>You are paying via: <strong id="modal-payment-method"><?php echo $paymentMethod; ?></strong></p>
                        <p>Specific Channel: <strong id="modal-specific-details"><?php echo $specificDetails; ?></strong></p>
                        <p>Payment type: <strong id="modal-payment-type"><?php echo $paymentType; ?></strong></p>
                        
                        <input type="hidden" name="order_id_value" id="modal-order-id-input" value="<?php echo $orderId; ?>">
                        
                        <input type="hidden" name="payment_details" value="<?php echo $specificDetails; ?>"> 

                        <div class="mb-3">
                            <label for="referenceCode" class="form-label">Reference ID/Code</label>
                            <input type="text" class="form-control" name="reference_code" id="referenceCode" required placeholder="Enter your Payment Reference code"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit_reference_from_modal" class="btn btn-success" id="submitReferenceBtn">Submit Reference</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <footer class="footer" id="Page-footer">
    </footer>
    

    
    <script>
        // CRITICAL: PHP to JS Data Injection
        window.packageData = <?php echo $packages_json; ?>;
    
        $(document).ready(function() {
            const packageData = window.packageData || {};
            const themeDropdown = $('#theme');
            const packagesDropdown = $('#packages');
            const infoDiv = $('#selected-package-info');
            
            // --- Payment Details Toggle Logic ---
            const paymentMethodDropdown = $('#payment-method');
            const paymentDetailsSelect = $('#payment_details_form');
            const cashPaymentDetailsHidden = $('#cash_payment_details_hidden');

            function togglePaymentDetails() {
                const selectedMethod = paymentMethodDropdown.val();
                
                // Show the specific bank/e-wallet dropdown for Online Bank and E-Wallet
                if (selectedMethod === 'Online Bank' || selectedMethod === 'E-Wallet') {
                    paymentDetailsSelect.show().prop('required', true);
                    cashPaymentDetailsHidden.prop('disabled', true); // Disable cash hidden field
                } else {
                    // If no relevant payment method is selected, hide the dropdown
                    paymentDetailsSelect.hide().prop('required', false).val('');
                    cashPaymentDetailsHidden.prop('disabled', false); // Enable cash hidden field
                }
            }

            paymentMethodDropdown.on('change', togglePaymentDetails);
            togglePaymentDetails(); // Initial call
            // --- END NEW PAYMENT LOGIC ---

            // --- Logic for displaying price/inclusions (UNCHANGED) ---
            function updatePackageDetails(packageName) {
                const selectedEvent = themeDropdown.val().toLowerCase().trim();
                const trimmedPackageName = packageName ? packageName.trim() : '';
                if (!trimmedPackageName || !selectedEvent) {
                    infoDiv.html('<p class="text-gray-500 dark:text-gray-400">Please select a package to view the price and inclusions.</p>');
                    return;
                }
                
                const packageInfo = packageData[selectedEvent] ? packageData[selectedEvent][trimmedPackageName] : null;

                if (!packageInfo) {
                    infoDiv.html(`<p class="text-red-500 dark:text-red-400 font-bold">Error: Package details not found. Check if the package is linked to the selected event type in the database.</p>`);
                    return;
                }

                let htmlContent = `
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400 mb-2">Fixed Price: <strong>${packageInfo.price}</strong></p>
                    <h4 class="font-medium mt-3 mb-1">Inclusions:</h4>
                    <ul class="list-disc ml-5 space-y-1 text-sm">
                `;

                if (Array.isArray(packageInfo.inclusions)) {
                    packageInfo.inclusions.forEach(item => {
                        const trimmedItem = item.trim();
                        if (trimmedItem !== '') { 
                            htmlContent += `<li>${trimmedItem}</li>`;
                        }
                    });
                }

                htmlContent += '</ul>';
                infoDiv.html(htmlContent);
            }
            
            function filterPackages(selectedEvent) {
                packagesDropdown.empty().append('<option value="">Select Packages</option>'); 
                
                const eventKey = selectedEvent.toLowerCase().trim();
                const packagesForEvent = packageData[eventKey];

                if (packagesForEvent) {
                    $.each(packagesForEvent, function(name, details) {
                        packagesDropdown.append($('<option>', {
                            value: name,
                            text: name
                        }));
                    });
                }
                
                updatePackageDetails('');
            }

            themeDropdown.on('change', function() {
                const selectedEvent = $(this).val();
                filterPackages(selectedEvent);
            }).trigger('change'); 

            packagesDropdown.on('change', function() {
                const selectedPackage = $(this).val();
                updatePackageDetails(selectedPackage);
            });
            
            // --- Modal display for successful order placement (RESTORED) ---
            <?php if ($showModal): ?>
                // Clean the URL to prevent the modal from popping up on refresh
                if (window.history.replaceState) {
                    const url = window.location.href;
                    const cleanUrl = url.split("?")[0];
                    window.history.replaceState({path:cleanUrl}, '', cleanUrl);
                }
                
                $(document).ready(function() {
                    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
                    paymentModal.show();
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>
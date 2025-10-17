<?php
// FILE: user/booking.php (CLEANED VERSION)

// ----------------------------------------------------------------------------------------------------------------------
// A. PACKAGE DATA FETCHING
// ----------------------------------------------------------------------------------------------------------------------

session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../user/user_login.html");
    exit();
}

// Profile Picture Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn_profile = new mysqli($servername, $username, $password, $dbname);
$user_data = [];

if (!$conn_profile->connect_error && isset($_SESSION['user_id'])) {
    $stmt = $conn_profile->prepare("SELECT user_name, profile_picture FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc() ?? [];
    $stmt->close();
}
$conn_profile->close();

// Package Data Fetching
$db_host = 'localhost'; 
$db_name = 'raflora_enterprises';
$db_user = 'root'; 
$db_pass = ''; 

$packages_data = [];
$packages_json = "{}";

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT event_type, package_name, fixed_price, inclusions FROM package_details_tbl WHERE is_active = TRUE ORDER BY event_type, package_name";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $event_type = trim(strtolower($row['event_type']));
        $package_name = trim($row['package_name']);
        
        if (!isset($packages_data[$event_type])) {
            $packages_data[$event_type] = [];
        }
        
        $packages_data[$event_type][$package_name] = [ 
            'price' => '₱' . number_format($row['fixed_price'], 2), 
            'inclusions' => explode("\n", $row['inclusions']) 
        ];
    }
    $packages_json = json_encode($packages_data);

} catch (PDOException $e) {
    error_log("DB Error (Package Fetch): " . $e->getMessage()); 
    $packages_json = "{}";
}

// ----------------------------------------------------------------------------------------------------------------------
// B. MODAL LOGIC
// ----------------------------------------------------------------------------------------------------------------------
$showModal = false;
$orderId = '';
$paymentMethod = '';
$paymentType = '';
$formattedAmount = '';
$specificDetails = '';

if (isset($_GET['order_id']) && isset($_GET['payment_method']) && isset($_GET['payment_type']) && isset($_GET['amount_due']) && isset($_GET['payment_details'])) {
    $showModal = true;
    $orderId = htmlspecialchars($_GET['order_id']);
    $paymentMethod = htmlspecialchars($_GET['payment_method']);
    $paymentType = htmlspecialchars($_GET['payment_type']);
    $amountDue = (float)htmlspecialchars($_GET['amount_due']);
    $specificDetails = htmlspecialchars($_GET['payment_details']);
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
    <script src="../assets/js/user/navbar.js" defer></script>
    <script src="../assets/js/user/booking.js" defer></script>
    <script src="../assets/js/user/modal.js" defer></script>

    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.2.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
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
                            <img src="/raflora_enterprises/<?php echo ltrim($user_data['profile_picture'], '/'); ?>" 
                                alt="Profile" 
                                class="profile-picture profile-picture-small">
                        <?php else: ?>
                            <div class="profile-default-icon">
                                <i class="fa fa-user"></i>
                            </div>
                        <?php endif; ?>
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
                    <!-- Personal Information -->
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
                                    <input type="date" name="event_date" id="event-date" required>
                                </div>
                                <div class="time-field">
                                    <input type="time" name="event_time" id="event-time" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-field form-field-regards">
                            <label for="regards">Your Recommendations :</label>
                            <textarea id="regards" name="recommendations" placeholder="Type your message here..." required></textarea>
                        </div>
                    </div>
                    
                    <!-- Event & Package Selection -->
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
                    
                    <!-- Payment Selection (IN MAIN FORM) -->
                    <div class="form-group-row">
                        <div class="form-field">
                            <label for="payment_method">Payment Method</label>
                            <select id="payment_method" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="Online Bank">Online Bank</option>
                                <option value="E-Wallet">E-Wallet</option>
                            </select>
                        </div>
                        
                        <div class="form-field">
                            <label for="payment_type">Payment Type</label>
                            <select id="payment_type" name="payment_type" required>
                                <option value="">Select Payment Type</option>
                                <option value="Down Payment">Down Payment (50%)</option>
                                <option value="Full Payment">Full Payment (100%)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="package-details-container mt-4 mb-4">
                        <h3 class="text-xl font-bold mb-2">Package Details:</h3>
                        <div id="selected-package-info" class="p-3 border rounded ">
                            <p class="text-gray-500 dark:text-gray-400">Please select a package to view the price and inclusions.</p>
                        </div>
                    </div>
                   
                    <div class="form-action">
                        <button type="submit" class="submit-button" name="place_order_btn">Place order</button>
                    </div>
                    
                    <label style="display:block; margin-bottom:8px;">
                        <input type="checkbox" required> I read and agree to <a href="#" id="showPrivacyPolicy">Privacy Policy</a>
                    </label>
                    <label style="display:block; margin-bottom:12px;">
                        <input type="checkbox" required> I read and agree to <a href="#" id="showTermsCondition">Terms and Condition</a>
                    </label>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Payment Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    
                </div>
                <form action="../config/client_booking.php" method="POST" id="modalReferenceForm">
                    <div class="modal-body">
                        <div class="alert alert-info" role="alert">
                            Please complete your payment information below. <br>
                            <strong>Gcash No. 09773436195</strong><br>
                            <strong>Bank No.  001234567891</strong><br>
                        </div>
                        
                        <p><strong>Order ID:</strong> <span id="modal-order-id"><?php echo $orderId; ?></span></p>
                        <p><strong>Package Price:</strong> <span id="package-price" class="text-info"><?php echo $formattedAmount; ?></span></p>
                        <p><strong>Amount Due Now:</strong> <span id="amount-due-now" class="text-xl text-success"><?php echo $formattedAmount; ?></span></p>
                        
                        <input type="hidden" id="total-package-price" value="<?php echo $amountDue; ?>">

                        <!-- Payment Method Display (Read-only) -->
                        <div class="mb-3">
                            <label class="form-label">Selected Payment Method</label>
                            <div class="form-control" style="background-color: #f8f9fa;">
                                <strong><?php echo $paymentMethod; ?> - <?php echo $paymentType; ?></strong>
                            </div>
                        </div>

                        <!-- ✅ IMPROVED PAYMENT CHANNEL SECTION -->
                        <div class="mb-3" id="payment-channel-section">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label for="payment-details-select" class="form-label required-field">
                                        Payment Channel <span class="required-indicator">*</span>
                                    </label>
                                    <select name="payment_details" id="payment-details-select" class="form-control" required>
                                        <option value="">Select channel</option>
                                        <!-- Options will be populated by JavaScript based on payment method -->
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <!-- Custom Channel Input (Hidden by default) -->
                                    <label for="custom-payment-channel" class="form-label" style="color: #6c757d;">
                                        Specify Channel
                                    </label>
                                    <input type="text" 
                                           name="custom_payment_channel" 
                                           id="custom-payment-channel" 
                                           class="form-control" 
                                           placeholder="Enter other payment channel"
                                           style="display: none; color: #6c757d;"
                                           disabled>
                                </div>
                            </div>
                        </div>

                        <!-- Reference Code -->
                        <div class="mb-3">
                            <label for="referenceCode" class="form-label required-field">
                                Reference ID/Code <span class="required-indicator">*</span>
                            </label>
                            <input type="text" class="form-control" name="reference_code" id="referenceCode" required 
                                placeholder="Enter your 12-13 digit Reference code" 
                                minlength="12" maxlength="13">
                        </div>

                        <!-- Hidden fields to pass payment data -->
                        <input type="hidden" name="payment_method" value="<?php echo $paymentMethod; ?>">
                        <input type="hidden" name="payment_type" value="<?php echo $paymentType; ?>">
                        <input type="hidden" name="order_id_value" id="modal-order-id-input" value="<?php echo $orderId; ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit_reference_from_modal" class="btn btn-success">Submit Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer" id="Page-footer"></footer>
    
    <script>
        // PHP to JS Data Injection
        window.packageData = <?php echo $packages_json; ?>;
    
        $(document).ready(function() {
            const packageData = window.packageData || {};
            const themeDropdown = $('#theme');
            const packagesDropdown = $('#packages');
            const infoDiv = $('#selected-package-info');
            
            function updatePackageDetails(packageName) {
                const selectedEvent = themeDropdown.val().toLowerCase().trim();
                const trimmedPackageName = packageName ? packageName.trim() : '';
                
                if (!trimmedPackageName || !selectedEvent) {
                    infoDiv.html('<p class="text-gray-500 dark:text-gray-400">Please select a package to view the price and inclusions.</p>');
                    return;
                }
                
                const packageInfo = packageData[selectedEvent] ? packageData[selectedEvent][trimmedPackageName] : null;

                if (!packageInfo) {
                    infoDiv.html(`<p class="text-red-500 dark:text-red-400 font-bold">Error: Package details not found.</p>`);
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
            
            // Modal display for successful order placement
            <?php if ($showModal): ?>
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

    <!-- Payment Channel Dynamic Logic -->
    <script>
    // Payment channel handling for modal
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethod = "<?php echo $paymentMethod; ?>"; // Get PHP variable
        const paymentDetailsSelect = document.getElementById('payment-details-select');
        const customChannelInput = document.getElementById('custom-payment-channel');
        
        // Define payment channels
        const paymentChannels = {
            'Online Bank': ['BDO Bank', 'BPI Bank', 'Metrobank', 'UnionBank', 'Landbank', 'Security Bank', 'Other'],
            'E-Wallet': ['GCash', 'PayMaya', 'Other']
        };
        
        function initializePaymentChannels() {
            if (paymentDetailsSelect && paymentChannels[paymentMethod]) {
                // Clear existing options
                paymentDetailsSelect.innerHTML = '<option value="">Select channel</option>';
                
                // Populate with channels based on payment method
                paymentChannels[paymentMethod].forEach(channel => {
                    const option = document.createElement('option');
                    option.value = channel;
                    option.textContent = channel;
                    paymentDetailsSelect.appendChild(option);
                });
                
                console.log('Populated payment channels for:', paymentMethod);
            }
        }
        
        function handleChannelSelection() {
            if (paymentDetailsSelect && customChannelInput) {
                const selectedValue = paymentDetailsSelect.value;
                
                if (selectedValue === 'Other') {
                    // Show and enable custom input
                    customChannelInput.style.display = 'block';
                    customChannelInput.disabled = false;
                    customChannelInput.required = true;
                    customChannelInput.placeholder = 'Enter payment channel name';
                } else {
                    // Hide and disable custom input
                    customChannelInput.style.display = 'none';
                    customChannelInput.disabled = true;
                    customChannelInput.required = false;
                    customChannelInput.value = ''; // Clear value
                }
            }
        }
        
        // Initialize on page load
        initializePaymentChannels();
        
        // Add event listener for channel selection
        if (paymentDetailsSelect) {
            paymentDetailsSelect.addEventListener('change', handleChannelSelection);
        }
        
        // Initialize custom input state
        handleChannelSelection();
    });
    </script>
</body>
</html>
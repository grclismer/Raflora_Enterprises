<?php
// test_submission.php
echo "TEST FILE LOADED!<br>";
echo "POST data: ";
print_r($_POST);
echo "<br>GET data: ";
print_r($_GET);

if (isset($_POST['submit_reference_from_modal'])) {
    echo "<h1>REFERENCE SUBMISSION DETECTED!</h1>";
    echo "Order ID: " . ($_POST['order_id_value'] ?? 'NOT SET') . "<br>";
    echo "Reference Code: " . ($_POST['reference_code'] ?? 'NOT SET') . "<br>";
}
?>
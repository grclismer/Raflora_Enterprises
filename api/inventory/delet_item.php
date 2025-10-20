<?php
// Raflora_Enterprises/api/inventory/delete_item.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Also check for form data
if (!$input && $_POST) {
    $input = $_POST;
}

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$item_id = $input['item_id'] ?? '';

if (empty($item_id)) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    $conn->close();
    exit;
}

// First, get item info for confirmation message
$check_sql = "SELECT item_name FROM inventory_tbl WHERE item_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $item_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    $check_stmt->close();
    $conn->close();
    exit;
}

$item_data = $check_result->fetch_assoc();
$item_name = $item_data['item_name'];
$check_stmt->close();

// Delete the item
$sql = "DELETE FROM inventory_tbl WHERE item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $item_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true, 
            'message' => "Item '{$item_name}' (ID: {$item_id}) has been permanently deleted"
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found or already deleted']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
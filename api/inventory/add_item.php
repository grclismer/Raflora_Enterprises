<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$item_id = $input['item_id'] ?? '';
$item_name = $input['item_name'] ?? '';
$quantity = $input['quantity'] ?? 0;
$category = $input['category'] ?? '';
$status = $input['status'] ?? 'available';

// Validate required fields
if (empty($item_id) || empty($item_name)) {
    echo json_encode(['success' => false, 'message' => 'Item ID and Name are required']);
    $conn->close();
    exit;
}

// Check if item exists
$check_sql = "SELECT COUNT(*) as count FROM inventory_tbl WHERE item_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $item_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$row = $check_result->fetch_assoc();

if ($row['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Item ID already exists']);
    $conn->close();
    exit;
}

// Insert item
$sql = "INSERT INTO inventory_tbl (item_id, item_name, quantity, category, status) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssiss", $item_id, $item_name, $quantity, $category, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Item added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
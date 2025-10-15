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

if (empty($item_id)) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    $conn->close();
    exit;
}

// FIXED: The bind_param should have 5 parameters matching the 5 placeholders in SQL
$sql = "UPDATE inventory_tbl SET item_name=?, quantity=?, category=?, status=?, updated_at=NOW() WHERE item_id=?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    $conn->close();
    exit;
}

// FIXED: Changed "siss" to "siss" (4 parameters) to "sisss" (5 parameters)
// s=string, i=integer for each parameter:
// item_name (string), quantity (integer), category (string), status (string), item_id (string)
$stmt->bind_param("sisss", $item_name, $quantity, $category, $status, $item_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found or no changes made']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
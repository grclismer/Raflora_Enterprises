<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

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

$search_term = $_GET['q'] ?? '';

$sql = "SELECT item_id, item_name, quantity, category, status 
        FROM inventory_tbl 
        WHERE item_id LIKE ? OR item_name LIKE ? 
        ORDER BY item_id 
        LIMIT 10";
        
$stmt = $conn->prepare($sql);
$search_pattern = "%$search_term%";
$stmt->bind_param("ss", $search_pattern, $search_pattern);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}

echo json_encode(['success' => true, 'data' => $results]);

$stmt->close();
$conn->close();
?>
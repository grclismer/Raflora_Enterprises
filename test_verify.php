<?php
// Simple test for QR verification
$test_data = '{"user_id":5,"system":"raflora_enterprises","method":"qr_login"}';

$post_data = json_encode(['qr_data' => $test_data]);

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => $post_data,
    ],
];

$context  = stream_context_create($options);
$result = file_get_contents('http://localhost/Raflora_Enterprises/api/verify_qr_login.php', false, $context);

echo "Verification API Response:\n";
echo $result;
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../engine/init.php';
require_once '../../engine/function/users.php';

header('Content-Type: application/json');

$response = [];

try {
    if (isset($_GET['username'])) {
        $username = $_GET['username'];

        // Check if the username exists
        $exists = user_exist($username);

        // Prepare response
        $response['exists'] = $exists;
    } else {
        // If username parameter is not provided
        $response['error'] = 'Username not provided';
    }
} catch (Exception $e) {
    // Handle any exceptions and set error message
    $response['error'] = 'Server error: ' . $e->getMessage();
}

// Output the response as JSON
echo json_encode($response);
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../engine/init.php';
require_once '../../engine/function/users.php';

header('Content-Type: application/json');

$response = [];

try {
    if (isset($_GET['email'])) {
        $email = $_GET['email'];
        $response['email_received'] = $email; // Debugging line

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $exists = user_email_exist($email);
            $response['exists'] = $exists;
        } else {
            $response['error'] = 'Invalid email format';
        }
    } else {
        $response['error'] = 'Email not provided';
    }
} catch (Exception $e) {
    $response['error'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
?>
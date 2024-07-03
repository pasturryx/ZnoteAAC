<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'engine/init.php';
require_once 'engine/function/users.php';

header('Content-Type: application/json');

$response = [];

try {
    if (isset($_POST['current_password'])) {
        $currentPassword = $_POST['current_password'];
        $sessionUserId = $session_user_id;

        $passData = user_data($sessionUserId, 'password');

        // Assuming the TFS_10 uses SHA1 for password hashing without a salt
        $isCurrentPasswordCorrect = (sha1($currentPassword) === $passData['password']);

        if (!$isCurrentPasswordCorrect) {
            $response['error'] = 'Password incorrect.';
        } else {
            $response['success'] = 'Password is correct.';
        }
    } else {
        $response['error'] = 'Current password is required.';
    }
} catch (Exception $e) {
    $response['error'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
?>

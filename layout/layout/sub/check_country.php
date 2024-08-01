<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../engine/init.php';
require_once '../../engine/function/users.php';
require_once dirname(__FILE__) . '/../../config.countries.php';

// Get the user's public IP address
$user_ip = file_get_contents('https://api.ipify.org');

// Fetch the user's location data from ip-api.com
$ipapi_url = "http://ip-api.com/json/$user_ip";
// echo "API URL: $ipapi_url"; // Comment out or remove this line

$location_data = file_get_contents($ipapi_url);

if ($location_data === false) {
    $response = ['error' => 'Unable to fetch location data'];
} else {
    $location_data = json_decode($location_data, true);
    $country_code = $location_data['countryCode'] ?? ''; // Default to empty string if not found
    $response = [];
    // Validate if the country code exists in your countries config
    if (!empty($country_code) && isset($config['countries'][strtolower($country_code)])) {
        $response['country_code'] = strtolower($country_code);
        $response['country_name'] = $config['countries'][strtolower($country_code)];
    } else {
        // If country code is not found or invalid, return an error
        $response['error'] = 'Unable to detect your country';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
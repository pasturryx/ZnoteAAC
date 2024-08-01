<?php
require_once 'engine/init.php';
require_once 'engine/function/users.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (isset($_POST['name'])) {
    $name = $_POST['name'];
    $errors = [];

    $_POST['name'] = validate_name($name);
    if ($_POST['name'] === false) {
        $errors[] = 'Your name cannot contain more than 2 words.';
    } else {
        if (user_character_exist($name) !== false) {
            $errors[] = 'Sorry, that character name already exists.';
        }
        if (!preg_match("/^[a-zA-Z_ ]+$/", $name)) {
            $errors[] = 'Your name may only contain a-z, A-Z, and spaces.';
        }
        if (strlen($name) < $config['minL'] || strlen($name) > $config['maxL']) {
            $errors[] = 'Your character name must be between ' . $config['minL'] . ' - ' . $config['maxL'] . ' characters long.';
        }
        // name restriction
        $resname = explode(" ", $name);
        foreach($resname as $res) {
            if(in_array(strtolower($res), $config['invalidNameTags'])) {
                $errors[] = 'Your username contains a restricted word.';
            }
            else if(strlen($res) == 1) {
                $errors[] = 'Too short words in your name.';
            }
        }
    }

    if (empty($errors)) {
        $response['success'] = true;
        $response['message'] = 'Name is available!';
    } else {
        $response['message'] = implode(', ', $errors);
    }
}

echo json_encode($response);

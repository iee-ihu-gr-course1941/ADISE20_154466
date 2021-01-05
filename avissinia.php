<?php
 
ini_set('display_errors', 'on');
require_once 'db_connect.php';
require_once 'users.php';
require_once 'game.php';


$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);
$headers = getallheaders();

// if (isset($_SERVER['HTTP_X_TOKEN'])) {
// 	$input['token'] = $_SERVER['HTTP_X_TOKEN'];
// }

$r = array_shift($request);

switch ($r) {
    case 'register':  // Register a user
        if ($method == 'POST') {
            register($method, $request, $input);
        } 
        break;
    case 'login': // Login a user
        if ($method == 'POST') {
            login($method, $request, $input); 
        }
        break;
    case 'authorized': // Returns if user is authorized
        if ($method == 'POST') {
            echo json_encode(is_authorized($headers));
        }
        break;
    case 'profile':
        if ($method == 'GET') { // Returns object contains id, username, last_action
            profile();
        }
        break;
    case 'new-game': // Creates and sets game
        if ($method == 'POST') {
            new_game($input);
        }
        break;
    case 'start-game': // Starts game and sharing cards
        if ($method == 'POST') {
            start_game($input);
        }
        break;
    default:
        // header('HTTP/1.1 404 Not Found');
        exit;
}

?>
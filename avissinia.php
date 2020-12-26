<?php
 
ini_set('display_errors','on' );
require_once 'db_connect.php';
require_once 'users.php';
// require_once 'game.php';


$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);
if (isset($_SERVER['HTTP_X_TOKEN'])) {
	$input['token'] = $_SERVER['HTTP_X_TOKEN'];
}

$r = array_shift($request);

switch ($r) {
    case 'register':
        if ($method == 'POST') {
            register($method, $request, $input); // /register
        } 
        
    case 'login':
        if ($method == "POST") {
            login($method, $request, $input); // /login
        }
    default:  
        // header('HTTP/1.1 404 Not Found');
        exit;
}

?>
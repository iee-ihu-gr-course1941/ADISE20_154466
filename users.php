<?php

function register($method, $request, $input) {
    if(!isset($input['username'])) {
        header('HTTP/1.1 400 Bad Request');
        print json_encode(['errormesg'=>'No username given.']);
        exit;
    }
    global $mysqli_connection;

    $username = $input['username'];
    $password = md5($input['password']);
    $token = generateRandomString();

    // check if users exists
    $res0 = user_exists($username);
    if ($res0->fetch_row()[1] == $username) {
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg' => 'User exists.']);
        exit;
    }

    $sql = 'INSERT INTO player (username, password, token, last_action) VALUES (?, ?, ?, now())';
    $stmt = $mysqli_connection->prepare($sql);
    $stmt->bind_param('sss', $username, $password, $token);
    $stmt->execute();
    
    $sql1 = 'SELECT * FROM player WHERE username = ?';
    $stmt1 = $mysqli_connection->prepare($sql1);
	$stmt1->bind_param('s', $username);
	$stmt1->execute();
	$res = $stmt1->get_result();
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT); // shouldn't return password or token
}

function login($method, $request, $input) {
    if (!isset($input['username']) || $input['username'] == null) {
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>'No username given.']);
        exit;
    }

    $username = $input['username'];
    $password = md5($input['password']);
    
    global $mysqli_connection;
    $sql = 'SELECT * FROM player WHERE username = ? AND password = ?';
    $stmt = $mysqli_connection->prepare($sql);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->fetch_row()[1] == $username) { // not efficient way
        $token = token_return($username, $password);
        header("HTTP/1.1 200 OK");
        print json_encode(['token'=> $token]);
    } else {
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>'Username or password is wrong.']);
        exit;
    }
}


// UTIL FUNCTIONS
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function user_exists($username) {
    global $mysqli_connection;
    $sql0 = 'SELECT * FROM player WHERE username = ?';
    $stmt0 = $mysqli_connection->prepare($sql0);
    $stmt0->bind_param('s', $username);
    $stmt0->execute();
    $res0 = $stmt0->get_result();
    return $res0;
}

function token_return($username, $password) {
    global $mysqli_connection;
    $sql = 'SELECT * FROM player WHERE username = ? AND password = ?';
    $stmt = $mysqli_connection->prepare($sql);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $res = $stmt->get_result();
    $token = $res->fetch_row()[3];
    return $token;
}

function is_authorized($auth_header) {
    global $mysqli_connection;
    $sql = 'SELECT * FROM player WHERE token=?';
    $stmt = $mysqli_connection->prepare($sql);
    $stmt->bind_param('s', $auth_header);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows == 1){
        return true;
    } else {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'User Unauthorized';
        return false;
    }
}

?>
<?php

// dev
$mysqli_connection = mysqli_connect('localhost', 'root', 'root', 'adise20_avissinia'); 
if ($mysqli_connection->connect_error) {
    echo "Not connected, error: " . $mysqli_connection->connect_error;
}

// prod

?>

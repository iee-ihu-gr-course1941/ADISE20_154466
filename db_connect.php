<?php

// $mysqli_connection = mysqli_connect('localhost', 'root', 'root', 'adise20_avissinia'); 

if (gethostname()=='users.iee.ihu.gr') {
  $mysqli_connection = new mysqli('localhost', 'root', 'root', 'adise20_avissinia', null, '/home/staff/it154466/mysql/run/mysql.sock');
} else {
  $mysqli_connection = new mysqli('localhost', 'root', 'root', 'adise20_avissinia');
}

if ($mysqli_connection->connect_errno) {
  echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>

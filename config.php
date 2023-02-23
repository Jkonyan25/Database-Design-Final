<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'comp440');
define('DB_PASSWORD', 'pass1234');
define('DB_NAME', 'comp440');
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>

<?php

include "dbh_functions.php";

$conn = connect();

if($_POST['function'] == "login") {
    $user = $_POST['user'];
    $pwd = $_POST['pwd'];
    $test = log_in($conn, $user, $pwd);
    if($test == 2) {
        echo 0;
    } elseif($test == 3) {
        echo 1;
    } else {
        echo json_encode($test);
    }
}

elseif($_POST['function'] == "signup"){
    $user = $_POST['user'];
    $pwd = $_POST['pwd'];
    $test = sign_up($conn, $user, $pwd);
    if($test == false) {
        echo 0;
    } else {
        echo json_encode($test);
    }
}

<?php

include "src/dbh_functions.php";

$conn = connect();

if($_REQUEST['search-input']) {
    $keyword = $_POST['search-input'];
    $results = search($conn, $keyword);
    echo json_encode($results);
}
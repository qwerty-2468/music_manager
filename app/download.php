<?php

include "src/ytd.php";

session_start();

if($_POST["url-input"]) {
    $_SESSION["url-input"] = $_POST["url-input"];
}

if($_SESSION["url-input"]) {
    $url = $_SESSION["url-input"];
    
    if($_SESSION['metadata']['title']) {
        $metadata['title'] = $_SESSION['metadata']['title'];
    }if($_SESSION['metadata']['artists']) {
        $metadata['artists'] = $_SESSION['metadata']['artists'];
        $_SESSION['metadata']['artists'] = array_map("trim", explode(", ", $_SESSION['metadata']['artists']));
    }if($_SESSION['metadata']['filename']) {
        $metadata['filename'] = $_SESSION['metadata']['filename'];
    }

    if($metadata['title'] && $metadata['artists']) {  
        $output = download_with_different_metadata($url, $metadata);
    }
    if($output == false) {
        echo "Error while downloading!";
    } else {
        echo "Successfully downloaded!" . '<br><input type="button" id="add-db-btn" onclick="submitForm(this.id)" value="Add to database">';
    }
}
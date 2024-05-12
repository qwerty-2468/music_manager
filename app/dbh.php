<?php

include 'src/dbh_functions.php';

session_start();

$conn = connect();

// Add song to database
if($_SESSION['metadata']['title']) {
    $metadata['title'] = $_SESSION['metadata']['title'];
}if($_SESSION['metadata']['artists']) {
    $metadata['artists'] = $_SESSION['metadata']['artists'];
}if($_SESSION['metadata']['filename']) {
    $metadata['filename'] = $_SESSION['metadata']['filename'];
    
    $addSong = add_song($conn, $metadata['title'], $metadata['artists']);

    $oldFile = "/songs/{$metadata['filename']}.mp3";

    if($addSong['state' != true]) {
        echo "Error: " . $addSong;
    } else {
        $newFile = $addSong["file_path"];
        rename($oldFile, $newFile);
        $_SESSION['file_path'] = $addSong['file_path'];
        echo "Done!";
    }
}


// Show artist details
if($_POST["function"] == "show_artist") {
    $artist = $_POST['artist'];
    $songs = get_artist_songs($conn, $artist);
    echo json_encode($songs);
}
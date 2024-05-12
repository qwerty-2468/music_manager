<?php

include "src/dbh_functions.php";

session_start();

$user = $_SESSION['user'];

$conn = connect();

if($_REQUEST["function"] == "get-playlists") {
    if($_SESSION['user']) {
        $playlists = get_playlists($conn, $_SESSION['user']);
        echo json_encode($playlists);
    } else {
        echo "No user!";
    }
} 

elseif($_REQUEST["function"] == "add-song") {
    $playlistId = $_REQUEST["playlist_id"];
    $songId = $_REQUEST["song_id"];
    $output = add_to_playlist($conn, $songId, $playlistId);
    echo $output;
} 

elseif($_REQUEST["function"] == "remove-song") {
    $playlistId = $_REQUEST["playlist_id"];
    $number = $_REQUEST["number"];
    $output = remove_from_playlist($conn, $number, $playlistId);
    echo $output;
} 

elseif($_REQUEST["function"] == "delete") {
    $playlistId = $_REQUEST["playlist_id"];
    $output = delete_playlist($conn, $playlistId);
    echo json_encode($output);
} 

elseif($_REQUEST["function"] == "get-items") {
    $playlistId = $_REQUEST["playlist_id"];
    $songs = get_playlist_items($conn, $playlistId);
    echo json_encode($songs);
} 

elseif($_REQUEST["function"] == "add") {
    $playlistName = $_REQUEST["playlist_name"];
    $output = add_playlist($conn, $playlistName, $user);
    echo json_encode($output);
}

elseif($_REQUEST["function"] == "rename") {
    $newName = $_REQUEST["new_name"];
    $playlistId = $_REQUEST["playlist_id"];
    $output = rename_playlist($conn, $playlistId, $newName, $user);
    echo json_encode($output);
}

elseif($_REQUEST["function"] == "change_num") {
    $numToChange = $_REQUEST["num_to_change"];
    $numAfter = $_REQUEST["num_after"];
    $playlistId = $_REQUEST["playlist_id"];
    $output = change_num($conn, $playlistId, $numToChange, $numAfter);
    echo json_encode($output);
}
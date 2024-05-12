<?php

session_start();
    
if($_POST['title']) {
    $title = trim($_POST['title']);
    $_SESSION['metadata']['title'] = $title;
}if($_POST['artist']) {
    $_SESSION['metadata']['artists'] = $_POST['artist'];
}if($_POST['title'] && $_POST['artist']) {
    $_SESSION['metadata']['filename'] = $_POST['artist'] . " - " . $title;  
}


?>


<main>       
    <form id="download">
        <label for="url-input">Download by URL</label>
        <input type="text" id="URL-input" name="url-input" placeholder="YouTube URL">
        <input type="button" id="url-submit" value="Download" onclick="submitForm(this.id)"/>
    </form>
    <br>
    <form id="search">
        <label for="yts-input">Search on YouTube</label>
        <input type="text" id="yts-input" name="yts-input" placeholder="Keyword">
        <input type="button" id="keyword-submit" value="Search" onclick="submitForm(this.id)"/>
    </form>
    <br>
    <form id="upload">
        <label for="upfile">Upload file</label>
        <input type="file" name="upfile" id="upfile">
        <input type="button" id="upload-submit" value="Upload" onclick="submitForm(this.id)">
    </form>
</main>
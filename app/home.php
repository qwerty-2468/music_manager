<?php

session_start();
unset($_SESSION['metadata']);
unset($_SESSION['url-input']);
unset($_SESSION['file_path']);

$_SESSION['user'] = [
    "user_id" => 1,
    "username" => "querty"
];

?>


<!DOCTYPE html>
<html>

    <head>
        <title>MusicManager</title>
        <script src="https://code.jquery.com/jquery-3.5.0.js" integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc=" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css" href="src/css/style.css">
    </head>

    <body>
        
        <section id="nav-bar" class="main">
            <div>
                <button id="add-music-btn">Add Music</button>
                <input type="text" id="search-input" placeholder="Search...">
            </div>
        </section>

        <section id="side-bar">
            <div class="side-bar" id="playlists"></div> 
        </section>

        <main class="main">

            <div id="content"></div>

        </main>

        <section id="play-bar" class="main">
            <div id="title"></div>
            <div id="controls">
                <img class="btn" id="previous" src="src/img/previous.svg" height="35">
                <img class="btn" id="play-pause" src="src/img/play.svg" height="35">
                <i id="current-time"></i>
                <input type="range" id="seek" value="0" max="" step="0.001"/>
                <i id="left-time"></i>
                <img class="btn" id="mute" src="src/img/unmuted.svg" height="35">
                <input type="range" id="volume" value="1" max="1" step="0.01"/>
                <img class="btn" id="next" src="src/img/next.svg" height="35">                
            </div>
            <div id="audio"></div>
        </section>

    </body>
    
    <script src="src/js/script.js"></script>

</html>
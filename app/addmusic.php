<?php

session_start();
unset($_SESSION['metadata']);
unset($_SESSION['url-input']);
unset($_SESSION['file_path']);

?>


<!DOCTYPE html>
<html>

    <head>
        <script src="https://code.jquery.com/jquery-3.5.0.js" integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc=" crossorigin="anonymous"></script>
    </head>

    <body>
        
        <h1 class="heading">Add Music</h1>
        
        <div id="main"></div>

    </body>
    
    <script type="text/javascript" src="src/js/addmusic.js"></script>

</html>
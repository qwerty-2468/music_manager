<?php


if($_POST["yts-input"]){
    $keyword = str_replace(" ", "+", $_POST["yts-input"]);
    $api_key = "AIzaSyCi0vplUQmYzdPIHkDsx98JMdLUEJQ-YBM";
    $queryURL = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=3&key={$api_key}&q=" . $keyword;
    $response = file_get_contents($queryURL);
    if ($response === FALSE) {
        echo "anyád";
    }else{ 
        $decoded = json_decode($response, true);
    }
}


function get_video_url($item=array()) {
    $id = $item['id']['videoId'];
    $url = "https://www.youtube.com/watch?v={$id}";
    return $url;
}

?>

<!DOCTYPE html>
<html>

    <head>
        <title>MusicManager</title>
    </head>

    <body>
        
        <h3 class="heading">Resoults:</h3>
        
        <form id="download">
            
            <?php

            $optionId = 1;
            foreach ($decoded['items'] as $item){
                echo "<input type=radio id=option{$optionId} name='url-input' value=" . get_video_url($item) . "><label for=option{$optionId}><a href=" . get_video_url($item) . " target='_blank'>" . $item['snippet']['title'] . "</a><br>Author: " . $item['snippet']['channelTitle'] . "<br>";
                $optionId += 1;
            }

            ?>

            <input type="button" id="url-submit" onclick="submitForm(this.id)" value="Download">

        </form>

    </body>

</html>
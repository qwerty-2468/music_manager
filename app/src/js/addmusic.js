$(document).ready($("#main").load("metadata.php"));
        
function submitForm(id) {
    let data, php;

    if (id == "url-submit") {
        urlInput = $("#URL-input").val();
        if (!urlInput) {
            alert("No URL!");
            return;
        } else {
            ytUrlTemplate = "www.youtube.com/watch?v=";
            if (!urlInput.includes(ytUrlTemplate)) {
                alert("Not supported URL!");
                return;
            } else {
                data = {
                    "url-input": urlInput
                };
                php = "download.php";
            }
        }
    } 
    else if (id == "keyword-submit") {
        ytsInput = $("#yts-input").val();
        if (!ytsInput) {
            alert("No input!");
            return;
        } else {
            data = {
                "yts-input": ytsInput
            };
            php = "yts.php";
        }        
    } 
    else if (id == "next-btn") {
        data = $("#metadata").serializeArray();
        if (!data[0]['value']) {
            alert("No Title!");
            return;
        } else if (!data[1]['value']) {
            alert("No Artist!");
            return;
        }
        php = "add_options.php";
    } 
    else if (id == "add-db-btn") {
        data = null;
        php = "dbh.php";
    } 
    else if (id == "upload-submit") {
        music = document.getElementById("upfile").files[0];
        if (!music) {
            alert("No input file!");
            return;
        } else {
            php = "upload.php";
            data = new FormData();
            data.append("upfile", music);
            $.ajax( {
                url: 'upload.php',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false
            } ) 
            .then (function(response) {
                $("#main").html(response);    
            })
            return;
        }
    } 
    $("#main").load(php, data);
}

function getMetadata() {
    data = $("#get-metadata").serializeArray();
    php = "src/get_metadata.php";
    $.get(php, data, function(result, status) {
        if (status == "success") {
            object = JSON.parse(result);
            $("#title").val(object['title']);
            artists = "";
            for (artist of object['artists']) {
                artists += artist;
                artists += ", ";
            }
            artists = artists.slice(0, -2);
            $("#artist").val(artists);
        }
    });
}
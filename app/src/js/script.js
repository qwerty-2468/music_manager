
playPause = $('#play-pause');
mute = $('#mute');
audio = new Audio();
duration = audio.duration;
var list;
var currentSong;
var playlists;


(function start() {
    listPlaylists();
}())


function listPlaylists() {
    $("#playlists").html("<table id=list><caption><h4>Playlists</h4></caption></table>");
    playlists = $.ajax({
        type: 'POST',
        url: "playlist.php", 
        data: {"function": "get-playlists"},
        dataType: 'json',
        context: document.body,
        global: false,
        async:false,
        success: function(response) {
            playlists = response;
            for (num in playlists) {
                playlist = playlists[num];
                $("#list").append(`
                <tr>
                    <td id="playlist${playlist['playlist_id']}" onclick="openPlaylist(` + playlist['playlist_id'] + ", '" + playlist['playlist_name'] + `')">${playlist['playlist_name']}</td>
                    <td class="dropdown">
                        <span id="options${playlist['playlist_id']}" onclick="dropdownOptions(${playlist['playlist_id']})">...</span>
                        <ul id="option-list${playlist['playlist_id']}" class="playlist-option-dropdown"></ul>
                        </td>
                </tr>
                `);
            }
            return playlists;
        }
    }).responseText;
    playlists = JSON.parse(playlists);
    $("#list").append(`
    <tr>
        <td id="add">
            <img src='src/img/add.svg' style="width:20px;height:20px;" onclick="addPlaylist()">
        </td>
    </tr>`)
}


function dropdownOptions(playlistId) {
    console.log(playlistId);
    $(`#option-list${playlistId}`).html(`
    <li onclick='deletePlaylist(` + playlistId + `)'>Delete</li>
    <li onclick='renamePlaylist(` + playlistId + `)'>Rename</li>
    `);
    $(document).on('click', function(e) {
        if (e.target.id != `options${playlistId}`) {
            $(`#option-list${playlistId}`).hide();
        }
    })
    $(`#option-list${playlistId}`).toggle();
}


function deletePlaylist(playlistId) {
    $.post("playlist.php", {
        "function": "delete",
        "playlist_id": playlistId
    }, function(response) {
        if (response == "true") {
            console.log("Playlist successfully deleted!");
            listPlaylists();
            $("#content").html("");
        } else {
            console.log("Couldn't delete!");
        }
    });
}


function renamePlaylist(playlistId) {
    $(`#playlist${playlistId}`).html(`<input id="rename-input" type="text" placeholder="Playlist name here" style="width:120px;">`);
    $("#rename-input").focus();
    $("#rename-input").on("blur", function(){
        if (this.value) {
            newName = this.value;
            $.post("playlist.php", {
                "function": "rename",
                "playlist_id": playlistId,
                "new_name": newName
            }, function(response) {
                if (response == "false") {
                    $("#content").html("This playlist already exists!");
                    listPlaylists();
                } else {
                    console.log("Playlist successfully renamed!");
                    listPlaylists();
                    $('#playlist' + playlistId).click();
                }
            });
        } else {
            listPlaylists();
            $('#playlist' + playlistId).click();
        }
    })
}


function openPlaylist(playlistId, name) {
    $(".active-playlist").removeClass("active-playlist");
    $(`#playlist${playlistId}`).addClass("active-playlist");
    $.post("playlist.php", {
        "function": "get-items",
        "playlist_id": playlistId
    }, function(response) {
        if (response.charAt(0) == "{") {
            $("#content").html(`<table id="artist-songs"><caption><h3>${name}</h3></caption></table>`);
            songs = JSON.parse(response);
            console.log(songs);
            list = songs;
            for (num in songs) {
                song = songs[num];
                $("#artist-songs").append(`
                <tr id='${song["number"]}' draggable="true" ondragstart="drag(event)" ondragover="event.preventDefault()" ondrop="drop(event)">
                    <td>${song['number']}</td>
                    <td onclick='playSong(` + JSON.stringify(song) + `)'>${song['title']}</td>
                    <td>` + artistsSpan(song['artist']) + `</td>
                    <td  class="dropdown">
                        <img id="add${song["song_id"]}" src='src/img/add_to_playlist.svg' onclick='dropdownPlaylists(${song["song_id"]}, "list${song["number"]}")'>
                        <ul id="list${song["number"]}" class="playlists-dropdown"></ul>
                        </td>
                    <td>
                        <img class="remove" src='src/img/delete.svg' alt='remove' onclick="remove(${song['number']}, ${playlistId})">
                        </td>
                </tr>`);
            }
        } else {
            $("#content").html(response);
        }
    });
}


function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}


function drop(ev) {
    ev.preventDefault();
    var numToChange = parseInt(ev.dataTransfer.getData("text"));
    var numAfter = parseInt(ev.target.parentElement.id);
    var currPlaylist = $(".active-playlist").attr("id").replace("playlist", "")
    changeOrder(numToChange, numAfter, currPlaylist);
}


function changeOrder(numToChange, numAfter, playlistId) {
    $.post("playlist.php", {
        "function": "change_num",
        "num_to_change": numToChange,
        "num_after": numAfter,
        "playlist_id": playlistId
    }, function(response) {
        console.log(response);
        $(`#playlist${playlistId}`).click();
        if (currentSong != numToChange) {
            if (currentSong > numToChange && currentSong <= numAfter) {
                currentSong--;
            } else if (currentSong < numToChange && currentSong > numAfter) {
                currentSong++;
            }
        } else {
            if (currentSong > numAfter - 1) {
                currentSong = numAfter + 1;
            } else if (currentSong < numAfter) {
                currentSong = numAfter;
            }
        }
    })
}


function artistsSpan(artists) {
    artistsStr = "";
    for (artist of artists) {
        artistsStr += `<span onclick='showArtist("${artist}")'>${artist}</span>`
    }
    return artistsStr;
}


function showArtist(artist) {
    $(".active-playlist").removeClass("active-playlist");
    $.post("dbh.php", {
        "function": "show_artist",
        "artist": artist
    }, function(response) {
        if (response.charAt(0) == "[") {
            console.log(response)
            $("#content").html(`<table id="artist-songs"><caption><h3>${artist}</h3></caption></table>`);
            songs = JSON.parse(response);
            list = "";
            for (num in songs) {
                song = songs[num];
                $("#artist-songs").append(`
                <tr>
                    <td onclick='playSong(` + JSON.stringify(song) + `)'>${song['title']}</td>
                    <td>` + artistsSpan(song['artist']) + `</td>
                    <td  class="dropdown">
                        <img id="add${song["song_id"]}" src='src/img/add_to_playlist.svg' onclick='dropdownPlaylists(${song["song_id"]}, ${song["song_id"]})'>
                        <ul id="${song["song_id"]}" class="playlists-dropdown"></ul>
                        </td>
                </tr>`);
            }
        } else {
            $("#content").html(response);
        }
    });
}


(function search(){
    $("#search-input").on("keyup", e => {
        $(".active-playlist").removeClass("active-playlist");
        keyword = e.target.value;
        if(keyword) {
            $.post("search.php", {
                "search-input": keyword
            }, function(response) {
                if (response !== "[]") {
                    $("#content").html("<table id='songs'><caption><h3>Songs</h3></caption></table><table id='artists'><caption><h3>Artists</h3></caption></table>");
                    results = JSON.parse(response);
                    for (num in results) {
                        result = results[num];
                        if(result['title']) {
                            $("#songs").append(`
                            <tr>
                                <td onclick='playSong(` + JSON.stringify(result) + `)'>${result['title']}</td>
                                <td>` + artistsSpan(result['artist']) + `</td>
                                <td  class="dropdown">
                                    <img id="add${result["song_id"]}" src='src/img/add_to_playlist.svg' onclick='dropdownPlaylists(${result["song_id"]}, ${result["song_id"]})'>
                                    <ul id="${result["song_id"]}" class="playlists-dropdown"></ul>
                                    </td>
                            </tr>`);
                        } else if (result['name']) {
                            $("#artists").append(`<tr><td  onclick='showArtist("${result['name']}")'>${result['name']}</td></tr>`);
                        }
                    }
                } else {
                    $("#content").html("No results...");
                }
            });
        } else {
            $("#content").html("");
        }
    });
}())


$("#search-input").on("blur", e => {
    e.target.value = "";
});


function dropdownPlaylists(songId, elementId) {
    // list playlists
    $(`#${elementId}`).html("");
    for (num in playlists) {
        playlist = playlists[num];
        $(`#${elementId}`).append(`<li onclick='addToPlaylist(` + playlist['playlist_id'] + ", " + songId + `)'>${playlist['playlist_name']}</li>`);
    }
    $(document).on('click', function(e) {
        if (e.target.id != `add${songId}`) {
            $(`#${elementId}`).hide();
            $(document).unbind('click');
        }
    })
    $(`#${elementId}`).toggle();
}


function addToPlaylist(playlistId, songId) {
    $.post("playlist.php", {
        "function": "add-song",
        "playlist_id": playlistId,
        "song_id": songId
    }, function(response) {
        if (response == 1) {
            console.log("Song successfully added!");
            if ($(`#playlist${playlistId}`).hasClass("active-playlist")) {
                $(`#playlist${playlistId}`).click();
            }
        } else {
            alert("Couldn't add!");
        }
    });
}


function playSong(song) {
    $("#title").html(artistsSpan(song['artist']) + " - " + song['title']);
    currentSong = parseInt(song['number']);
    audio.src = song['file_path'];
    audio.preload = true;
    audio.play();
    $("#audio").html(audio);
    playPause.attr("src", "src/img/pause.svg");
    $("#seek").attr("max", audio.duration);
}


playPause.on('click', function() {
    if (!audio.paused) {
        audio.pause();
        playPause.attr("src", "src/img/play.svg");
    } else if (audio.src) {
        audio.play();
        playPause.attr("src", "src/img/pause.svg");
    }
});

document.getElementById("mute").addEventListener('click', function() {
    if(audio.muted) {
        audio.muted = false;
        $("#mute").attr("src", "src/img/unmuted.svg");
        $("#volume").attr("disabled", false);
    } else {
        audio.muted = true
        $("#mute").attr("src", "src/img/muted.svg");
        $("#volume").attr("disabled", true);
    }
});

$("#seek").on("input", function() {
    audio.currentTime = $(this).val();
    $("#seek").attr("max", audio.duration);
});

audio.addEventListener('timeupdate',function (){
    curtime = parseInt(audio.currentTime, 10);
    document.getElementById("seek").value = curtime;
    $("#seek").attr("max", audio.duration);
    var percentage = (audio.currentTime / audio.duration).toFixed(3);
    //progressBar.style.transition = "";

    //set current time
    var minute = Math.floor(audio.currentTime / 60);
    var second = Math.floor(audio.currentTime % 60);
    var leftTime = audio.duration - audio.currentTime;
    $("#current-time").html(("0" + minute).substr(-2) + ":" + ("0" + second).substr(-2));

    //set left time
    var leftMinute = Math.floor(leftTime / 60);
    var leftSecond = Math.floor(leftTime % 60);

    $("#left-time").html(("0" + leftMinute).substr(-2) + ":" + ("0" + leftSecond).substr(-2));

    //set time bar
    //progressBar.style.width = percentage * 100 + "%";
});

$("#volume").on("input", function() {
    audio.volume = $(this).val();
});

function next() {
    if (list) {
        if (list[currentSong + 1]) {
            playSong(list[currentSong + 1]);
        } else {
            playSong(list[1]);
        }
    } else {
        console.log("No list");
    }
}

$("#next").on("click", function() {
    next();
});

audio.addEventListener("ended", next);

$("#previous").on("click", function(){
    if (list) {
        if (list[currentSong - 1]) {
            playSong(list[currentSong - 1]);
        } else {
            playSong(list[Object.keys(list).pop()]);
        }
    } else {
        console.log("No list");
    }
});

function remove(number, playlistId) {
    $.post("playlist.php", {
        "function": "remove-song",
        "playlist_id": playlistId,
        "number": number
    }, function(response) {
        if (response == 1) {
            console.log("Song successfully removed!");
            $(`#playlist${playlistId}`).click();
        } else {
            console.log("Couldn't remove!");
        }
    });
}

function addPlaylist() {
    $("#add").html(`<input id="add-input" type="text" placeholder="Playlist name here" style="width:120px;">`);
    $("#add-input").focus();
    $("#add-input").on("blur", function(){
        if (this.value) {
            playlistName = this.value;
            $.post("playlist.php", {
                "function": "add",
                "playlist_name": playlistName
            }, function(response) {
                if (response == "false") {
                    $("#content").html("This playlist already exists!");
                } else {
                    response = JSON.parse(response);
                    playlistId = parseInt(response);
                    listPlaylists();
                    $('#playlist' + playlistId).click();
                }
            });
        } else {
            $("#add").html(`<img src='src/img/add.svg' style="width:20px;height:20px;" onclick="addPlaylist()">`);
        }
    })
}

$("#add-music-btn").on("click", function() {
    $(".active-playlist").removeClass("active-playlist");
    $("#content").html("<div id='add-music'></div>")
    $("#add-music").load("addmusic.php");
})
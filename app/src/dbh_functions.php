<?php


function connect() {
    $dbServer = "localhost";
    $dbUser = "music_manager";
    $dbPassword = "***";
    $dbName = "music_manager";

    $conn = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);

    if(!$conn) {
        return mysqli_connect_error();
    } else {
        return $conn;
    }
}


function add_song($conn, $title, $artists) {
    $artistIds = array();
    foreach($artists as $artist) {
        $sql = "SELECT artist_id FROM artists WHERE name='{$artist}'";
        $query = mysqli_query($conn, $sql);
        if($query->{num_rows} == 0) {
            $sql = "INSERT INTO artists (name) VALUES ('{$artist}');";
            $query = mysqli_query($conn, $sql);
            if(!$query){
                return "Can't insert!";
            } else {
                $sql = "SELECT artist_id FROM artists WHERE name='{$artist}';";
                $query = mysqli_query($conn, $sql);
                $artist_id = intval(mysqli_fetch_assoc($query)['artist_id']);
            }
        } else{
            $artist_id = intval(mysqli_fetch_assoc($query)['artist_id']);
        }
        array_push($artistIds, $artist_id);
    }
    $artistIds = json_encode($artistIds);
    $sql = "SELECT title, artist FROM songs;";
    $query = mysqli_query($conn, $sql);
    $all_song = mysqli_fetch_all($query, MYSQLI_ASSOC);
    $titles = [];
    foreach($all_song as $song) {
        if($song['artist'] == $artistIds){
            array_push($titles, $song['title']);
        }
    }
    if(gettype(array_search($title, $titles)) !== "boolean") {
        return "This song already exists!";
    } else {
        $sql = "INSERT INTO songs (title, artist) VALUES (\"{$title}\", \"{$artistIds}\");";
        $query = mysqli_query($conn, $sql);
        if(!$query) {
            return mysqli_error($conn);
        } else {
            $sql = "SELECT song_id FROM songs WHERE title=\"{$title}\" AND artist=\"{$artistIds}\";";
            $query = mysqli_query($conn, $sql);
            if(!$query) {
                return mysqli_error($conn);
            } else {
                $songId = mysqli_fetch_assoc($query)['song_id'];
                $songIdPad = str_pad($songId, 5, "0", STR_PAD_LEFT);
                $artistStr = implode(", ", $artists);
                $file_path = "/songs/{$songIdPad}_{$artistStr}_{$title}.mp3";
                $sql = "UPDATE songs SET file_path=\"{$file_path}\" WHERE song_id='{$songId}';"; 
                $query = mysqli_query($conn, $sql);
                if(!$query) {
                    $return = mysqli_error($conn);
                } else {
                    $return = [
                        "song_id" => $songId,
                        "file_path" => $file_path,
                        "state" => true
                    ];
                }
                return $return;
            }
        }
    }
}


function get_playlists($conn, $user) {
    $userId = $user['user_id'];
    $sql = "SELECT playlist_id, playlist_name FROM playlists WHERE user_id={$userId};";
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        return mysqli_error($conn);
    } else {
        $playlists = mysqli_fetch_all($query, MYSQLI_ASSOC);
        return $playlists;
    } 
}


function get_artists_for_songs($conn, $songs, $start) {
    $count = $start;
    foreach($songs as $song){
        $artists = json_decode($song['artist']);
        $songs[$count]['artist'] = array();
        foreach($artists as $artist) {
            $artistId = intval($artist);
            $sql = "SELECT name FROM artists WHERE artist_id={$artistId};";
            $query = mysqli_query($conn, $sql);
            if(!$query) {
                return "Wrong artist ID!";
            } else {
                $name = mysqli_fetch_assoc($query)['name'];
                array_push($songs[$count]['artist'], $name);
            }
        }
        $count += 1;
    }
    return $songs;
}


function get_playlist_items($conn, $playlistId) {
    $playlistId = intval($playlistId);
    $sql = "SELECT song_id, number FROM playlist_songs WHERE playlist_id={$playlistId} ORDER BY number;";
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        return mysqli_error($conn);
    } else {
        $ids = mysqli_fetch_all($query, MYSQLI_ASSOC);
        if(!$ids){
            return "No songs on this playlist!";
        } else {
            foreach($ids as $id){
                $sql = "SELECT * FROM songs WHERE song_id={$id['song_id']};";
                $query = mysqli_query($conn, $sql);
                if(!$query) {
                    return "Wrong song ID!";
                } else {
                    $song = mysqli_fetch_all($query, MYSQLI_ASSOC);
                    $number = intval($id['number']);
                    $songs[$number] = $song[0];
                    $songs[$number]['number'] = $number;
                }
            }
            $songs = get_artists_for_songs($conn, $songs, 1);
            return $songs;
        }
        
    }
}


function search($conn, $keyword) {
    $keyword = strtolower($keyword);
    $results = [];

    // Search for songs
    $sql = "SELECT * FROM songs;";
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        return mysqli_error($conn);
    } else {
        $songs = mysqli_fetch_all($query, MYSQLI_ASSOC);
        foreach($songs as $song) {
            $title = strtolower($song['title']);
            $pos = strpos($title, $keyword);
            if(gettype($pos) != "boolean") {
                array_push($results, $song);
            }
        }
        if($results != []) {
            $results = get_artists_for_songs($conn, $results, 0);
        }
    }

    // Search for artists
    $sql = "SELECT * FROM artists;";
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        return mysqli_error($conn);
    } else {
        $artists = mysqli_fetch_all($query, MYSQLI_ASSOC);
        foreach($artists as $artist) {
            $name = strtolower($artist['name']);
            $pos = strpos($name, $keyword);
            if(gettype($pos) != "boolean") {
                array_push($results, $artist);
            }
        }
    }

    return $results;
}


function add_to_playlist($conn, $songId, $playlistId) {
    $songId = intval($songId);
    $playlistId = intval($playlistId);

    // Find out what is the last number of the playlist
    $sql = "SELECT song_id, number FROM playlist_songs WHERE playlist_id={$playlistId} ORDER BY number DESC;";
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        return mysqli_error($conn);
    } else {
        $lastNumber = intval(mysqli_fetch_assoc($query)['number']);
        
        // Add song after the last number
        $sql = "INSERT INTO playlist_songs (playlist_id, song_id, number) VALUES ({$playlistId}, {$songId}, {$lastNumber} + 1);";
        $query = mysqli_query($conn, $sql);
        if(!$query) {
            return FALSE;
        } else {
            return TRUE;
        } 
    }
}


function remove_from_playlist($conn, $number, $playlistId) {
    $number = intval($number);
    $playlistId = intval($playlistId);
    $sql = "SELECT number FROM playlist_songs WHERE playlist_id={$playlistId} AND number={$number};";
    $query = mysqli_query($conn, $sql);
    $test = mysqli_fetch_assoc($query);
    if(!$query) {
        return mysqli_error($conn);
    } elseif($test == null) {
        return "No song with this number!";
    } else{
        $sql = "DELETE FROM playlist_songs WHERE playlist_id={$playlistId} AND number={$number};";
        $query = mysqli_query($conn, $sql);
        if(!$query) {
            return mysqli_error($conn);
        } else {
            $sql = "SELECT number FROM playlist_songs WHERE playlist_id={$playlistId} AND number>{$number} ORDER BY number;";
            $query = mysqli_query($conn, $sql);
            if(!$query) {
                return mysqli_error($conn);
            } else {
                $numbers = mysqli_fetch_all($query, MYSQLI_ASSOC);
                foreach($numbers as $num) {
                    $numFrom = intval($num['number']);
                    $numTo = intval($num['number']) - 1;
                    $sql = "UPDATE playlist_songs SET number={$numTo} WHERE playlist_id={$playlistId} AND number={$numFrom};";
                    $query = mysqli_query($conn, $sql);
                    if(!$query) {
                        return mysqli_error($conn);
                    }
                }
                return true;
            }
        }
    }
}


function add_playlist($conn, $playlistName, $user) {
    $userId = intval($user['user_id']);
    $sql = "SELECT playlist_name FROM playlists WHERE playlist_name='{$playlistName}' AND user_id={$userId};";
    $query = mysqli_query($conn, $sql);
    if(!$query){
        return mysqli_error($conn);
    } else {
        $exists = mysqli_fetch_assoc($query);
        if($exists != null) {
            return false;
        } else {
            $sql = "INSERT INTO playlists (playlist_name, user_id) VALUES ('{$playlistName}', {$userId});";
            $query = mysqli_query($conn, $sql);
            if(!$query){
                return mysqli_error($conn);
            } else {
                $sql = "SELECT playlist_id FROM playlists WHERE playlist_name='{$playlistName}' AND user_id={$userId};";
                $query = mysqli_query($conn, $sql);
                if(!$query){
                    return mysqli_error($conn);
                } else {
                    $playlistId = mysqli_fetch_assoc($query)['playlist_id'];
                    return $playlistId;
                }
            }
        }
    }
}


function delete_playlist($conn, $playlistId) {
    $playlistId = intval($playlistId);
    $sql = "DELETE FROM playlists WHERE playlist_id={$playlistId};";
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        return mysqli_error($conn);
    } else {
        $sql = "DELETE FROM playlist_songs WHERE playlist_id={$playlistId};";
        $query = mysqli_query($conn, $sql);
        if(!$query) {
            return mysqli_error($conn);
        } else {
            return true;
        }
    }
}


function rename_playlist($conn, $playlistId, $newName, $user) {
    $playlistId = intval($playlistId);
    $userId = intval($user['user_id']);
    $sql = "SELECT playlist_name FROM playlists WHERE playlist_name='{$newName}' AND user_id={$userId};";
    $query = mysqli_query($conn, $sql);
    if(!$query){
        return mysqli_error($conn);
    } else {
        $exists = mysqli_fetch_assoc($query);
        if($exists != null) {
            return false;
        } else {
            $sql = "UPDATE playlists SET playlist_name='{$newName}' WHERE playlist_id={$playlistId};";
            $query = mysqli_query($conn, $sql);
            if(!$query) {
                return mysqli_error($conn);
            } else {
                return true;
            }
        }
    }   
}


function get_artist_songs($conn, $artist) {
    $sql = "SELECT artist_id FROM artists WHERE name='{$artist}';";
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        return mysqli_error($conn);
    } else {
        $artistId = mysqli_fetch_assoc($query)['artist_id'];
        $sql = "SELECT * FROM songs WHERE artist LIKE '%{$artistId}%';";
        $query = mysqli_query($conn, $sql);
        if(!$query) {
            return mysqli_error($conn);
        } else {
            $songs = mysqli_fetch_all($query, MYSQLI_ASSOC);
            $songs = get_artists_for_songs($conn, $songs, 0);
            return $songs;
        }
    }
}


function change_num($conn, $playlistId, $numToChange, $numAfter) {
    $playlistId = intval($playlistId);
    $numToChange = intval($numToChange);
    $numAfter = intval($numAfter);
    if($numToChange != $numAfter){
        $sql = "UPDATE playlist_songs SET number = 0 WHERE playlist_id = {$playlistId} AND number = {$numToChange} ;";
        $query = mysqli_query($conn, $sql);
        if(!$query){
            return mysqli_error($conn);
        } else {
            $sql = "UPDATE playlist_songs SET number = number - 1 WHERE playlist_id = {$playlistId} AND number > {$numToChange};";
            $query = mysqli_query($conn, $sql);
            if(!$query) {
                return mysqli_error($conn);
            }
            if($numToChange < $numAfter){
                $numAfter--;
            }
            $sql = "UPDATE playlist_songs SET number = number + 1 WHERE playlist_id = {$playlistId} AND number > {$numAfter} ;";
            $query = mysqli_query($conn, $sql);
            if(!$query){
                return mysqli_error($conn);
            } else {
                $sql = "UPDATE playlist_songs SET number = {$numAfter} + 1 WHERE playlist_id = {$playlistId} AND number = 0;";
                $query = mysqli_query($conn, $sql);
                if(!$query){
                    return mysqli_error($conn);
                } else {
                    return true;
                }
            }
        }
    } else {
        return "No change!";
    }
}


function sign_up($conn, $user, $pwd) {
    $sql = "SELECT * FROM users WHERE username='{$user}';";
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        return mysqli_error($conn);
    } else {
        if(mysqli_fetch_assoc($query) != null) {
            return false;
        } else {
            $pwd_hashed = password_hash($pwd, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users(username, password) VALUES ('$user', '$pwd_hashed');";
            $query = mysqli_query($conn, $sql);
            if(!$query) {
                return mysqli_error($conn);
            } else {
                return true;
            }
        }
    }
}


function log_in($conn, $user, $pwd) {
    $pwd_hashed = password_hash($pwd, PASSWORD_DEFAULT);
    $sql = "SELECT password FROM users WHERE username = '{$user}';";
    $query = mysqli_query($conn, $sql);
    if(!$query) {
        return mysqli_error($conn);
    } else {
        $realPwd = mysqli_fetch_assoc($query);
        if($realPwd == null) {
            return 2;
        } elseif($pwd_hashed != $realPwd['password']) {
            return 3;
        } else {
            return 4;
        }
    }
}
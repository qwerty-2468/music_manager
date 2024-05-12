<?php

session_start();


// Spotify Application Client ID and Secret Key
$client_id     = '20e61e9f9dab490ba04cc1bad5ed9747'; 
$client_secret = 'bbb23a726f78488eb6c46bce2c24d6da'; 

if($_REQUEST['keyword']) {
    $keyword = $_REQUEST['keyword'];
    $accessToken = spotify_auth($client_id, $client_secret);
    $metadata = collect_metadata(search_for_metadata($keyword, $accessToken)[0]);
    $_SESSION['metadata'] = $metadata;
    echo json_encode($metadata);
}


function spotify_auth($client_id, $client_secret){
    // Get Spotify Access Token
    $authURL = 'https://accounts.spotify.com/api/token';
    $chAuth = curl_init();
    curl_setopt($chAuth, CURLOPT_URL, $authURL);
    curl_setopt($chAuth, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($chAuth, CURLOPT_POST, TRUE);
    curl_setopt($chAuth, CURLOPT_POSTFIELDS, 'grant_type=client_credentials'); 
    curl_setopt($chAuth, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret))); 

    $authResult = curl_exec($chAuth);
    $authResultJson = json_decode($authResult, true);
    $accessToken = $authResultJson['access_token'];
    
    return $accessToken;
}


function search_for_metadata($keyword, $accessToken){
    // Get results from API
    $keyword = str_replace(" ", "+", $keyword);
    $ch = curl_init("https://api.spotify.com/v1/search?type=track&limit=1&q={$keyword}");

    curl_setopt($ch, CURLOPT_HTTPHEADER,
        array(
            "Authorization: Bearer {$accessToken}",
        )
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $results = json_decode($response, TRUE)['tracks']['items'];
    
    return $results;

}


function get_metadata($song_url, $accessToken){
    // Get results from API
    if(strpos($song_url, "?") !== false) {
        $song_url = substr($song_url, 31, strpos($song_url, "?")-30);
    }else{
        $song_url .= "?";
        $song_url = substr($song_url, 31, strpos($song_url, "?")-30);
    }
    $ch = curl_init("https://api.spotify.com/v1/tracks/{$song_url}");

    curl_setopt($ch, CURLOPT_HTTPHEADER,
        array(
            "Authorization: Bearer {$accessToken}",
        )
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $data_array = json_decode($response, TRUE);

    return $data_array;

}


function collect_metadata($item=array()){
    $metadata = array(
        'title' => preg_replace('/\s*\([^)]*\)/', '', $item['name']),
        'artists' => []
    );
    foreach($item['artists'] as $artist) {
        array_push($metadata['artists'], $artist['name']);
    }
    return $metadata;
}
<?php


function download_with_metadata($url) {
    $url = escapeshellcmd($url);
    $command = `youtube-dl -o "/songs/temp/%(title)s.%(ext)s" --format bestaudio --add-metadata {$url}`;
    exec($command, $output);
    return $output;
}


function download_with_different_metadata($url, $metadata) {
    $url = escapeshellcmd($url);
    $metadata['artists'] = str_replace(",", ";", $metadata['artists']);
    $command = `youtube-dl -o "/songs/{$metadata['filename']}.wav" --format bestaudio {$url} && ffmpeg -i "/songs/{$metadata['filename']}.wav" -codec:a libmp3lame -qscale:a 5 -metadata title="{$metadata['title']}" -metadata artist="{$metadata['artists']}" -y "/songs/{$metadata['filename']}.mp3" && rm "/songs/{$metadata['filename']}.wav"`;
    exec($command, $output, $return_val);
    if(strpos($command, "[download]") == false) {
        return false;
    } else {
        return true;
    }
}
<?php

session_start();

if($_FILES["upfile"]){
    $filename = $_SESSION['metadata']['filename'];
    $_SESSION['metadata']['artists'] = array_map("trim", explode(", ", $_SESSION['metadata']['artists']));
    $target_dir = "/songs/";
    $target_file = $target_dir . $filename . ".mp3";

    try {
    
        if (
            !isset($_FILES['upfile']['error']) ||
            is_array($_FILES['upfile']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }


        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }


        if ($_FILES['upfile']['size'] > 20000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }


        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($_FILES['upfile']['tmp_name']),
            array(
                'mp3' => 'audio/mpeg'
            ),
            true
        )) {
            throw new RuntimeException('Invalid file format.');
        }
        if (!move_uploaded_file(
            $_FILES['upfile']['tmp_name'],
            $target_file
            )
        ) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        echo 'File is uploaded successfully.';

    } catch (RuntimeException $e) {
        echo $e->getMessage();
    }
}

?>

<input type="button" id="add-db-btn" onclick="submitForm(this.id)" value="Add to database">
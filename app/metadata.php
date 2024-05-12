<!DOCTYPE html>
<html>

    <head>
        <title>MusicManager</title>
    </head>

    <body>
        
        <h2 class="heading">At First Gather Metadata</h2>

        <form id="metadata">
            <label for="title">Title</label>
            <input type="text" id="title" name="title">
            <br>
            <label for="artist">Artist</label>
            <input type="text" id="artist" name="artist">
            <input type="button" id="next-btn" onclick="submitForm(this.id)" value="Next">
        </form>

        <form id="get-metadata">
                <input type="text" name="keyword" placeholder="Song title here">
                <input type="button" id="spot-btn" onclick="getMetadata()" value="Get from Spotify"/>
        </form>

    </body>

</html>
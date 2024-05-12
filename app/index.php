<!DOCTYPE html>
<html>

    <head>
        <title>MusicManager</title>
        <script src="https://code.jquery.com/jquery-3.5.0.js" integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc=" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css" href="src/css/style.css">
    </head>

    <body>
    <main>

        <form id="login">
            <h4>Log in</h4>
            <br>
            <input type="text" id="user" name="user" placeholder="Username...">
            <br>
            <input type="text" id="pwd" name="pwd" placeholder="Password...">
            <br>
            <input type="button" value="Log in" onclick="login()">
        </form>

        <a id="showSignUp">Sign up</a>

        <form id="signup">
            <h4>Sign up</h4>
            <br>
            <input type="text" id="username" name="username" placeholder="Username...">
            <br>
            <input type="text" id="password" name="password" placeholder="Password...">
            <br>
            <input type="text" id="password-confirm" name="password-confirm" placeholder="Confirm...">
            <br>
            <input type="button" value="Sign up" onclick="signup()">
        </form>

    </main>

    </body>
    
    <script src="src/js/login.js"></script>

</html>
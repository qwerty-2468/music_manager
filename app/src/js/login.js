(function(){
    $("#signup").hide();
}())

$("#showSignUp").on("click", function() {
    $("#showSignUp").hide();
    $("#signup").show();
})

function login() {
    user = $("#user").val();
    pwd = $("#pwd").val();
    if (!user) {
        alert("No Username!");
        return;
    } else {
        if (!pwd)  {
            alert("No Password!");
            return;
        } else {
            $.post("src/login.php", {
                "function": "login",
                "user": user,
                "pwd": pwd
            }, function(response) {
                console.log(response);
                if (true) {
                    return
                } else {
                    
                }
            })
        }
    }
}

function signup() {
    user = $("#username").val();
    pwd = $("#password").val();
    pwdc = $("#password-confirm").val();
    if (!user) {
        alert("No Username!");
        return;
    } else if (user.length > 30) {
        alert("Username must be under 30 characters!");
        return;
    } else if (!pwd)  {
        alert("No Password!");
        return;
    } else if (pwd.length < 7) {
        alert("Password must be at least 7 characters!");
        return;
    } else if (pwd != pwdc) {
        alert("Passwords don't match!");
        return;
    } else {
        $.post("src/login.php", {
            "function": "signup",
            "user": user,
            "pwd": pwd
        }, function(response) {
            console.log(response);
            if (response == 0) {
                alert("Username is already taken!");
            } else {
                alert("Successfully signed up!");
                location.reload();
            }
        })
    }
}
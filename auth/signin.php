<?php

// Include the database connection file
require_once '../pdo.php';

if(isset($_POST["submit"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if (authUser($username, $password)) {
        header("Location: ../index.php");
    } else {
        echo "Incorrect credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="./style.css" />
</head>
<body>
    <div class="login-field">
        <div class="login-background">
            <div class="login-title">
                <span>Sign In</span>
            </div>

            <div class="login-form">
                <form method="post">
                    <div class="field username-field">
                        <input type="text" id="username" name="username" placeholder="Username">
                    </div>
                    <div class="field password-field">
                        <input type="password" id="password" name="password" placeholder="Password">
                    </div>
                    <div class="field button-field">
                        <input type="submit" class="button button-login" name="submit" value="Sign In">
                        <button class="button button-register"><a href="signup.php">Sign Up</a></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <span class="square square-tl"></span>
    <span class="square square-tr"></span>
    <span class="square square-bl"></span>
    <span class="square square-br"></span>
    <span class="star star1"></span>
    <span class="star star2"></span>
</body>

</html>
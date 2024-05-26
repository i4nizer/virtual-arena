<?php
// Check if Logged In
session_start();
if(isset($_SESSION["user_id"]) && isset($_SESSION["username"])) header("Location: user/dashboard/index.php");
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    
    <header>
        <div class="logo">
            <h2>Virtual Arena</h2>
        </div>
        <nav>
            
        </nav>
        <div class="auth-box">
            <a href="auth/signin.php" class="auth-btn">Sign In</a>
            <a href="auth/signup.php" class="auth-btn">Sign Up</a>
        </div>
    </header>



    <main>
        <div class="content-box">
            <div class="welcome-box box">
                <h1>Welcome to Our Website!</h1>
                <p>We're glad to have you here. Explore our content and discover amazing things.</p>
                <a href="auth/signup.php" class="btn">Sign Up</a>
            </div>
        </div>
    </main>




</body>
</html>

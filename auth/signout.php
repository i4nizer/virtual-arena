<?php
// Remove session
session_start();
$_SESSION = [];
session_destroy();

// Redirect to signin
header("Location: signin.php");
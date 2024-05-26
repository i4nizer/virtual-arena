<?php
require "pdo.php";





// Function to add a new user
function createUser($username, $email, $password) {
    global $pdo;    

    // Prepare & Exec
    $sql = "INSERT INTO va_user (name, email, psk) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($username, $email, hash('sha256', $password)));

    // Return if the data is inserted successfully
    return $stmt->rowCount() > 0;
}

// Function to authenticate user (session saved)
function authUser($username, $password) {
    global $pdo;

    // Prepare & Exec
    $sql = "SELECT * FROM va_user WHERE name = ? AND psk = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($username, hash('sha256', $password)));

    // Fetch user
    $user = $stmt->fetch();

    // Check if user exists
    if ($user) {
        // Start session
        session_start();

        // Store user information in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name'];

        return true;
    }
    return false;
}
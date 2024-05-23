<?php
// connections credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "virtual_arena";
$dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";

// optional set of attributes to set
$options = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false
);

// connection string
$pdo = new PDO($dsn, $username, $password, $options);



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
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if ($user) {
        // Start session
        session_start();

        // Store user information in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        return true;
    }
    return false;
}



// Insert Tourna
function createTourna($title, $format, $maxEntry, $maxEntryPlayer, $pairing, $public, $open, $desc, $creatorId) {
    GLOBAL $pdo;

    // Prepare SQL 
    $sql = "INSERT INTO tournament (title, format,  max_entry, max_entry_player, pairing, is_public, is_open, description, creator_id) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // Supply param and execute
    $stmt->execute(array($title, $format, $maxEntry, $maxEntryPlayer, $pairing, $public, $open, $desc, $creatorId));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Get Specific Tourna
function getTourna($userId, $tournaId) {
    GLOBAL $pdo;

    // Prepare & Exec
    $sql = "SELECT * FROM tournament WHERE creator_id = ? AND id = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($userId, $tournaId));

    // Fetch tourna
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get Tourna (assoc)
function getTournas($userId) {
    GLOBAL $pdo;

    // Prepare & Exec
    $sql = "SELECT * FROM tournament WHERE creator_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($userId));

    // Fetch tournas
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Usually used in homepage
function updateTourna($id, $title, $format, $maxEntry, $maxEntryPlayer, $pairing, $public, $open, $desc, $creatorId) {
    GLOBAL $pdo;

    // Prepare SQL 
    $sql = "UPDATE tournament SET title = ?, format = ?, max_entry = ?, max_entry_player = ?, pairing = ?, is_public = ?, is_open = ?, description = ?, creator_id = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    // Supply param and execute
    $stmt->execute(array($title, $format, $maxEntry, $maxEntryPlayer, $pairing, $public, $open, $desc, $creatorId, $id));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Referred by Player
function createTeam($name, $tournaId) {
    GLOBAL $pdo;
    
    // Prepare SQL
    $sql = "INSERT INTO team (name, tourna_id) VALUES(?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // Supply param and execute
    $stmt->execute(array($name, $tournaId));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Based on pairing - random
function createMatch($tournaId, $startDT, $refId, $p1Id, $p2Id) {
    GLOBAL $pdo;
    
    // Prepare SQL
    $sql = "INSERT INTO `match` (tourna_id, start_dt, ref_id, p1_id, p2_id) VALUES(?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // Supply param and execute
    $stmt->execute(array($tournaId, $startDT, $refId, $p1Id, $p2Id));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Based on match
function createResult($matchId, $duration, $winnerId, $loserId, $conclusion) {
    GLOBAL $pdo;

    // Prepare SQL
    $sql = "INSERT INTO result (match_id, duration, winner_id, loser_id, conclusion) VALUES(?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // Supply param and execute
    $stmt->execute(array($tournaId, $startDT, $refId, $p1Id, $p2Id));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}
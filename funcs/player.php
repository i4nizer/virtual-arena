<?php
require "pdo.php";





// No team, no player //
function createPlayer($name, $email, $contactNo, $teamId) {
    GLOBAL $pdo;

    // Get tourna timezone
    $sql = "SELECT tourna.timezone FROM tourna INNER JOIN team ON team.tourna_id = tourna.id WHERE team.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($teamId));
    $timezone = $stmt->fetch(PDO::FETCH_COLUMN);
    $creationDt = (new DateTime('now', new DateTimeZone($timezone)))->format("Y-m-d H:i:s");
    
    // Supply param and execute
    $sql = "INSERT INTO player (name, email, contact_no, score, wins, loses, creation_dt, team_id) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($name, $email, $contactNo, 0, 0, 0, $creationDt, $teamId));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Get Specific Player
function getPlayer($id) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT * FROM player WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($id));
    
    // Fetch player
    return $stmt->fetch();
}

// Get All Players of team (assoc -> name) //
function getPlayers($teamId, $cols = "*") {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT $cols FROM player WHERE team_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($teamId));
    
    // Fetch players
    return $stmt->fetchAll();
}

// Get all players of the Tourna (array of assocs) //
function getAllPlayers($tournaId) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT player.*, team.id AS team_id, team.name AS team_name FROM player 
        INNER JOIN team ON team.id = player.team_id WHERE team.tourna_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    
    // Fetch players
    return $stmt->fetchAll();
}

// Update specific player
function updatePlayer($id, $name, $email, $contactNo, $score, $wins, $loses) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "UPDATE player SET name = ?, email = ?, contact_no = ?, score = ?, wins = ?, loses = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($name, $email, $contactNo, $score, $wins, $loses, $id));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Update specific player info part //
function updatePlayerInfo($id, $name, $email, $contactNo) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "UPDATE player SET name = ?, email = ?, contact_no = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($name, $email, $contactNo, $id));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Update specific player stats part //
function updatePlayerStats($id, $score, $wins, $loses) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "UPDATE player SET score = ?, wins = ?, loses = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($score, $wins, $loses, $id));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Remove specific player //
function deletePlayer($id) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "DELETE FROM player WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($id));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}
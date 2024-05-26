<?php
require "pdo.php";





// No team, no player
function createPlayer($name, $email, $contactNo, $teamId) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "INSERT INTO player (name, email, contact_no, score, wins, loses, team_id) VALUES(?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($name, $email, $contactNo, 0, 0, 0, $teamId));
    
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

// Get All Players with team name mixed with team_name & team_id (assoc)
function getPlayers($teamId) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT player.*, team.id AS team_id, team.name AS team_name FROM player INNER JOIN team ON team.id = player.team_id WHERE player.team_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($teamId));
    
    // Fetch players
    return $stmt->fetchAll();
}

// Get all players of the Tourna (array of assocs)
function getAllPlayers($tournaId) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT player.*, team.name AS team_name FROM player INNER JOIN team ON team.id = player.team_id WHERE team.tourna_id = ?";
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

// Remove specific player
function removePlayer($id) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "DELETE FROM player WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($id));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}
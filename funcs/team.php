<?php
require "pdo.php";





// Referred by Player
function createTeam($name, $tournaId) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "INSERT INTO team (name, tourna_id) VALUES(?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($name, $tournaId));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Get specific team
function getTeam($teamId) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT * FROM team WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($teamId));
    
    // Fetch team
    return $stmt->fetch();
}

// Get teams (assoc)
function getTeams($tournaId) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT * FROM team WHERE tourna_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    
    // Fetch teams
    return $stmt->fetchAll();
}

// Remove specific team
function removeTeam($id) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "DELETE FROM team WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($id));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}
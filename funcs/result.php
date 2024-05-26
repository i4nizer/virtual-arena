<?php
require "pdo.php";





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
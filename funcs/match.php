<?php
require "pdo.php";





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

// Get Matchlist - Selects with winner & loser id but leaves null if no result yet //
function getMatchList($roundId) {
    GLOBAL $pdo;

    // Pre & Exec
    $sql = "SELECT r.winner_id, tw.name AS winner_team, r.loser_id, tl.name AS loser_team, mt.* FROM (
        SELECT m.* , t1.id AS team1_id, t1.name AS team1_name, t2.id AS team2_id, t2.name AS team2_name
            FROM `match` m
            INNER JOIN team t1 ON m.p1_id = t1.id
            INNER JOIN team t2 ON m.p2_id = t2.id
            INNER JOIN round r ON r.id = m.round_id
            WHERE m.round_id = ?
        ) AS mt
        LEFT JOIN result r ON mt.id = r.match_id
        LEFT JOIN team tw ON tw.id = r.winner_id
        LEFT JOIN team tl ON tl.id = r.loser_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($roundId));

    return $stmt->fetchAll();
}

// Update Match were col by col //
function updateMatchCol($matchId, $col, $val) {
    GLOBAL $pdo;
    
    // Prep & Exec
    $sql = "UPDATE `match` SET $col = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($val, $matchId));

    return $stmt->rowCount() > 0;
}
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
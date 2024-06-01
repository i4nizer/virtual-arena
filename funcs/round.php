<?php
require "pdo.php";



// Get rounds of a tourna //
function getRounds($tournaId) {
    GLOBAL $pdo;

    $sql = "SELECT round.id, ROW_NUMBER() OVER (ORDER BY round.id ASC) AS `number`, round.start_dt, round.end_dt FROM round
        INNER JOIN tourna ON tourna.id = round.tourna_id 
        WHERE tourna.id = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));

    return $stmt->fetchAll();
}
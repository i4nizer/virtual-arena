<?php
require "../../funcs/pdo.php";



if(!isset($_GET["req"])) exit("Add 'req' arg so we know what is requested.");
if(!isset($_GET["tourna_id"])) exit("Provide tournament id to get stats from.");



$req = $_GET["req"];
$tournaId = $_GET["tourna_id"];

if($req == "team-player-dt") {
    // Get creation_dt of team
    $sql = "SELECT CONVERT(team.creation_dt, DATETIME) AS team_cdt, COUNT(*) AS team_count 
        FROM team WHERE team.tourna_id = ? GROUP BY CONVERT(team.creation_dt, DATE)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    $teamCountPerDT = $stmt->fetchAll();

    // Get creation_dt of player
    $sql = "SELECT CONVERT(player.creation_dt, DATETIME) AS player_cdt, COUNT(*) AS player_count 
        FROM player INNER JOIN team ON team.id = player.team_id
        WHERE team.tourna_id = ? GROUP BY CONVERT(player.creation_dt, DATE)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    $playerCountPerDT = $stmt->fetchAll();

    // Set Assoc Array and send
    $stats = array();
    $stats["team"] = $teamCountPerDT;
    $stats["player"] = $playerCountPerDT;

    echo json_encode($stats);
}
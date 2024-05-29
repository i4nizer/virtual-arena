<?php
require "pdo.php";





// Get Timezone & Pairing //
function getTournaTimezonePairing($tournaId) {
    GLOBAL $pdo;
    
    // Get tz n pairing type
    $sql = "SELECT tourna.timezone, tourna_setup.pairing FROM tourna 
        INNER JOIN tourna_setup ON tourna_setup.tourna_id = tourna.id 
        WHERE tourna.id = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    
    return $stmt->fetch();
}

// Pair teams //
function pairTeams($teams, $pairing) {
    $pairs = [];
    $count = count($teams);

    // if random pairing
    if($pairing == "Random") shuffle($teams);

    // If the number of teams is odd, one team gets a bye
    if ($count % 2 == 1) {
        $byeTeam = array_shift($teams);
        $pairs[] = [$byeTeam, null]; // null indicates a bye
    }

    // Pair the remaining teams
    while (count($teams) > 1) {
        $team1 = array_shift($teams);
        $team2 = array_shift($teams);
        $pairs[] = [$team1, $team2];
    }

    // If there's one team left without a pair, it gets a bye
    if (count($teams) == 1) {
        $pairs[] = [array_shift($teams), null];
    }

    return $pairs;
}

// Override all matches of the tourna //
function createNewMatchList($tournaId, $pairs) {
    GLOBAL $pdo;

    // Delete existing rounds and matches of the tourna
    $sql = "DELETE FROM round WHERE tourna_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));

    // Create new Round
    $sql = "INSERT INTO `round` (tourna_id) VALUES(?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    $roundId = $pdo->lastInsertId();

    // Insert Matches
    $sql = "INSERT INTO `match` (p1_id, p2_id, round_id) VALUES ";

    // Add Values
    $noPairId = NULL;
    $pLen = count($pairs);
    foreach($pairs as $i => $pair) {
        $p1Id = ($pair[0])["id"];
        if($pair[1] == NULL) {
            $noPairId = $p1Id;
            continue;
        }
        
        $p2Id = ($pair[1])["id"];
        $sql .= "($p1Id, $p2Id, $roundId)".($i < $pLen - 1 ? ",":"");
    }
    
    echo $sql;  
    // Move the no pair to the next round
    if($noPairId != NULL) {
        $pdo->exec("UPDATE team SET round = CASE WHEN id = $noPairId THEN 2 ELSE 1 END WHERE tourna_id = $tournaId ");
    }

    // Exec
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}





// Get all teams (assoc) //
function getTeams($tournaId, $cols = "*") {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT $cols FROM team WHERE tourna_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    
    // Fetch teams
    return $stmt->fetchAll();
}

// Referred by Player -> rerumbles the matches based on pairing //
function createTeam($name, $tournaId) {
    GLOBAL $pdo;

    // Get tourna timezone & pairing
    $tournaTzP = getTournaTimezonePairing($tournaId);
    $creationDt = (new DateTime('now', new DateTimeZone($tournaTzP["timezone"])))->format("Y-m-d H:i:s");
    
    // Supply param and execute
    $sql = "INSERT INTO team (name, round, creation_dt, tourna_id) VALUES(?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($name, 0, $creationDt, $tournaId));
    $teamCreated = $stmt->rowCount() > 0;
    
    // Get Teams id
    $teams = getTeams($tournaId, "id");
    
    // Pair Teams
    $pairs = pairTeams($teams, $tournaTzP["pairing"]);
    
    // Create New Matchlist
    $matched = createNewMatchList($tournaId, $pairs);

    // Boolean state return Success
    return $teamCreated && $matched;
}

// Get specific team //
function getTeam($teamId, $cols = "*") {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT $cols FROM team WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($teamId));
    
    // Fetch team
    return $stmt->fetch();
}

// Get all teams with total wins from Players //
function getTeamsWithCount($tournaId) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT team.*, COALESCE(p.score, 0) AS score, COALESCE(p.wins, 0) AS wins, COALESCE(p.loses, 0) AS loses 
        FROM team LEFT JOIN (
            SELECT player.team_id, SUM(player.score) AS score, SUM(player.wins) AS wins, SUM(player.loses) AS loses
            FROM player GROUP BY player.team_id
        ) AS p ON team.id = p.team_id
        WHERE team.tourna_id = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    
    // Fetch teams
    return $stmt->fetchAll();
}

// Update Team //
function updateTeam($id, $name, $round = 0) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "UPDATE team SET name = ?, round = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($name, $round, $id));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}


// Remove specific team
function deleteTeam($id) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "DELETE FROM team WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($id));
    
    // Boolean state return Success
    return $stmt->rowCount() > 0;
}
<?php
require "pdo.php";





// Insert Tourna
function createTourna($title, $timezone, $format, $maxEntry, $maxEntryPlayer, $start_dt, $end_dt, $pairing, $public, $open, $desc, $creatorId) {
    GLOBAL $pdo;

    // Prepare SQL 
    $sql = "INSERT INTO tournament (title, timezone, format,  max_entry, max_entry_player, start_dt, end_dt, pairing, is_public, is_open, description, creator_id) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // Supply param and execute
    $stmt->execute(array($title, $timezone, $format, $maxEntry, $maxEntryPlayer, $start_dt, $end_dt, $pairing, $public, $open, $desc, $creatorId));

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
    return $stmt->fetch();
}

// Get Tourna (assoc -> title, id)
function getTournas($userId, $cols) {
    GLOBAL $pdo;

    // Prepare & Exec
    $sql = "SELECT $cols FROM tournament WHERE creator_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($userId));

    // Fetch tournas
    return $stmt->fetchAll();
}

// Usually used in homepage prep
function updateTourna($id, $title, $timezone, $format, $maxEntry, $maxEntryPlayer, $start_dt, $end_dt, $pairing, $public, $open, $desc, $creatorId) {
    GLOBAL $pdo;

    // Prepare SQL 
    $sql = "UPDATE tournament SET title = ?, timezone = ?, format = ?, max_entry = ?, max_entry_player = ?, start_dt = ?, end_dt = ?, pairing = ?, is_public = ?, is_open = ?, description = ?, creator_id = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    // Supply param and execute
    $stmt->execute(array($title, $timezone, $format, $maxEntry, $maxEntryPlayer, $start_dt, $end_dt, $pairing, $public, $open, $desc, $creatorId, $id));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Use when ongoing
function updateTournaOngoing($id, $desc, $end_dt, $public) {
    GLOBAL $pdo;

    // Prepare SQL 
    $sql = "UPDATE tournament SET description = ?, end_dt = ?, is_public = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    // Supply param and execute
    $stmt->execute(array($desc, $end_dt, $public, $id));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Use when done
function updateTournaEnded($id, $desc, $public) {
    GLOBAL $pdo;

    // Prepare SQL 
    $sql = "UPDATE tournament SET description = ?, is_public = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    // Supply param and execute
    $stmt->execute(array($desc, $public, $id));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
}




// Util -> str params
function getTimeStatus($startDtStr, $endDtStr, $timezone) {
    // Convert to time
    $startDt = new DateTime($startDtStr, new DateTimeZone($timezone));
    $endDt = new DateTime($endDtStr, new DateTimeZone($timezone));
    $currentDt = new DateTime('now', new DateTimeZone($timezone));

    if($currentDt->diff($startDt)->invert) return "Ended";        // After
    else if($currentDt->diff($endDt)->invert) return "Ongoing";   // During
    else return "Preparation";                                    // Before
}

// Count teams of tourna
function getTeamCount($tournaId) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT COUNT(id) FROM team WHERE tourna_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    
    // Fetch team count
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

// Count players of tourna
function getPlayerCount($tournaId) {
    GLOBAL $pdo;
    
    // Supply param and execute
    $sql = "SELECT COUNT(player.id) FROM team INNER JOIN player ON player.team_id = team.id WHERE team.tourna_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    
    // Fetch player count
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

// Get All ongoing matches (team1, team2)
function getOngoingMatches($tournaId, $tournaTimezone) {
    GLOBAL $pdo;

    // Current Time of tourna
    $tournaDt = new DateTime("now", new DateTimeZone($tournaTimezone));

    // Get ongoing matches
    $sql = "SELECT t1.name AS team1, t2.name AS team2 FROM `match` m
        INNER JOIN team t1 ON m.p1_id = t1.id
        INNER JOIN team t2 ON m.p2_id = t2.id 
        WHERE t1.tourna_id = ? AND t2.tourna_id = ? AND ? BETWEEN m.start_dt AND m.end_dt;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId, $tournaId, $tournaTimezone));
    
    // Fetch all matches
    return $stmt->fetchAll();
}

// Get Top Team
function getTopTeams($tournaId, $limit) {
    GLOBAL $pdo;

    // Prepare
    // $sql = "SELECT ";
    // $sql = "SELECT player.team_id, player.wins FROM player WHERE player.team_id = team.id AND team.tourna_id = ? GROUP BY player.team_id";
    // $sql = "SELECT team.name, SUM(player.wins) AS wins FROM team 
    //     INNER JOIN player ON player.team_id = team.id
    //     WHERE team.tourna_id = ? ORDER BY wins ASC LIMIT $limit";
    $sql = "SELECT name FROM team WHERE tourna_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));

    // Fetch top5 teams
    return $stmt->fetchAll();
}

// Get Top Team with its top scoring player
function getTopTeamsTopPlayer($tournaId) {
    GLOBAL $pdo;

    $sql = "WITH TeamWins AS (
            SELECT p.team_id, SUM(p.wins) AS total_wins FROM player p 
            INNER JOIN team t ON t.id = p.team_id 
            WHERE t.tourna_id = ? GROUP BY p.team_id
        ),
        TopTeams AS (
            SELECT tw.team_id, tw.total_wins, t.name AS team_name, ROW_NUMBER() OVER (ORDER BY tw.total_wins DESC) AS rank FROM TeamWins tw
            INNER JOIN team t ON tw.team_id = t.id
            WHERE tw.total_wins IS NOT NULL
            ORDER BY tw.total_wins DESC LIMIT 5
        ),
        TopPlayers AS (
            SELECT p.id AS player_id, p.name AS player_name, p.score, p.team_id FROM player p
            INNER JOIN TopTeams tt ON p.team_id = tt.team_id
        ),
        MaxScorePlayers AS ( 
            SELECT tp.team_id, MAX(tp.score) AS max_score FROM TopPlayers tp GROUP BY tp.team_id 
        )
        SELECT tt.team_id, tt.team_name, tt.total_wins, tp.player_id, tp.player_name, tp.score FROM TopTeams tt
        INNER JOIN TopPlayers tp ON tt.team_id = tp.team_id
        INNER JOIN MaxScorePlayers msp ON tp.team_id = msp.team_id AND tp.score = msp.max_score
        ORDER BY tt.total_wins DESC, tp.score DESC;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));
    
    // Fetch top5 teams
    return $stmt->fetchAll();
}
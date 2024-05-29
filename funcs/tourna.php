<?php
require "pdo.php";





// Setup tourna //
function createTournaSetup($tournaId, $format, $maxEntry, $maxEntryPlayer, $pairing, $public, $open) {
    GLOBAL $pdo;

    // Supply param and execute
    $sql = "INSERT INTO tourna_setup (format, max_entry, max_entry_player, pairing, is_public, is_open, tourna_id) VALUES(?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($format, $maxEntry, $maxEntryPlayer, $pairing, $public, $open, $tournaId));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Get Setup //
function getTournaSetup($tournaId, $cols = "*") {
    GLOBAL $pdo;

    // Supply param and execute
    $sql = "SELECT $cols FROM tourna_setup WHERE tourna_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Change Setup //
function updateTournaSetup($tournaId, $format, $maxEntry, $maxEntryPlayer, $pairing, $public, $open) {
    GLOBAL $pdo;

    // Supply param and execute
    $sql = "UPDATE tourna_setup SET format = ?, max_entry = ?, max_entry_player = ?, pairing = ?, is_public = ?, is_open = ? WHERE tourna_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($format, $maxEntry, $maxEntryPlayer, $pairing, $public, $open, $tournaId));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Insert Tourna //
function createTourna($title, $timezone, $start_dt, $end_dt, $desc, $creatorId) {
    GLOBAL $pdo;

    // Supply param and execute
    $sql = "INSERT INTO tourna (title, timezone, start_dt, end_dt, description, creator_id) VALUES(?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($title, $timezone, $start_dt, $end_dt, $desc, $creatorId));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Get Tourna w/setup //
function getTournaWithSetup($tournaId) {
    GLOBAL $pdo;

    // Prepare & Exec
    $sql = "SELECT tourna.*, tourna_setup.* FROM tourna
        INNER JOIN tourna_setup ON tourna_setup.tourna_id = tourna.id
        WHERE tourna.id = ? LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));

    // Fetch tourna
    return $stmt->fetch();
}

// Get Tourna w/counts (Dashboard) //
function getTournaWithCount($tournaId) {
    GLOBAL $pdo;

    // Prepare & Exec
    $sql = "SELECT
            tourna.*,
            (SELECT COUNT(*) FROM round WHERE round.tourna_id = tourna.id) AS round_count,
            (SELECT COUNT(*) FROM `match` INNER JOIN round ON `match`.round_id = round.id WHERE round.tourna_id = tourna.id) AS match_count,
            (SELECT COUNT(*) FROM team WHERE team.tourna_id = tourna.id) AS team_count,
            (SELECT COUNT(*) FROM player INNER JOIN team ON player.team_id = team.id WHERE team.tourna_id = tourna.id) AS player_count
        FROM tourna WHERE tourna.id = ? LIMIT 1;
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));

    // Fetch tourna
    return $stmt->fetch();
}

// Get Specific Tourna (most used)
function getTourna($tournaId) {
    GLOBAL $pdo;

    // Prepare & Exec
    $sql = "SELECT * FROM tourna WHERE id = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($tournaId));

    // Fetch tourna
    return $stmt->fetch();
}

// Get Tourna (assoc -> title, id) //
function getTournas($userId, $cols = "*") {
    GLOBAL $pdo;

    // Prepare & Exec
    $sql = "SELECT $cols FROM tourna WHERE creator_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($userId));

    // Fetch tournas
    return $stmt->fetchAll();
}

// Usually used in homepage prep //
function updateTourna($id, $title, $timezone, $start_dt, $end_dt, $desc) {
    GLOBAL $pdo;

    // Prepare SQL 
    $sql = "UPDATE tourna SET title = ?, timezone = ?, start_dt = ?, end_dt = ?, description = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    // Supply param and execute
    $stmt->execute(array($title, $timezone, $start_dt, $end_dt, $desc, $id));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
}

// Update prep tourna
function deleteTourna($id) {
    GLOBAL $pdo;

    // Prepare and Execute
    $sql = "DELETE FROM tourna WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($id));

    return $stmt->rowCount() > 0;
}

// Use when ongoing
function updateTournaOngoing($id, $desc, $end_dt, $public) {
    GLOBAL $pdo;

    // Prepare SQL 
    $sql = "UPDATE tourna SET description = ?, end_dt = ?, is_public = ? WHERE id = ?";
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
    $sql = "UPDATE tourna SET description = ?, is_public = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    // Supply param and execute
    $stmt->execute(array($desc, $public, $id));

    // Boolean state return Success
    return $stmt->rowCount() > 0;
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
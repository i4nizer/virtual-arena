<?php
require '../../funcs/match.php';
require '../../funcs/team.php';

// Check if Logged In
session_start();
if(!isset($_SESSION["user_id"])) header("Location: ../../auth/signin.php");

$userId = $_SESSION["user_id"];
$userName = $_SESSION["username"];

// Allow post only
if($_SERVER["REQUEST_METHOD"] != "POST") header("Location: index.php");

$msg = "No Action";
$msgState = "";
$tournaId = $_POST["tourna_id"];
$roundId = $_POST["round_id"];

// Check match_id
if(isset($_POST["match_id"])) {
    $matchId = $_POST["match_id"];
    $tournaTzP = getTournaTimezonePairing($tournaId);
    $timezone = $tournaTzP["timezone"];
    $pairing = $tournaTzP["pairing"];
    $currentDt = new DateTime('now', new DateTimeZone($timezone));
    $updated = false;

    // Check if there is existing result with such match_id
    $sql = "SELECT id FROM result WHERE match_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($matchId));
    $matchHasResult = $stmt->rowCount() > 0;

    // Update
    if($matchHasResult) {
        $winnerID = $_POST["winner_id"];
        $loserID = $_POST["loser_id"];

        $sql = "UPDATE result SET winner_id = ?, loser_id = ? WHERE match_id = ?";
    }
    // Insert
    else {
        $winnerID = $_POST["winner_id"];
        $loserID = $_POST["loser_id"];

        $sql = "INSERT INTO result (winner_id, loser_id, match_id) VALUES (?, ?, ?)";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($winnerID, $loserID, $matchId));
    $updated = $stmt->rowCount() > 0;
    $msg = $updated? "Match result updated successfully.": "An error occurred, failed to update match result.";

    if($updated) {
        // Check if there are matches that doesn't have results yet
        $sql = "SELECT m.id, m.p1_id, m.p2_id, m.start_dt, m.end_dt, m.round_id
            FROM `match` m
            LEFT JOIN result r ON m.id = r.match_id
            WHERE r.match_id IS NULL AND m.round_id = ?;
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($roundId));
        $matches = $stmt->fetchAll();
        $roundDone = empty($matches) || $matches == false || $matches == NULL;

        // If there wasn't, create next round then and ascend winners
        if($roundDone) {
            // Get Winning Teams of the last round
            $sql = "SELECT re.winner_id AS id FROM result re
                INNER JOIN `match` m ON m.id = re.match_id
                WHERE m.round_id = ?
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array($roundId));
            $winnerTeams = $stmt->fetchAll();

            // Check how many teams
            if(count($winnerTeams) <= 0 || empty($winnerTeams) || $winnerTeams == NULL) {
                // Win!
            }
            else {
                // Get what round it is
                $sql = "SELECT COUNT(id) as rounds FROM round WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array($roundId));
                $roundNumber = $stmt->fetch(PDO::FETCH_COLUMN);

                // Ascend teams
                $sql = "UPDATE team t
                    JOIN (
                        SELECT r.winner_id, m.round_id
                        FROM result r
                        JOIN `match` m ON r.match_id = m.id
                        WHERE m.round_id = ?
                    ) AS wt
                    ON t.id = wt.winner_id
                    SET t.round = t.round + 1
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array($roundId));
                $updated = $stmt->rowCount() > 0;
                $msg .= " Winning teams ascended to the next round.";
                
                // Create New Matchlist for the new round
                $pairs = pairTeams($winnerTeams, $pairing);
                
                // Create New Matchlist
                $updated = createNewMatchList($tournaId, $currentDt->format('Y-m-d H:i:s'), $pairs, false);
                $msg .= " New matchlist added to the round.";
            }

        }
    }

    $msgState = $updated? "success": "failed";
}

// Back
$redirect = isset($_POST["redirect"])? "Location: ".$_POST["redirect"] : "Location: index.php?tourna_id=$tournaId&round_id=$roundId";
header($redirect."&msg=$msg&msg_state=$msgState");
<?php
require '../../funcs/match.php';

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
    $updated = false;
    
    // Updating match
    if(isset($_POST["start_dt"])) {
        $updated = updateMatchCol($matchId, "start_dt", $_POST["start_dt"]);
    }
    else if(isset($_POST["end_dt"])) {
        $updated = updateMatchCol($matchId, "end_dt", $_POST["end_dt"]);
    }
    else {
        $updated = updateMatchCol($matchId, "auto_end", isset($_POST["auto_end"]));
    }

    $msg = $updated? "Match updated successfully.": "An error occurred, failed to update match.";
    $msgState = $updated? "success": "failed";
}

// Back
$redirect = isset($_POST["redirect"])? "Location: ".$_POST["redirect"] : "Location: index.php?tourna_id=$tournaId&round_id=$roundId";
header($redirect."&msg=$msg&msg_state=$msgState");
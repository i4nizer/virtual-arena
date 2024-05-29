<?php
require '../../funcs/player.php';

// Check if Logged In
session_start();
if(!isset($_SESSION["user_id"])) header("Location: ../../auth/signin.php");

$userId = $_SESSION["user_id"];
$userName = $_SESSION["username"];

$msg = "No Action";
$msgState = "";
$teamId = $_POST["team_id"];
$tournaId = $_POST["tourna_id"];

// Allow post only
if($_SERVER["REQUEST_METHOD"] != "POST") header("Location: index.php?tourna_id=$tournaId&team_id=$teamId");

// Updates Player
if(isset($_POST["edit_player"])) {
    $updated = updatePlayerInfo($_POST["player_id"], $_POST["name"], $_POST["email"], $_POST["contact_no"]);
    
    $msg = $updated? "Player edited successfully.": "An error occured, failed to edit player.";
    $msgState = $updated? "success": "failed";
}
else if(isset($_POST["update_player"])) {
    $updated = updatePlayer($_POST["player_id"], $_POST["score"], $_POST["wins"], $_POST["loses"]);
    
    $msg = $updated? "Player updated successfully.": "An error occured, failed to update player.";
    $msgState = $updated? "success": "failed";
}

// Back
$redirect = isset($_POST["redirect"])? "Location: ".$_POST["redirect"]."&msg=$msg&msg_state=$msgState" : NULL;
$redirect = $redirect ?? "Location: index.php?tourna_id=$tournaId&team_id=$teamId&msg=$msg&msg_state=$msgState";
header($redirect);
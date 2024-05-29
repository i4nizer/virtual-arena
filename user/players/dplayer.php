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

// Deletes Player
if(isset($_POST["delete_player"])) {
    $deleted = deletePlayer($_POST["player_id"]);
    
    $msg = $deleted? "Player removed successfully.": "An error occured, failed to remove player.";
    $msgState = $deleted? "success": "failed";
}

// Back
$redirect = isset($_POST["redirect"])? "Location: ".$_POST["redirect"]."&msg=$msg&msg_state=$msgState" : NULL;
$redirect = $redirect ?? "Location: index.php?tourna_id=$tournaId&team_id=$teamId&msg=$msg&msg_state=$msgState";
header($redirect);
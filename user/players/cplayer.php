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

// Creates Player
if(isset($_POST["create_player"])) {
    $created = createPlayer($_POST["name"], $_POST["email"], $_POST["contact_no"], $teamId);
        
    $msg = $created? "Player added successfully.": "An error occured, failed to add player.";
    $msgState = $created? "success": "failed";
}

// Back
$redirect = "Location: index.php?tourna_id=$tournaId&team_id=$teamId&msg=$msg&msg_state=$msgState";
header($redirect);
<?php
require '../../funcs/team.php';

// Check if Logged In
session_start();
if(!isset($_SESSION["user_id"])) header("Location: ../../auth/signin.php");

$userId = $_SESSION["user_id"];
$userName = $_SESSION["username"];

$msg = "No Action";
$msgState = "";
$tournaId = $_POST["tourna_id"];

// Allow post only
if($_SERVER["REQUEST_METHOD"] != "POST") header("Location: all.php?tourna_id=$tournaId");

// Creates Team
if(isset($_POST["create_team"])) {
    $created = createTeam($_POST["name"], $tournaId);
        
    $msg = $created? "Team added successfully.": "An error occured, failed to add team.";
    $msgState = $created? "success": "failed";
}

// Back
$redirect = "Location: index.php?tourna_id=$tournaId&msg=$msg&msg_state=$msgState";
header($redirect);
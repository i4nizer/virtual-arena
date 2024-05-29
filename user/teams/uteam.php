<?php
require '../../funcs/team.php';

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
if($_SERVER["REQUEST_METHOD"] != "POST") header("Location: all.php?tourna_id=$tourna_id");


// Creates Team
if(isset($_POST["update_team"])) {
    $round = isset($_POST["round"])? $_POST["round"] : NULL;
    $name = $_POST["name"];
    $updated = $round == NULL? updateTeam($teamId, $name) : updateTeam($teamId, $name, $round);
    
    // Success
    if($updated) {
        $msg = "Team updated successfully.";
        $msgState = "success";
    }
    // Failed
    else {
        $msg = "An error occured, failed to update team.";
        $msgState = "failed";
    }
}

// Back
$redirect = isset($_POST["redirect"])? $_POST["redirect"]."&msg=$msg&msg_state=$msgState" : "Location: index.php?tourna_id=$tournaId&team_id=$teamId&msg=$msg&msg_state=$msgState";
header($redirect);
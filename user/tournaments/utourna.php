<?php
require '../../funcs/tourna.php';

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

// Updates tournament
if(isset($_POST["update_tourna"])) {
    // Updating tourna
    $tournaUpdated = updateTourna($tournaId, $_POST["title"], $_POST["timezone"], $_POST["start_dt"], $_POST["end_dt"], $_POST["description"]);
    $setupUpdated = updateTournaSetup($tournaId, $_POST["format"], $_POST["max_entry"], $_POST["max_entry_player"], $_POST["pairing"], isset($_POST["is_public"]), isset($_POST["is_open"]));
    
    if($tournaUpdated && $setupUpdated) $msg = "Tournament updated and set.";
    else if($tournaUpdated) $msg = "Tournament updated";
    else if($setupUpdated) $msg = "Tournament setup updated.";
    else $msg = "An error occurred, failed to update tournament.";

    $msgState = $tournaUpdated && $setupUpdated? "success": "failed";
}

// Back
header("Location: index.php?tourna_id=$tournaId&msg=$msg&msg_state=$msgState");
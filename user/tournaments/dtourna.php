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

// Deletes tournament
if(isset($_POST["delete_tourna"])) {
    // Success
    if(deleteTourna($tournaId)) {
        $msg = "Tournament deleted.";
        $msgState = "success";
    }
    // Failed
    else {
        $msg = "An error occured, failed deleting tournament.";
        $msgState = "failed";
    }
}

// Back
header("Location: index.php?tourna_id=$tournaId&msg=$msg&msg_state=$msgState");
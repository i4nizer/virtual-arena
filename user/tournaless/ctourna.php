<?php
require '../../funcs/tourna.php';

// Check if Logged In
session_start();
if(!isset($_SESSION["user_id"])) header("Location: ../../auth/signin.php");

$userId = $_SESSION["user_id"];
$userName = $_SESSION["username"];

// Allow post only
if($_SERVER["REQUEST_METHOD"] != "POST") header("Location: index.php");


// Creates tournament
if(isset($_POST["create_tourna"])) {
    // Success creating tourna
    if(createTourna($_POST["title"], $_POST["timezone"], $_POST["start_dt"], $_POST["end_dt"], $_POST["description"], $_POST["creator_id"])) {
        $msg = "Tournament created";
        $msgState = "success";
        
        // Get tourna_id for setup
        $tournaId = $pdo->lastInsertId();
        if(createTournaSetup($tournaId, $_POST["format"], $_POST["max_entry"], $_POST["max_entry_player"], $_POST["pairing"], isset($_POST["is_public"]), isset($_POST["is_open"]))) {
            $msg .= " and set.";
            header("Location: ../tournaments/index.php?tourna_id=$tournaId&msg=$msg&msg_state=$msgState");
        }
        else {
            $deleted = deleteTourna($tournaId);
            $msg = $deleted? "An error occured, tournament creation failed." : "An error occured, tournament created without setup.";
            $msgState = "failed";
            header("Location: index.php?msg=$msg&msg_state=$msgState");
        }
    }
    // Failed
    else {
        $msg = "An error occured, failed creating tournament.";
        $msgState = "failed";
    }
}
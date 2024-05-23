<?php
require_once "pdo.php";

// Check if Logged In
session_start();
if(!isset($_SESSION["user_id"])) header("Location: auth/signin.php");

$userId = $_SESSION["user_id"];
$userName = $_SESSION["username"];

// Get Requested Content
$content = isset($_GET["content"])? $_GET["content"] : "dashboard";

// Notifier
$msg = "";



// Get All Tournaments
$tournas = getTournas($userId);
$tournaId = isset($_GET["tourna_id"])? $_GET["tourna_id"] : NULL;
$tournaTitle = isset($_GET["title"])? $_GET["title"] : NULL;
$selectedTourna = $tournaId != NULL ? getTourna($userId, $tournaId) : NULL;     // Get Selected Tournament




// Creates tournament
if(isset($_POST["create_tourna"])) {
    // Success creating tourna
    if(createTourna($_POST["title"], $_POST["format"], $_POST["max_entry"], $_POST["max_entry_player"], $_POST["pairing"], $_POST["is_public"], $_POST["is_open"], $_POST["description"], $_POST["creator_id"])) {
        $msg = "Tournament created";
    }
    // Failed
    else $msg = "An error occured, failed creating tournament.";
}
// Updates a Tournament
else if(isset($_POST["update_tourna"])) {
    // Success
    if(updateTourna($_POST["tourna_id"], $_POST["title"], $_POST["format"], $_POST["max_entry"], $_POST["max_entry_player"], $_POST["pairing"], isset($_POST["is_public"]), isset($_POST["is_open"]), $_POST["description"], $_POST["creator_id"])) {
        $msg = "Tournament info saved.";
    }
    // Failed
    else $msg = "An error occured, failed to save tournament info.";
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Virtual Arena</title>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="header-row">
            <div class="header-section">
                <h3>Virtual Arena</h3>
                <?php
                // Has tournas
                if($tournas) {
                    ?> <select name="tournament" id="tournament"> <?php

                    // Create options
                    foreach($tournas as $tourna) {
                        $title = $tourna["title"];
                        $id = $tourna["id"];

                        // For first load
                        $tournaId = $tournaId ?? $id;
                        if($tournaId != NULL && $selectedTourna == NULL) $selectedTourna = getTourna($userId, $tournaId);
                    ?>
                        <option value="<?php echo $id; ?>">
                            <a href="index.php?tourna_id=<?php echo $id; ?>"><?php echo $title; ?></a>
                        </option>
                    <?php }
                    ?> </select> <?php
                }
                ?>
                </select>
            </div>
            <div class="header-section">
                <h4><?php echo $userName; ?></h4>
                <a class="btn" href="auth/signout.php">Sign Out</a>
            </div>
        </div>
        <?php
        // Show navs if there are tournas & tournaID
        if($tournas) { ?>

        <div class="header-row">
            <div class="header-nav">
                <a href="index.php?content=dashboard&tourna_id=<?php echo $tournaId; ?>">Dashboard</a>
                <a href="view-tourna/index.php?tourna_id=<?php echo $tournaId; ?>" target="_blank">View</a>
                <a href="index.php?content=teams&tourna_id=<?php echo $tournaId; ?>">Teams</a>
                <a href="index.php?content=matches&tourna_id=<?php echo $tournaId; ?>">Matches</a>
                <a href="index.php?content=results&tourna_id=<?php echo $tournaId; ?>">Results</a>
                <a href="index.php?content=contact&tourna_id=<?php echo $tournaId; ?>">Contact</a>
            </div>
        </div>

        <?php } ?>
    </header>



    <!-- Main Content -->
    <main>
        <div class="content-box">
            <?php
            // Message/Response/Notif
            if($msg != "") echo "<div class=\"msg\">$msg</div>";

            // No Tourna Yet -> Let's Get Started
            if($tournaId == NULL) { ?>
            <div class="form-box">
                <!-- Create Tourna -->
                <h1>Let's Get Started</h1>
                <form action="" method="post">
                    <h3>Create Tournament</h3>
                    <div class="form-field">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" max="255">
                    </div>  
                    <div class="form-field">
                        <label for="format">Format</label>
                        <select name="format" id="format">
                            <option value="Single Elimination">Single Elimination</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="max_entry">Max Entries</label>
                        <input type="number" name="max_entry" id="max_entry" max="1000">
                    </div>
                    <div class="form-field">
                        <label for="max_entry_player">Players per Entry</label>
                        <input type="number" name="max_entry_player" id="max_entry_player" max="1000">
                    </div>
                    <div class="form-field">
                        <label for="pairing">Pairing</label>
                        <select name="pairing" id="pairing">
                            <option value="Random">Random</option>
                            <option value="Order">Order</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="is_public" class="switch">
                            <input type="checkbox" name="is_public" id="is_public">
                            <div><div></div></div>
                            <span>Public</span>
                        </label>
                    </div>
                    <div class="form-field">
                        <label for="is_open" class="switch">
                            <input type="checkbox" name="is_open" id="is_open">
                            <div><div></div></div>
                            <span>Allow registration</span>
                        </label>
                    </div>
                    <div class="form-field">
                        <label for="description">Description</label>
                        <textarea name="description" id="description"></textarea>
                    </div>
                    <input type="hidden" name="creator_id" value="<?php echo $userId; ?>">
                    <input type="submit" name="create_tourna" value="Create">
                </form>
            </div>
            
            <?php }
            // Show content
            else {
                // Dashboard
                if($content == "dashboard") { ?>
                    <div class="form-box">
                        <form action="" method="post">
                            <h3>Tournament</h3>
                            <div class="form-field">
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" max="255" value="<?php echo $selectedTourna["title"]; ?>">
                            </div>
                            <div class="form-field">
                                <label for="format">Format</label>
                                <select name="format" id="format">
                                    <option value="Single Elimination">Single Elimination</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label for="max_entry">Max Entries</label>
                                <input type="number" name="max_entry" id="max_entry" max="1000" value="<?php echo $selectedTourna["max_entry"]; ?>">
                            </div>
                            <div class="form-field">
                                <label for="max_entry_player">Players per Entry</label>
                                <input type="number" name="max_entry_player" id="max_entry_player" max="1000" value="<?php echo $selectedTourna["max_entry_player"]; ?>">
                            </div>
                            <div class="form-field">
                                <label for="pairing">Pairing</label>
                                <select name="pairing" id="pairing">
                                    <option value="Random">Random</option>
                                    <option value="Order">Order</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label for="is_public" class="switch">
                                    <input type="checkbox" name="is_public" id="is_public" <?php echo ($tourna["is_public"]? "checked":""); ?>>
                                    <div><div></div></div>
                                    <span>Public</span>
                                </label>
                            </div>
                            <div class="form-field">
                                <label for="is_open" class="switch">
                                    <input type="checkbox" name="is_open" id="is_open" <?php echo ($tourna["is_open"]? "checked":""); ?>>
                                    <div><div></div></div>
                                    <span>Allow registration</span>
                                </label>
                            </div>
                            <div class="form-field">
                                <label for="description">Description</label>
                                <textarea name="description" id="description"><?php echo $tourna["description"]; ?></textarea>
                            </div>
                            <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                            <input type="hidden" name="creator_id" value="<?php echo $userId; ?>">
                            <input type="submit" name="update_tourna" value="Save">
                        </form>
                    </div>

            <?php }
                // Teams
                else if($content == "teams") { ?>

                    <!-- Add Team Form -->
                    <div class="form-box">
                        <form action="" method="post">
                            <h3>Add Team</h3>
                            <div class="form-field">
                                <label for="name">Team Name</label>
                                <input type="text" name="name" id="name" max="255">
                            </div>
                            <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                            <input type="submit" name="add_team" value="Add Team">
                        </form>
                    </div>
                    
                    <!-- Team List Table -->
                    <div class="table-box">
                        <table>
                            <th>Team</th>
                            <th>Add Players</th>
                            <th>Delete</th> 
                        </table>
                    </div>
                    
                <?php }
            }
            ?>
        </div>
    </main>


</body>
</html>

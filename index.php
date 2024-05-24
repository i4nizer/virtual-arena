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
// Creates a Team
else if(isset($_POST["add_team"])) {
    // Success
    if(createTeam($_POST["name"], $_POST["tourna_id"])) {
        $msg = "Team created successfully.";
    }
    // Fail
    else $msg = "An error occured, failed to create team.";
}
// Remove Team
else if(isset($_POST["remove_team"])) {
    // Success
    if(removeTeam($_POST["team_id"])) {
        $msg = "Team removed successfully.";
    }
    // Fail
    else $msg = "An error occured, failed to remove team.";
}
// Adds player
else if(isset($_POST["add_player"])) {
    // Success
    if(createPlayer($_POST["name"], $_POST["email"], $_POST["contact_no"], $_POST["team_id"])) {
        $msg = "Player added successfully.";
    }
    // Fail
    else $msg = "An error occured, failed to add player.";
}
// Updates player
else if(isset($_POST["update_player"])) {
    // Success
    if(updatePlayer($_POST["player_id"], $_POST["name"], $_POST["email"], $_POST["contact_no"], $_POST["score"], $_POST["wins"], $_POST["loses"])) {
        $msg = "Player updated successfully.";
    }
    // Fail
    else $msg = "An error occured, failed to update player.";
}
// Remove player
else if(isset($_POST["remove_player"])) {
    // Success
    if(removePlayer($_POST["player_id"])) {
        $msg = "Player removed successfully.";
    }
    // Fail
    else $msg = "An error occured, failed to remove player.";
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
                <a class="<?php echo $content == "dashboard"? "selected" : ""; ?>" href="index.php?content=dashboard&tourna_id=<?php echo $tournaId; ?>">Dashboard</a>
                <a class="<?php echo $content == "view"? "selected" : ""; ?>" href="view-tourna/index.php?tourna_id=<?php echo $tournaId; ?>" target="_blank">View</a>
                <a class="<?php echo $content == "teams"? "selected" : ""; ?>" href="index.php?content=teams&tourna_id=<?php echo $tournaId; ?>">Teams</a>
                <a class="<?php echo $content == "players"? "selected" : ""; ?>" href="index.php?content=players&tourna_id=<?php echo $tournaId; ?>">Players</a>
                <a class="<?php echo $content == "matches"? "selected" : ""; ?>" href="index.php?content=matches&tourna_id=<?php echo $tournaId; ?>">Matches</a>
                <a class="<?php echo $content == "results"? "selected" : ""; ?>" href="index.php?content=results&tourna_id=<?php echo $tournaId; ?>">Results</a>
                <a class="<?php echo $content == "contact"? "selected" : ""; ?>" href="index.php?content=contact&tourna_id=<?php echo $tournaId; ?>">Contact</a>
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
            
            <?php } // no tourna
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

            <?php } // dashboard
                // Teams
                else if($content == "teams") {
                    $teamLimit = $selectedTourna["max_entry"];
                    $teams = getTeams($tournaId);
                    
                    // Watch Team Limit (max_entry)
                    if(empty($teams) || count($teams) < $teamLimit) {
                    ?>

                        <!-- Add Team Form -->
                        <div class="form-box">
                            <form action="" method="post">
                                <h3>Add Team</h3>
                                <div class="form-field">
                                    <label for="name">Team Name</label>
                                    <input type="text" name="name" id="name" max="255" placeholder="Team name">
                                </div>
                                <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                <input type="submit" name="add_team" value="Add Team">
                            </form>
                        </div>

              <?php } ?>
                    
                    <!-- Team List Table -->
                    <div class="table-box">
                        <table>
                            <tr>
                                <th>Team</th>
                                <th>Manage Players</th>
                                <th>Manage Team</th>
                            </tr>
                      <?php if($teams) {
                                // Loop through
                                foreach($teams as $team) { ?>
                                    
                                    <tr>
                                        <td><?php echo $team["name"]; ?></td>
                                        <td><a href="index.php?content=players&team_id=<?php echo $team["id"]; ?>&tourna_id=<?php echo $tournaId; ?>">Manage</a></td>
                                        <td>
                                            <form action="" method="post">
                                                <input type="hidden" name="team_id" value="<?php echo $team["id"]; ?>">
                                                <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                                <input type="submit" name="remove_team" value="Remove Team">
                                            </form>
                                        </td>
                                    </tr>

                                <?php }
                            } // has teams
                            // no teams
                            else { ?> <tr> <td colspan="3">No Teams Found</td> </tr> <?php }
                            ?>
                        </table>
                    </div>
                    
                <?php } // teams

                // Players
                else if($content == "players") {
                    // has team_id (add/player list) | has player_id too (edit)
                    $teamId = isset($_GET["team_id"])? $_GET["team_id"] : NULL;                             // GET
                    $teamId = $teamId ?? (isset($_POST["team_id"])? $_POST["team_id"] : NULL);              // POST
                    $playerId = isset($_GET["player_id"])? $_GET["player_id"] : NULL;                       // GET
                    $playerId = $playerId ?? (isset($_POST["player_id"])? $_POST["player_id"] : NULL);      // POST

                    // Edit Player
                    if($playerId != NULL) { 
                        // Get Player info
                        $player = getPlayer($playerId);
                        
                        // After delete, there might be playerId but no tuple in db
                        if(empty($player)) {
                            header("Location: index.php?content=players&team_id=$teamId&tourna_id=$tournaId");
                            exit();
                        }
                    ?>

                        <!-- Edit Player Data -->
                        <div class="form-box">
                            <form action="" method="post">
                                <h3>Update Player</h3>
                                <div class="form-field">
                                    <label for="name">Player Name</label>
                                    <input type="text" name="name" id="name" max="255" placeholder="Name" value="<?php echo $player["name"]; ?>">
                                </div>
                                <div class="form-field">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" max="255" placeholder="Email" value="<?php echo $player["email"]; ?>">
                                </div>
                                <div class="form-field">
                                    <label for="contact_no">Contact</label>
                                    <input type="tel" name="contact_no" id="contact_no" max="255" placeholder="Contact number" value="<?php echo $player["contact_no"]; ?>">
                                </div>
                                <div class="form-field">
                                    <label for="score">Score</label>
                                    <input type="number" name="score" id="score" max="255" placeholder="Score" value="<?php echo $player["score"]; ?>">
                                </div>
                                <div class="form-field">
                                    <label for="wins">Wins</label>
                                    <input type="number" name="wins" id="wins" max="255" placeholder="Wins" value="<?php echo $player["wins"]; ?>">
                                </div>
                                <div class="form-field">
                                    <label for="loses">Loses</label>
                                    <input type="number" name="loses" id="loses" max="255" placeholder="Loses" value="<?php echo $player["loses"]; ?>">
                                </div>
                                <input type="hidden" name="player_id" value="<?php echo $playerId; ?>">
                                <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                                <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                <div class="form-btns">
                                    <input type="submit" name="update_player" value="Save">
                                    <input type="submit" name="remove_player" value="Remove">
                                </div>
                            </form>
                        </div>

                    <?php } // end of Edit Player

                    // Show Form & Player List when there is team_id
                    else if($teamId != NULL) { 
                        $players = getPlayers($teamId);
                        $playerLimit = $selectedTourna["max_entry_player"];
                        
                        // Watch Player Limit
                        if(empty($players) || count($players) < $playerLimit) {
                        ?>

                            <!-- Add Player Form -->
                            <div class="form-box">  
                                <form action="" method="post">
                                    <h3>Add Player</h3>
                                    <div class="form-field">
                                        <label for="name">Player Name</label>
                                        <input type="text" name="name" id="name" max="255" placeholder="Name">
                                    </div>
                                    <div class="form-field">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" max="255" placeholder="Email">
                                    </div>
                                    <div class="form-field">
                                        <label for="contact_no">Contact</label>
                                        <input type="tel" name="contact_no" id="contact_no" max="255" placeholder="Contact number">
                                    </div>
                                    <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                    <input type="submit" name="add_player" value="Add Player">
                                </form>
                            </div>

                  <?php } // end of player limit ?>
                    
                        <!-- Player List Table mixed with Team Name -->
                        <div class="table-box">
                            <table>
                                <tr>
                                    <th>Team Name</th>
                                    <th>Player Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Score</th>
                                    <th>Wins</th>
                                    <th>Loses</th>
                                    <th>Edit</th>
                                </tr>
                          <?php if($players) {
                                    // Loop through
                                    foreach($players as $player) { ?>
                                        
                                        <tr>
                                            <td><?php echo $player["team_name"]; ?></td>
                                            <td><?php echo $player["name"]; ?></td>
                                            <td><?php echo $player["email"]; ?></td>
                                            <td><?php echo $player["contact_no"]; ?></td>
                                            <td><?php echo $player["score"]; ?></td>
                                            <td><?php echo $player["wins"]; ?></td>
                                            <td><?php echo $player["loses"]; ?></td>
                                            <td><a href="index.php?content=players&team_id=<?php echo $teamId; ?>&player_id=<?php echo $player["id"]; ?>&tourna_id=<?php echo $tournaId; ?>">Edit</a></td>
                                        </tr>

                                    <?php }
                                } // has players
                                // no players
                                else { ?> <tr> <td colspan="8">No Players Found</td> </tr> <?php }
                                ?>
                            </table>
                        </div>
                    
              <?php } // end of Form with Player list (has team_id)

                    // Just want to see Players (List of Players)
                    else {
                        // Get All Players of Tourna
                        $players = getAllPlayers($tournaId);    
                    ?>
                        
                        <!-- Player List Table mixed with Team Name -->
                        <div class="table-box">
                            <table>
                                <tr>
                                    <th>Team Name</th>
                                    <th>Player Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Score</th>
                                    <th>Wins</th>
                                    <th>Loses</th>
                                    <th>Edit</th>
                                </tr>
                          <?php if($players) {
                                    // Loop through
                                    foreach($players as $player) { ?>
                                        
                                        <tr>
                                            <td><?php echo $player["team_name"]; ?></td>
                                            <td><?php echo $player["name"]; ?></td>
                                            <td><?php echo $player["email"]; ?></td>
                                            <td><?php echo $player["contact_no"]; ?></td>
                                            <td><?php echo $player["score"]; ?></td>
                                            <td><?php echo $player["wins"]; ?></td>
                                            <td><?php echo $player["loses"]; ?></td>
                                            <td><a href="index.php?content=players&team_id=<?php echo $player["team_id"]; ?>&player_id=<?php echo $player["id"]; ?>&tourna_id=<?php echo $tournaId; ?>">Edit</a></td>
                                        </tr>

                                    <?php }
                                } // has players
                                // no players
                                else { ?> <tr> <td colspan="8">No Players Found</td> </tr> <?php }
                                ?>
                            </table>
                        </div>

              <?php }
                }

            } // if($tourna_id != NULL)
            ?>


        </div>
    </main>


</body>
</html>

<?php
require '../../funcs/tourna.php';
require '../../funcs/team.php';

// Check if Logged In
session_start();
if(!isset($_SESSION["user_id"])) header("Location: ../../auth/signin.php");

$userId = $_SESSION["user_id"];
$userName = $_SESSION["username"];

// Get All Tournaments of the user
$tournas = getTournas($userId, "id, title");     // title, id
$tournaId = NULL;

if( !(isset($_POST["tourna_id"]) || isset($_GET["tourna_id"])) ) {
    if(empty($tournas)) {
        header("Location: ctourna.php");
        exit();
    }
    
    $tournaId = $tournas[0]["id"];
}
else {  
    $tournaId = isset($_POST["tourna_id"])? $_POST["tourna_id"] : $_GET["tourna_id"];
    if($tournaId == "new") header("Location: ctourna.php");
}

// Get Selected Tournament of the user *
$selectedTourna = getTourna($userId, $tournaId);
if(empty($selectedTourna)) header("Location: ctourna.php");

// Get tourna status
$tournaStatus = getTimeStatus($selectedTourna["start_dt"], $selectedTourna["end_dt"], $selectedTourna["timezone"]);
if($tournaStatus != "Preparation") {
    header("Location: view.php?tourna_id=$tournaId");
    exit();
}

// Get All Teams of the selected tourna
$teams = getTeams($tournaId);


// Notifier
$msg = "";
$msgState = "";





// Updates a Tournament
if(isset($_POST["update_tourna"])) {
    // Success
    if(updateTourna($_POST["tourna_id"], $_POST["title"], $_POST["timezone"], $_POST["format"], $_POST["max_entry"], $_POST["max_entry_player"], $_POST["start_dt"], $_POST["end_dt"], $_POST["pairing"], isset($_POST["is_public"]), isset($_POST["is_open"]), $_POST["description"], $_POST["creator_id"])) {
        $msg = "Tournament info saved.";
        $msgState = "success";
    }
    // Failed
    else {
        $msg = "An error occured, failed to save tournament info.";
        $msgState = "failed";
    }
}
// Deletes tourna
if(isset($_POST["delete_tourna"])) {
    // Success
    if(deleteTourna($_POST["tourna_id"])) {
        $msg = "Tournament deleted successfully";
        $msgState = "success";
    }
    // Failed
    else {
        $msg = "An error occured, failed to delete tournament.";
        $msgState = "failed";
    }
}

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/index.css">
    <title>Virtual Arena</title>
</head>
<body>



    <!-- Header -->
    <header>
        <div class="logo">
            <h2>Virtual Arena</h2>
        </div>
        <nav>
            <ul class="nav-box">
                <li class="nav-item">
                    <a href="#" class="nav-btn selected">Tournaments</a>
                    <ul class="dropdown">
                        <!-- Tournament List -->
                        <?php 
                        foreach($tournas as $tourna) {
                            $id = $tourna["id"];
                            $title = htmlspecialchars($tourna["title"]);
                            echo "<li><a href=\"index.php?tourna_id=$id\">$title</a></li>";
                        }
                        ?>
                        <li><a href="index.php?tourna_id=new">New</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../teams/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Teams</a>
                    <ul class="dropdown">
                        <!-- All Teams of Selected Tourna -->
                        <?php
                        foreach($teams as $team) {
                            $id = $team["id"];
                            $name = htmlspecialchars($team["name"]);
                            echo "<li><a href=\"../players/index.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../players/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Players</a>
                </li>
                <li class="nav-item">
                    <a href="../matches/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Matches</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-btn">Leaderboards</a>
                    <ul class="dropdown">
                        <li><a href="#">Rankings</a></li>
                        <li><a href="#">Playerlist</a></li>
                        <li><a href="#">View Bracket</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-btn">Statistics</a>
                    <ul class="dropdown">
                        <li><a href="#">Results</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="auth-box">
            <a href="../../auth/signout.php" class="auth-btn">Sign Out</a>
        </div>
    </header>
    <!-- Header -->



    <!-- Main Content -->
    <main>

        <div class="content-box">
            <?php if($msg != "") echo "<div id=\"msg\" class=\"msg $msgState\">$msg</div>"; ?>
            <div class="form-box box">
                <form class="form-display" action="" method="post">
                    <h3>Tournament</h3>
                    <div class="form-field">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" max="255" value="<?php echo htmlspecialchars($selectedTourna["title"]); ?>">
                    </div>
                    <div class="form-field">
                        <label for="timezone">Tournament Timezone</label>
                        <select name="timezone" id="timezone">
                            <?php 
                            $timezones = DateTimeZone::listIdentifiers();
                            if($timezones) foreach($timezones as $timezone) {
                                $tournaTimezone = $selectedTourna["timezone"];
                                ?>
                                <option value="<?php echo $timezone; ?>" <?php echo ($tournaTimezone == $timezone? "selected" : ""); ?>><?php echo $timezone; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="format">Format</label>
                        <select name="format" id="format">
                            <option value="Single Elimination" <?php echo ($selectedTourna["format"] == "Single Elimination"? "selected" : ""); ?>>Single Elimination</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="max_entry">Max Entries</label>
                        <input type="number" name="max_entry" id="max_entry" max="1000" value="<?php echo htmlspecialchars($selectedTourna["max_entry"]); ?>">
                    </div>
                    <div class="form-field">
                        <label for="max_entry_player">Players per Entry</label>
                        <input type="number" name="max_entry_player" id="max_entry_player" max="1000" value="<?php echo htmlspecialchars($selectedTourna["max_entry_player"]); ?>">
                    </div>
                    <div class="form-field">
                        <label for="start_dt">Start DateTime</label>
                        <input type="datetime" name="start_dt" id="start_dt" value="<?php echo $selectedTourna["start_dt"]; ?>" placeholder="00-00-00 00:00:00">
                    </div>
                    <div class="form-field">
                        <label for="end_dt">End DateTime</label>
                        <input type="datetime" name="end_dt" id="end_dt" value="<?php echo $selectedTourna["end_dt"]; ?>" placeholder="00-00-00 00:00:00">
                    </div>
                    <div class="form-field">
                        <label for="pairing">Pairing</label>
                        <select name="pairing" id="pairing">
                            <option value="Random" <?php echo($selectedTourna["pairing"] == "Random"? "selected" : ""); ?>>Random</option>
                            <option value="Order" <?php echo($selectedTourna["pairing"] == "Order"? "selected" : ""); ?>>Order</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="is_public" class="switch">
                            <input type="checkbox" name="is_public" id="is_public" <?php echo htmlspecialchars($selectedTourna["is_public"]? "checked":""); ?>>
                            <div><div></div></div>
                            <span>Public</span>
                        </label>
                    </div>
                    <div class="form-field">
                        <label for="is_open" class="switch">
                            <input type="checkbox" name="is_open" id="is_open" <?php echo htmlspecialchars($selectedTourna["is_open"]? "checked":""); ?>>
                            <div><div></div></div>
                            <span>Allow registration</span>
                        </label>
                    </div>
                    <div class="form-field">
                        <label for="description">Description</label>
                        <textarea name="description" id="description"><?php echo htmlspecialchars($selectedTourna["description"]); ?></textarea>
                    </div>
                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                    <input type="hidden" name="creator_id" value="<?php echo $userId; ?>">
                    <div class="form-btn-box">
                        <input type="submit" name="delete_tourna" value="Delete" class="danger">
                        <input type="submit" name="update_tourna" value="Save">
                    </div>
                </form>
            </div>

            <!-- TODO: Layout of this dashboard info box -->
            <!-- TODO: Teams (../teams/index.php) -->
            <!-- Stats -> Status, Teams, Players, Matches, Rank(w/MVP) -->
            <div class="info-box box">
                <div class="info-dash">
                    <h3><?php echo htmlspecialchars($selectedTourna["title"]); ?></h3>
                    <p>Status: <?php echo $tournaStatus; ?></p>
                    <p>Team Count: <?php echo getTeamCount($tournaId); ?></p>
                    <p>Player Count: <?php echo getPlayerCount($tournaId); ?></p>
                    <?php
                    // Show Hot(Ongoing) Matches
                    $matches = $tournaStatus != "Preparation"? getOngoingMatches($selectedTourna["id"], $selectedTourna["timezone"]) : array();
                    if(!empty($matches)) {
                    ?>  <table>
                            <tr>
                                <th>Team1</th>
                                <th>Team2</th>
                            </tr>
                    <?php
                        foreach($matches as $match) { ?>
                            <tr>
                                <td><?php echo $match["team1"]; ?></td>
                                <td><?php echo $match["team2"]; ?></td>
                            </tr>
                <?php } // match loop
                    ?>  </table> <?php
                    } // has ongoing matches
                    else { echo "<p>Matches: No Ongoing Matches</p>"; }
                    
                    // Show top5 teams and their top player
                    $topTeams = $tournaStatus != "Preparation"? getTopTeamsTopPlayer($tournaId) : array();
                    if(!empty($topTeams)) {
                        ?>  <table>
                            <tr>
                                <th>Top Teams</th>
                                <th>Check Team</th>
                                <th>Team MVP</th>
                                <th>Check Player</th>
                            </tr>
                            <?php
                        foreach($topTeams as $team) { ?>
                            <tr>
                                <td><?php echo $team["team_name"]; ?></td>
                                <td><a href="../teams/index.php?tourna_id=<?php echo $tournaId; ?>&team_id=<?php echo $team["team_id"]; ?>">Check</a></td>
                                <td><?php echo $team["player_name"]; ?></td>
                                <td><a href="../players/index.php?tourna_id=<?php echo $tournaId; ?>&player_id=<?php echo $team["player_id"]; ?>">Check</a></td>
                            </tr>
                <?php } // top teams loop 
                    } // has top teams
                    else { echo "<p>Top5 Teams: No Top Teams/Players yet.</p>"; }
                    ?>
    
                </div>
            </div>

        </div>
    </main>
    <!-- Main Content -->

    

    <script>
        window.onload = async function() {
            const msg = document.getElementById('msg')
            if(msg == null) return
            setTimeout(() => msg.style.opacity = '0', 3000)
        }
    </script>


</body>
</html>

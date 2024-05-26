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

// Get All Teams of the selected tourna
$teams = getTeams($tournaId);

// Notifier
$msg = "";
$msgState = "";



// Updates ongoing Tournament
if($tournaStatus == "Ongoing" && isset($_POST["update_tourna"])) {
    // Success
    if( updateTournaOngoing($_POST["tourna_id"], $_POST["description"], $_POST["end_dt"], isset($_POST["is_public"])) ) {
        $msg = "Tournament info saved.";
        $msgState = "success";
    }
    // Failed
    else {
        $msg = "An error occured, failed to save tournament info.";
        $msgState = "failed";
    }
}
// Updates ended Tournament
else if($tournaStatus == "Ended" && isset($_POST["update_tourna"])) {
    // Success
    if( updateTournaEnded($_POST["tourna_id"], $_POST["description"], isset($_POST["is_public"])) ) {
        $msg = "Tournament info saved.";
        $msgState = "success";
    }
    // Failed
    else {
        $msg = "An error occured, failed to save tournament info.";
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
                    <h3>Tournament: <?php echo htmlspecialchars($selectedTourna["title"]); ?></h3>
                    <div class="form-field">
                        <label for="description">Description</label>
                        <textarea name="description" id="description"><?php echo htmlspecialchars($selectedTourna["description"]); ?></textarea>
                    </div>
                    <?php
                    // Can update end_dt if ongoing (extending time)
                    if($tournaStatus == "Ongoing") { ?>
                        <div class="form-field">
                            <label for="end_dt">End DateTime</label>
                            <input type="datetime" name="end_dt" id="end_dt" value="<?php echo $selectedTourna["end_dt"]; ?>" placeholder="00-00-00 00:00:00">
                        </div>  
                <?php }
                    ?>
                    <div class="form-field">
                        <label for="is_public" class="switch">
                            <input type="checkbox" name="is_public" id="is_public" <?php echo htmlspecialchars($selectedTourna["is_public"]? "checked":""); ?>>
                            <div><div></div></div>
                            <span>Public</span>
                        </label>
                    </div>
                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                    <input type="submit" name="update_tourna" value="Save">
                </form>
            </div>

            <!-- Stats -> Status, Teams, Players, Matches, Rank(w/MVP) -->
            <div class="info-box box">
                <div class="info-dash">
                    <h3><?php echo htmlspecialchars($selectedTourna["title"]); ?></h3>
                    <p>Status: <?php echo $tournaStatus; ?></p>
                    <p>Team Count: <?php echo getTeamCount($tournaId); ?>/<?php echo $selectedTourna["max_entry"]; ?></p>
                    <p>Player Count: <?php echo getPlayerCount($tournaId); ?>/<?php echo $selectedTourna["max_entry"] * $selectedTourna["max_entry_player"]; ?></p>
                    <?php
                    // Show Hot(Ongoing) Matches
                    $matches = $tournaStatus != "Preparation"? getOngoingMatches($selectedTourna["id"], $selectedTourna["timezone"]) : array();
                    if(!empty($matches)) {
                    ?>  
                        <h4>Ongoing Matches</h4>
                        <table>
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
                        ?> 
                        <h4>Top5 Teams</h4>
                        <table>
                            <tr>
                                <th>Rank</th>
                                <th>Top Teams</th>
                                <th>Check Team</th>
                                <th>Team MVP</th>
                                <th>Check Player</th>
                            </tr>
                            <?php
                        $rank = 1;
                        foreach($topTeams as $team) { ?>
                            <tr>
                                <td><?php echo $rank++; ?></td>
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

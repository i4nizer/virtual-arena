<?php
require '../../funcs/tourna.php';

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
        <div class="header-row">
            <div class="header-section">
                <h3>Virtual Arena</h3>
                <form class="form-quick" action="" method="post">
                    <select name="tourna_id" onchange="this.form.submit()">
                    <?php
                    // Create options
                    foreach($tournas as $tourna) {
                        $title = $tourna["title"];
                        $id = $tourna["id"];
                    ?> <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($title); ?></option>
              <?php }
                    ?>
                        <option value="new">New</option>
                    </select>
                </form>
            </div>
            <div class="header-section">
                <h4>@<?php echo htmlspecialchars($userName); ?></h4>
                <a class="btn" href="auth/signout.php">Sign Out</a>
            </div>
        </div>
        <div class="header-row">
            <div class="header-nav">
                <a href="#" class="selected">Dashboard</a>
                <a href="../../viewer/index.php?tourna_id=<?php echo $tournaId; ?>" target="_blank">View</a>
                <a href="../teams/index.php?tourna_id=<?php echo $tournaId; ?>">Teams</a>
                <a href="../players/index.php?tourna_id=<?php echo $tournaId; ?>">Players</a>
                <a href="../matches/index.php?tourna_id=<?php echo $tournaId; ?>">Matches</a>
                <a href="../results/index.php?tourna_id=<?php echo $tournaId; ?>">Results</a>
                <a href="../contact/index.php?tourna_id=<?php echo $tournaId; ?>">Contact</a>
            </div>
        </div>
    </header>
    <!-- Header -->



    <!-- Main Content -->
    <main>

        <div class="content-box">
            <?php if($msg != "") echo "<div id=\"msg\" class=\"msg $msgState\">$msg</div>"; ?>
            <div class="form-box">
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

            <!-- TODO: Layout of this dashboard info box -->
            <!-- TODO: Teams (../teams/index.php) -->
            <!-- Stats -> Status, Teams, Players, Matches, Rank(w/MVP) -->
            <div class="info-box">
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
                    $topTeams = $tournaStatus != "Preparation"? getTopTeams($tournaId, 5) : array();
                    if(!empty($topTeams)) {
                        ?>  <table>
                            <tr>
                                <th>Rank</th>
                                <th>Top Teams</th>
                                <th>Check Team</th>
                                <th>Team MVP</th>
                                <th>Check Player</th>
                            </tr>
                            <?php
                        $rank = 1;
                        print_r($topTeams);
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

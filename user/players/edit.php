<?php
require '../../funcs/tourna.php';
require '../../funcs/time.php';
require '../../funcs/round.php';
require '../../funcs/team.php';
require '../../funcs/player.php';

// Check if Logged In
session_start();
if(!isset($_SESSION["user_id"])) header("Location: ../../auth/signin.php");

$userId = $_SESSION["user_id"];
$userName = $_SESSION["username"];

// Get All Tournaments of the user
$tournas = getTournas($userId);
if(empty($tournas)) header("Location: ../tournaless/index.php");

// Init tourna_id
$tournaId = isset($_POST["tourna_id"])? $_POST["tourna_id"] : NULL;
$tournaId = $tournaId ?? (isset($_GET["tourna_id"])? $_GET["tourna_id"] : NULL);
$tournaId = $tournaId ?? $tournas[0]["id"];

// Get Selected Tourna
$tourna = getTournaWithSetup($tournaId);
$tournaStatus = getTimeStatus($tourna["start_dt"], $tourna["end_dt"], $tourna["timezone"]);
if($tournaStatus == "Ended") header("Location: all.php?tourna_id=$tournaId");

// Get All Teams
$teams = getTeams($tournaId);
if(empty($teams)) header("Location: ../teams/index.php?tourna_id=$tournaId&msg=Add Team first");

// Get Team Id
$teamId = isset($_POST["team_id"]) || isset($_GET["team_id"])? ($_POST["team_id"] ?? $_GET["team_id"]): NULL;
if($teamId == NULL) header("Location: all.php?tourna_id=$tournaId");

// Get Selected Team
$filteredTeams = array_filter($teams, function($t) use ($teamId) { return $t["id"] == $teamId; });
$team = reset($filteredTeams);

// Get Player Id
$playerId = isset($_POST["player_id"]) || isset($_GET["player_id"])? ($_POST["player_id"] ?? $_GET["player_id"]): NULL;
if($playerId == NULL) header("Location: index.php?tourna_id=$tournaId&team_id=$teamId");

// Get Players of selected Team
$players = getPlayers($teamId);
if(empty($players)) header("Location: index.php?tourna_id=$tournaId&team_id=$teamId&msg=Add Players First");

// Get selected player of the Team
$filteredPlayers = array_filter($players, function($p) use ($playerId) { return $p["id"] == $playerId; });
$player = reset($filteredPlayers);

// Notif
$msg = isset($_GET["msg"])? $_GET["msg"] : "";
$msgState = isset($_GET["msg_state"])? $_GET["msg_state"] : "";






?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/table.css">
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
                    <a href="../tournaments/index.php" class="nav-btn">Tournaments</a>
                    <ul class="dropdown">
                        <li><a href="../tournaments/index.php?category=all&tourna_id=<?php echo $tournaId; ?>">All</a></li>
                        <li><a href="../tournaments/index.php?category=preparation&tourna_id=<?php echo $tournaId; ?>">Preparation</a></li>
                        <li><a href="../tournaments/index.php?category=ongoing&tourna_id=<?php echo $tournaId; ?>">Ongoing</a></li>
                        <li><a href="../tournaments/index.php?category=ended&tourna_id=<?php echo $tournaId; ?>">Ended</a></li>
                        <li><a href="../tournaments/create.php">Create New</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../dashboard/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Dashboard</a>
                    <ul class="dropdown">
                        <!-- Tournament List -->
                        <?php 
                        foreach($tournas as $t) {
                            $id = $t["id"];
                            $title = htmlspecialchars($t["title"]);
                            echo "<li><a href=\"index.php?tourna_id=$id\">$title</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../matches/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Matchlist</a>
                    <ul class="dropdown">
                        <!-- Tournament Matchlist -->
                        <?php 
                        $rounds = getRounds($tournaId);
                        foreach($rounds as $r) {
                            $id = $r["id"];
                            $number = $r["number"];
                            echo "<li><a href=\"../matches/index.php?tourna_id=$tournaId&round_id=$id\">Round $number</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../teams/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Teams</a>
                    <ul class="dropdown">
                        <!-- All Teams of Selected Tourna -->
                        <li><a href="../teams/index.php?tourna_id=<?php echo $tournaId; ?>&team_id=">All</a></li>
                        <?php
                        foreach($teams as $t) {
                            $id = $t["id"];
                            $name = htmlspecialchars($t["name"]);
                            echo "<li><a href=\"../teams/index.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="all.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn selected">Players</a>
                    <ul class="dropdown">
                        <!-- All Teams of Selected Tourna -->
                        <li><a href="all.php?tourna_id=<?php echo $tournaId; ?>">All</a></li>
                        <?php
                        foreach($teams as $t) {
                            $id = $t["id"];
                            $name = htmlspecialchars($t["name"]);
                            echo "<li><a href=\"index.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../leaderboards/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Leaderboards</a>
                    <ul class="dropdown">
                        <li><a href="../leaderboards/index.php?tourna_id=<?php echo $tournaId; ?>&ranking=team">Team Ranking</a></li>
                        <li><a href="../leaderboards/index.php?tourna_id=<?php echo $tournaId; ?>&ranking=player">Player Ranking</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="auth-box">
            <a href="../../auth/signout.php" class="auth-btn">Sign Out @<?php echo $userName; ?></a>
        </div>
    </header>
    <!-- Header -->



    <!-- Main Content -->
    <main>
        <div class="content-box">
            <?php if($msg != "") echo "<div id=\"msg\" class=\"msg $msgState\">$msg</div>"; ?>

            <div class="head-box">
                <div class="box">
                    <h3>Tournament: <?php echo $tourna["title"]; ?></h3>
                </div>
                <div class="nav-box">
                    <h3 class="box">Team: </h3>
                    <div class="nav-item">
                        <h3 class="nav-btn"><a href="#"><?php echo $team["name"]; ?></a></h3>
                        <ul class="dropdown">
                            <li><a href="all.php?tourna_id=<?php echo $tournaId; ?>">All</a></li>
                            <?php
                            foreach($teams as $t) {
                                $id = $t["id"];
                                $name = $t["name"];
                                echo "<li><a href=\"index.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="nav-box">
                    <h3 class="box">Player: </h3>
                    <div class="nav-item">
                        <h3 class="nav-btn"><a href="#"><?php echo $player["name"]; ?></a></h3>
                        <ul class="dropdown">
                            <?php
                            foreach($players as $p) {
                                $id = $p["id"];
                                $name = $p["name"];
                                echo "<li><a href=\"edit.php?tourna_id=$tournaId&team_id=$teamId&player_id=$id\">$name</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="form-box box">
                <form action="uplayer.php" method="post">
                    <?php
                    if($tournaStatus == "Preparation") {
                        ?>
                        <h3>Edit Player - <?php echo $player["name"]; ?></h3>
                        <div class="form-field">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" placeholder="Player Name" value="<?php echo $player["name"]; ?>"> 
                        </div>
                        <div class="form-field">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" placeholder="Email" value="<?php echo $player["email"]; ?>"> 
                        </div>
                        <div class="form-field">
                            <label for="contact_no">Contact</label>
                            <input type="text" name="contact_no" id="contact_no" placeholder="Contact Number" value="<?php echo $player["contact_no"]; ?>"> 
                        </div>
                        <?php $redirect = "edit.php?tourna=$tournaId&team_id=$teamId&player_id=$playerId"; ?>
                        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
                        <input type="hidden" name="player_id" value="<?php echo $player["id"]; ?>">
                        <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                        <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                        <input type="submit" name="edit_player" value="Save">
                        <?php
                    }
                    else if($tournaStatus == "Ongoing") {
                        ?>
                        <h3>Update Player - <?php echo $player["name"]; ?></h3>
                        <div class="form-field">
                            <label for="score">Score</label>
                            <input type="number" name="score" id="score" placeholder="Score" value="<?php echo $player["score"]; ?>"> 
                        </div>
                        <div class="form-field">
                            <label for="wins">Wins</label>
                            <input type="number" name="wins" id="wins" placeholder="Wins" value="<?php echo $player["wins"]; ?>"> 
                        </div>
                        <div class="form-field">
                            <label for="loses">Loses</label>
                            <input type="number" name="loses" id="loses" placeholder="Contact Number" value="<?php echo $player["loses"]; ?>"> 
                        </div>
                        <?php $redirect = "edit.php?tourna=$tournaId&team_id=$teamId&player_id=$playerId"; ?>
                        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
                        <input type="hidden" name="player_id" value="<?php echo $player["id"]; ?>">
                        <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                        <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                        <input type="submit" name="update_player" value="Update">
                        <?php
                    }
                    ?>
                </form>
            </div>

            <div class="table-box">
                <table class="box">
                    <tr>
                        <th></th>
                        <th>Player</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <?php
                        if($tournaStatus == "Preparation") {
                            echo "<th>Edit</th>";
                            echo "<th>Remove</th>";
                        }
                        else if($tournaStatus == "Ongoing") {
                            echo "<th>Score</th>";
                            echo "<th>Wins</th>";
                            echo "<th>Loses</th>";
                            echo "<th>Edit</th>";
                        }
                        else { // no go here
                            echo "<th>Score</th>";
                            echo "<th>Wins</th>";
                            echo "<th>Loses</th>";
                        }
                        ?>
                    </tr>
                    <?php
                    foreach($players as $i => $p) {
                        echo "<tr>";
                        
                        $id = $p["id"];
                        $name = $p["name"];
                        $email = $p["email"];
                        $contact = $p["contact_no"];

                        echo "<td>".($i + 1)."</td>";
                        echo "<td>$name</td>";
                        echo "<td>$email</td>";
                        echo "<td>$contact</td>";
                        
                        if($tournaStatus == "Preparation") {    
                            echo "<td><a href=\"edit.php?tourna_id=$tournaId&team_id=$teamId&player_id=$id\">Edit</td>";
                            ?>
                            <td>
                                <form action="dplayer.php" method="post">
                                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                    <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                                    <input type="hidden" name="player_id" value="<?php echo $id; ?>">
                                    <input class="danger" type="submit" name="delete_player" value="Remove">
                                </form>
                            </td>
                            <?php
                        }
                        else if($tournaStatus == "Ongoing") {
                            $score = $p["score"];
                            $wins = $p["wins"];
                            $loses = $p["loses"];

                            echo "<td>$score</td>";
                            echo "<td>$wins</td>";
                            echo "<td>$loses</td>";
                            echo "<td><a href=\"edit.php?tourna_id=$tournaId&team_id=$teamId&player_id=$id\">Edit</td>";
                        }
                        else {  // won't go here though
                            $score = $p["score"];
                            $wins = $p["wins"];
                            $loses = $p["loses"];

                            echo "<td>$score</td>";
                            echo "<td>$wins</td>";
                            echo "<td>$loses</td>";
                        }
                        echo "</tr>";
                    }
                    if(count($players) <= 0) echo "<tr><td colspan=\"100%\">Team doesn't have players yet.</td></tr>";
                    ?>
                </table>
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
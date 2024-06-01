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

// Get All Teams
$teams = getTeams($tournaId);
if(empty($teams)) header("Location: ../teams/index.php?tourna_id=$tournaId&msg=Add Team first");

// Get Team Id
$teamId = isset($_POST["team_id"]) || isset($_GET["team_id"])? ($_POST["team_id"] ?? $_GET["team_id"]): NULL;
if($teamId != NULL) header("Location: index.php?tourna_id=$tournaId&team_id=$teamId");

// No Selected Team, No Selected Player
$players = getAllPlayers($tournaId);    // has team_id, team_name

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
                    <a href="#" class="nav-btn selected">Players</a>
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
                        <h3 class="nav-btn"><a href="#">All</a></h3>
                        <ul class="dropdown">
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
            </div>

            <div class="table-box">
                <table class="box">
                    <tr>
                        <th></th>
                        <th>Team</th>
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
                        else {
                            echo "<th>Score</th>";
                            echo "<th>Wins</th>";
                            echo "<th>Loses</th>";
                        }
                        ?>
                    </tr>
                    <?php
                    foreach($players as $i => $p) {
                        echo "<tr>";
                        
                        $teamName = $p["team_name"];
                        $id = $p["id"];
                        $name = $p["name"];
                        $email = $p["email"];
                        $contact = $p["contact_no"];
                        
                        echo "<td>".($i + 1)."</td>";
                        echo "<td>$teamName</td>";
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
                        else {
                            $score = $p["score"];
                            $wins = $p["wins"];
                            $loses = $p["loses"];

                            echo "<td>$score</td>";
                            echo "<td>$wins</td>";
                            echo "<td>$loses</td>"; 
                        }
                        echo "</tr>";
                    }
                    if(count($players) <= 0) echo "<tr><td colspan=\"100%\">Tournament doesn't have players yet.</td></tr>";
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
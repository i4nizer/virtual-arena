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

// Init team_id
$teamId = isset($_POST["team_id"])? $_POST["team_id"] : NULL;
$teamId = $teamId ?? (isset($_GET["team_id"])? $_GET["team_id"] : NULL);
if($teamId == NULL) header("Location: index.php?tourna_id=$tournaId");

// Get Selected Tourna
$tourna = getTournaWithSetup($tournaId);
$tournaStatus = getTimeStatus($tourna["start_dt"], $tourna["end_dt"], $tourna["timezone"]);

// Get Selected Team
$team = getTeam($teamId);

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
                            echo "<li><a href=\"../dashboard/index.php?round_id=$id\">Round $number</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn selected">Teams</a>
                    <ul class="dropdown">
                        <!-- All Teams of Selected Tourna -->
                        <li><a href="index.php?tourna_id=<?php echo $tournaId; ?>">All</a></li>
                        <?php
                        $teams = getTeams($tournaId, "id, name");
                        foreach($teams as $t) {
                            $id = $t["id"];
                            $name = htmlspecialchars($t["name"]);
                            echo "<li><a href=\"index.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../players/all.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Players</a>
                    <ul class="dropdown">
                        <!-- All Teams of Selected Tourna -->
                        <li><a href="../players/all.php?tourna_id=<?php echo $tournaId; ?>">All</a></li>
                        <?php
                        foreach($teams as $t) {
                            $id = $t["id"];
                            $name = htmlspecialchars($t["name"]);
                            echo "<li><a href=\"../players/index.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
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
                        <h3 class="nav-btn"><?php echo $team["name"]; ?></h3>
                        <ul class="dropdown">
                            <li><a href="index.php?tourna_id=<?php echo $tournaId; ?>">All</a></li>
                            <?php
                            foreach($teams as $t) {
                                $id = $t["id"];
                                $name = $t["name"];
                                echo "<li><a href=\"index.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
                            }
                            ?>
                            <li><a href="index.php?tourna_id=<?php echo $tournaId; ?>">Add Team</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <?php
            // Edit only when preparation
            if($tournaStatus == "Preparation") {
                ?>
                <div class="form-box box">
                    <form action="uteam.php" method="post">
                        <h3>Edit Team - <?php echo $team["name"]; ?></h3>
                        <div class="form-field">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" value="<?php echo $team["name"]; ?>">
                        </div>
                        <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                        <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                        <input type="submit" name="update_team" value="Save">
                    </form>
                    <form class="form-quick" action="dteam.php" method="post">
                        <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                        <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                        <input class="danger" type="submit" name="delete_team" value="Remove">
                    </form>
                </div>
                <?php
            }
            ?>

            <div class="table-box">
                <table class="box">
                    <tr>
                        <th>Player</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Edit</th>
                        <?php
                        if($tournaStatus == "Preparation") echo "<th>Remove</th>";
                        ?>
                    </tr>
                    <?php
                    $players = getPlayers($team["id"]);
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
                        echo "<td><a href=\"../players/edit.php?tourna_id=$tournaId&team_id=$teamId&player_id=$id\">Edit</td>";

                        if($tournaStatus == "Preparation") {
                            ?>
                            <td>
                                <form action="../players/dplayer.php" method="post">
                                    <input type="hidden" name="redirect" value="../teams/index.php?tourna_id=<?php echo $tournaId; ?>&team_id=<?php echo $teamId; ?>">
                                    <input type="hidden" name="player_id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="team_id" value="<?php echo $team["id"]; ?>">
                                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                    <input class="danger" type="submit" name="delete_player" value="Remove">
                                </form>
                            </td>
                            <?php
                        }
                        echo "</tr>";
                    }
                    
                    if($tournaStatus == "Preparation" && (empty($players) || count($players) < $tourna["max_entry_player"])) {
                        echo "<tr><td colspan=\"100%\"><a href=\"../players/index.php?tourna_id=$tournaId&team_id=$teamId\">Add Players</a></td></tr>";
                    }
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
<?php
require '../../funcs/tourna.php';
require '../../funcs/time.php';
require '../../funcs/round.php';
require '../../funcs/team.php';
require '../../funcs/player.php';
require '../../funcs/match.php';

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

// Get Viewed Tourna
$tourna = getTournaWithCount($tournaId);
$tournaStatus = getTimeStatus($tourna["start_dt"], $tourna["end_dt"], $tourna["timezone"]);

// Get Rounds
$rounds = getRounds($tournaId);
if(empty($rounds)) header("Location: ../tournaments/index.php?tourna_id=$tournaId&msg=No rounds yet.&msg_state=failed");

// Init round_id
$roundId = isset($_POST["round_id"])? $_POST["round_id"] : NULL;
$roundId = $roundId ?? (isset($_GET["round_id"])? $_GET["round_id"] : NULL);
$roundId = $roundId ?? $rounds[0]["id"];
if($roundId == NULL) header("Location: all.php?tourna_id=$tournaId");

// Get Selected Round
$filteredRounds = array_filter($rounds, function($r) use ($roundId) { return $r["id"] == $roundId; });
$round = reset($filteredRounds);

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
                            echo "<li><a href=\"../dashboard/index.php?tourna_id=$id\">$title</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-btn selected">Matchlist</a>
                    <ul class="dropdown">
                        <!-- Tournament Matchlist -->
                        <li><a href="all.php?tourna_id=<?php echo $tournaId; ?>">All</a></li>
                        <?php 
                        foreach($rounds as $r) {
                            $id = $r["id"];
                            $number = $r["number"];
                            echo "<li><a href=\"index.php?tourna_id=$tournaId&round_id=$id\">Round $number</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../teams/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Teams</a>
                    <ul class="dropdown">
                        <!-- All Teams of Selected Tourna -->
                        <li><a href="../teams/index.php?tourna_id=<?php echo $tournaId; ?>">All</a></li>
                        <?php
                        $teams = getTeams($tournaId, "id, name");
                        foreach($teams as $t) {
                            $id = $t["id"];
                            $name = htmlspecialchars($t["name"]);
                            echo "<li><a href=\"../teams/index.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
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
                    <h3 class="box">Round: </h3>
                    <div class="nav-item">
                        <h3 class="nav-btn"><a href="#">Round <?php echo $round["number"]; ?></a></h3>
                        <ul class="dropdown">
                            <li><a href="all.php?tourna_id=<?php echo $tournaId; ?>">All</a></li>
                            <?php
                            foreach($rounds as $r) {
                                $id = $r["id"];
                                $number = $r["number"];
                                echo "<li><a href=\"index.php?tourna_id=$tournaId&round_id=$id\">Round $number</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="box">
                    <?php
                    // Get status of round
                    $roundStatus = getTimeStatus($round["start_dt"], $round["end_dt"], $tourna["timezone"]);
                    echo "<h3>Round Status: $roundStatus</h3>";
                    ?>
                </div>
            </div>

            <!-- Match List -->
            <div class="table-box">
                <table class="box">
                    <tr>
                        <th></th>
                        <?php
                        // Allow assigning editor & date range when Ongoing
                        if($roundStatus == "Preparation") {
                            echo "<th>Team1</th>";
                            echo "<th>Team2</th>";
                            echo "<th>Start DateTime</th>";
                            echo "<th>End DateTime</th>";
                        }
                        else if($roundStatus == "Ongoing") {
                            echo "<th>Team1</th>";
                            echo "<th>Set as Winner</th>";
                            echo "<th>Team2</th>";
                            echo "<th>Set as Winner</th>";
                            echo "<th>Start DateTime</th>";
                            echo "<th>End DateTime</th>";
                            echo "<th title=\"Ends the match once end datetime is reached.\">Auto End</th>";
                        }
                        else {
                            echo "<th>Team1</th>";
                            echo "<th>Team2</th>";
                            echo "<th>Winner</th>";
                            echo "<th>Start DateTime</th>";
                            echo "<th>End DateTime</th>";
                        }
                        ?>
                    </tr>
                    <?php
                    // Get MatchList
                    $matches = getMatchList($roundId);
                    foreach($matches as $i => $m) {
                        echo "<tr>";
                        echo "<td>".($i + 1)."</td>";

                        $team1ID = $m["team1_id"];
                        $team1Name = $m["team1_name"];
                        $team2ID = $m["team2_id"];
                        $team2Name = $m["team2_name"];
                        
                        $winnerID = $m["winner_id"];
                        $winnerTeam = $m["winner_team"];
                        $loserID = $m["loser_id"];
                        $loserTeam = $m["loser_team"];

                        $startDt = $m["start_dt"];
                        $endDt = $m["end_dt"];
                        $autoEnd = $m["auto_end"];

                        if($roundStatus == "Preparation") {
                            $startDt = $startDt ?? "Not set";
                            $endDt = $endDt ?? "Not set";
                            
                            echo "<td>$team1Name</td>";
                            echo "<td>$team2Name</td>";
                            echo "<td>$startDt</td>";
                            echo "<td>$endDt</td>";
                        }
                        else if($roundStatus == "Ongoing") {
                            // Team1 setting for winner/loser
                            echo "<td>$team1Name</td>";
                            ?>
                            <td>
                                <form class="form-quick" action="curesult.php" method="post">
                                    <input type="hidden" name="round_id" value="<?php echo $m["round_id"]; ?>">
                                    <input type="hidden" name="match_id" value="<?php echo $m["id"]; ?>">
                                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                    <input type="hidden" name="loser_id" value="<?php echo $team2ID; ?>">
                                    <input type="hidden" name="winner_id" value="<?php echo $team1ID; ?>">
                                    <input type="hidden" name="timezone" value="<?php echo $tourna["timezone"]; ?>">
                                    <input onchange="this.form.submit()" type="radio" name="winner" id="winner<?php echo $i; ?>" value="<?php echo $team1ID; ?>" <?php echo ($team1ID == $winnerID? "checked":""); ?>>
                                </form>
                            </td>
                            <?php

                            // Team2 setting for winner/loser
                            echo "<td>$team2Name</td>";
                            ?>
                            <td>
                                <form class="form-quick" action="curesult.php" method="post">
                                    <input type="hidden" name="round_id" value="<?php echo $m["round_id"]; ?>">
                                    <input type="hidden" name="match_id" value="<?php echo $m["id"]; ?>">
                                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                    <input type="hidden" name="loser_id" value="<?php echo $team1ID; ?>">
                                    <input type="hidden" name="winner_id" value="<?php echo $team2ID; ?>">
                                    <input type="hidden" name="timezone" value="<?php echo $tourna["timezone"]; ?>">
                                    <input onchange="this.form.submit()" type="radio" name="winner" id="winner<?php echo $i; ?>" value="<?php echo $team2ID; ?>" <?php echo ($team2ID == $winnerID? "checked":""); ?>>
                                </form>
                            </td>
                            
                            <!-- DT & Auto End -->
                            <td>
                                <form class="form-quick" action="umatch.php" method="post">
                                    <input type="hidden" name="round_id" value="<?php echo $m["round_id"]; ?>">
                                    <input type="hidden" name="match_id" value="<?php echo $m["id"]; ?>">
                                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                    <input class="tbl-input" type="datetime" name="start_dt" id="start_dt" value="<?php echo $startDt; ?>" placeholder="00-00-00 00:00:00">
                                </form>
                            </td>
                            <td>
                                <form class="form-quick" action="umatch.php" method="post">
                                    <input type="hidden" name="round_id" value="<?php echo $m["round_id"]; ?>">
                                    <input type="hidden" name="match_id" value="<?php echo $m["id"]; ?>">
                                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                    <input class="tbl-input" type="datetime" name="end_dt" id="end_dt" value="<?php echo $endDt; ?>" placeholder="00-00-00 00:00:00">
                                </form>
                            </td>
                            <td>
                                <form class="form-quick" action="umatch.php" method="post">
                                    <input type="hidden" name="round_id" value="<?php echo $m["round_id"]; ?>">
                                    <input type="hidden" name="match_id" value="<?php echo $m["id"]; ?>">
                                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                    <label for="auto_end<?php echo $i; ?>" class="switch">
                                        <input type="checkbox" name="auto_end" id="auto_end<?php echo $i; ?>" onchange="this.form.submit()" <?php echo ($autoEnd? "checked":""); ?>>
                                        <div><div></div></div>
                                    </label>
                                </form>
                            </td>

                            <?php
                        }
                        else {
                            $winnerTeam = $winnerTeam ?? "No set winner.";

                            echo "<td>$team1Name</td>";
                            echo "<td>$team2Name</td>";
                            echo "<td>$winnerTeam</td>";
                            echo "<td>$startDt</td>";
                            echo "<td>$endDt</td>";
                        }

                        echo "</tr>";
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
            if(msg != null) setTimeout(() => msg.style.opacity = '0', 3000)

            // tbl-input must submit once lost focus
            const tblInputs = document.querySelectorAll('.tbl-input');

            tblInputs.forEach(function(input) {
                input.addEventListener('blur', function() {
                    
                    const parentForm = input.parentElement;
                    if (parentForm) {
                        // Submit the parent form
                        parentForm.submit();
                    }
                });
            });
        }
    </script>


</body>
</html>
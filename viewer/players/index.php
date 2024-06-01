<?php
require '../../funcs/tourna.php';
require '../../funcs/time.php';
require '../../funcs/round.php';
require '../../funcs/team.php';
require '../../funcs/player.php';

// Init tourna_id
$tournaId = isset($_POST["tourna_id"])? $_POST["tourna_id"] : NULL;
$tournaId = $tournaId ?? (isset($_GET["tourna_id"])? $_GET["tourna_id"] : NULL);
$tournaId = $tournaId ?? $tournas[0]["id"];

// Get Selected Tourna
$tourna = getTournaWithSetup($tournaId);
$tournaStatus = getTimeStatus($tourna["start_dt"], $tourna["end_dt"], $tourna["timezone"]);

// Get All Teams
$teams = getTeams($tournaId);

// Get Team Id
$teamId = isset($_POST["team_id"]) || isset($_GET["team_id"])? ($_POST["team_id"] ?? $_GET["team_id"]): NULL;
$teamId = $teamId ?? ($teams[0])["id"];
if($teamId == NULL) header("Location: ../teams/index.php?tourna_id=$tournaId");
$team = getTeam($teamId);

// No Selected Team, No Selected Player
$players = getPlayers($teamId);    // has team_id, team_name

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
                    <a href="../dashboard/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="../matches/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Matchlist</a>
                    <ul class="dropdown">
                        <!-- Tournament Matchlist -->
                        <?php 
                        $rounds = getRounds($tournaId);
                        foreach($rounds as $round) {
                            $id = $round["id"];
                            $number = $round["number"];
                            echo "<li><a href=\"../matches/index.php?tourna_id=$tournaId&round_id=$id\">Round $number</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../teams/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Teams</a>
                    <ul class="dropdown">
                        <!-- All Teams of Selected Tourna -->
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
                    <a href="../players/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn selected">Players</a>
                    <ul class="dropdown">
                        <!-- All Teams of Selected Tourna -->
                        <?php
                        foreach($teams as $t) {
                            $id = $t["id"];
                            $name = htmlspecialchars($t["name"]);
                            echo "<li><a href=\"../teams/index.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
                        }
                        ?>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="auth-box"></div>
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
                        <th>Score</th>
                        <th>Wins</th>
                        <th>Loses</th>
                    </tr>
                    <?php
                    foreach($players as $i => $p) {
                        echo "<tr>";
                        
                        $teamName = $team["name"];
                        $id = $p["id"];
                        $name = $p["name"];
                        $email = $p["email"];
                        $contact = $p["contact_no"];
                        
                        echo "<td>".($i + 1)."</td>";
                        echo "<td>$teamName</td>";
                        echo "<td>$name</td>";
                        
                        $score = $p["score"];
                        $wins = $p["wins"];
                        $loses = $p["loses"];

                        echo "<td>$score</td>";
                        echo "<td>$wins</td>";
                        echo "<td>$loses</td>";

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
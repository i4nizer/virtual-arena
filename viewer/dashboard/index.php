<?php
require '../../funcs/tourna.php';
require '../../funcs/time.php';
require '../../funcs/round.php';
require '../../funcs/team.php';
require '../../funcs/player.php';

// Init tourna_id
$tournaId = isset($_POST["tourna_id"])? $_POST["tourna_id"] : NULL;
$tournaId = $tournaId ?? (isset($_GET["tourna_id"])? $_GET["tourna_id"] : NULL);
if($tournaId == NULL) header("Location: ../../auth/signin.php");

// Get Viewed Tourna
$tourna = getTournaWithCount($tournaId);
$tournaStatus = getTimeStatus($tourna["start_dt"], $tourna["end_dt"], $tourna["timezone"]);

// Teams
$teams = getTeams($tournaId, "id, name");

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
                    <a href="#" class="nav-btn selected">Dashboard</a>
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
                    <a href="../players/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Players</a>
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
                <div class="box">
                    <h3>Status: <?php echo $tournaStatus; ?></h3>
                </div>
                <div class="box">
                    <h3>Rounds: <?php echo $tourna["round_count"]; ?></h3>
                </div>
                <div class="box">
                    <h3>Matches: <?php echo $tourna["match_count"]; ?></h3>
                </div>
                <div class="box">
                    <h3>Teams: <?php echo $tourna["team_count"]; ?></h3>
                </div>
                <div class="box">
                    <h3>Players: <?php echo $tourna["player_count"]; ?></h3>
                </div>
            </div>

            <div class="head-box">
                <h3 class="box">Participating Teams</h3>
            </div>
            <div class="table-box">
                <table class="box">
                    <tr>
                        <th></th>
                        <th>Team</th>
                        <th>Players</th>
                        <th>Score</th>
                        <th>Wins</th>
                        <th>Loses</th>
                    </tr>
                    <?php
                    $teams = getTeamsWithCount($tournaId);
                    foreach($teams as $i => $t) {
                        echo "<tr>";
                        $name = $t["name"];
                        $score = $t["score"];
                        $wins = $t["wins"];
                        $loses = $t["loses"];
                        echo "<td>".($i + 1)."</td>";
                        echo "<td>$name</td>";

                        echo "<td>";
                        $players = getPlayers($t["id"]);
                        $pLen = count($players);
                        for($i = 0; $i < $pLen; $i++) {
                            $p = $players[$i];
                            echo $i < $pLen - 1 && $pLen > 1? $p["name"]." - " : $p["name"];
                        }
                        if(empty($players)) echo "Team doesn't have players yet.";
                        echo "</td>";
                        
                        echo "<td>$score</td>";
                        echo "<td>$wins</td>";
                        echo "<td>$loses</td>";
                        echo "</tr>";
                    }
                    if(empty($teams)) echo "<td colspan=\"5\">No participating teams yet.</td>";
                    ?>
                </table>
            </div>

            

        </div>
    </main>
    <!-- Main Content -->

    



    <script>
        window.onload = async function () {

            // Notif fading
            const msg = document.getElementById('msg')
            if(msg != null) setTimeout(() => msg.style.opacity = '0', 3000)

        }
    </script>





</body>
</html>
<?php
require '../../funcs/tourna.php';
require '../../funcs/time.php';
require '../../funcs/round.php';
require '../../funcs/team.php';
require '../../funcs/player.php';
require '../../funcs/match.php';

// Init tourna_id
$tournaId = isset($_POST["tourna_id"])? $_POST["tourna_id"] : NULL;
$tournaId = $tournaId ?? (isset($_GET["tourna_id"])? $_GET["tourna_id"] : NULL);
if($tournaId == NULL) header("Location: ../../auth/signin.php");

// Get Viewed Tourna
$tourna = getTournaWithCount($tournaId);
$tournaStatus = getTimeStatus($tourna["start_dt"], $tourna["end_dt"], $tourna["timezone"]);

// Get Rounds
$rounds = getRounds($tournaId);
if(empty($rounds)) header("Location: ../tournaments/index.php?tourna_id=$tournaId&msg=No rounds yet.&msg_state=failed");

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
                    <a href="../dashboard/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="../matches/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn selected">Matchlist</a>
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
                <div class="nav-box">
                    <h3 class="box">Round: </h3>
                    <div class="nav-item">
                        <h3 class="nav-btn"><a href="#">Round <?php echo $round["number"]; ?></a></h3>
                        <ul class="dropdown">
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
            </div>

            <div class="table-box">
                <table class="box">
                    <tr>
                        <th></th>
                        <th>Round</th>
                        <th>Team1</th>
                        <th>Team2</th>
                        <th>Start DateTime</th>
                        <th>End DateTime</th>
                    </tr>
                    <?php 
                    // Get All Rounds
                    foreach($rounds as $ir => $r) {
                        $roundId = $r["id"];
                        $roundNumber = $r["number"];

                        // Get All Matchlist of the Round
                        $matches = getMatchList($roundId);
                        foreach($matches as $im => $m) {
                            echo "<tr>";
                            echo "<td>".($im + 1)."</td>";

                            $team1Name = $m["team1_name"];
                            $team2Name = $m["team2_name"];
                            $startDt = $m["start_dt"];
                            $startDt = $startDt ?? "Not set";
                            $endDt = $m["end_dt"];
                            $endDt = $endDt ?? "Not set";

                            echo "<td>$roundNumber</td>";
                            echo "<td>$team1Name</td>";
                            echo "<td>$team2Name</td>";
                            echo "<td>$startDt</td>";
                            echo "<td>$endDt</td>";

                            echo "</tr>";
                        }

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
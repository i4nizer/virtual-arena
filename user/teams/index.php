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

// Check Team Id
$teamId = isset($_POST["team_id"]) || isset($_GET["team_id"])? ($_POST["team_id"] ?? $_GET["team_id"]): NULL;
if($teamId != NULL) header("Location: edit.php?tourna_id=$tournaId&team_id=$teamId");

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
                    <a href="#" class="nav-btn selected">Teams</a>
                    <ul class="dropdown">
                        <!-- All Teams of Selected Tourna -->
                        <?php
                        $teams = getTeams($tournaId, "id, name");
                        foreach($teams as $t) {
                            $id = $t["id"];
                            $name = htmlspecialchars($t["name"]);
                            echo "<li><a href=\"edit.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
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
                        <h3 class="nav-btn"><a href="#">All</a></h3>
                        <ul class="dropdown">
                            <?php
                            foreach($teams as $t) {
                                $id = $t["id"];
                                $name = $t["name"];
                                echo "<li><a href=\"edit.php?tourna_id=$tournaId&team_id=$id\">$name</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <?php 
            // Show add Team when below limit
            if($tournaStatus == "Preparation" && (empty($teams) || count($teams) < $tourna["max_entry"])) {
                ?>
                <div class="form-box box">
                    <form action="cteam.php" method="post">
                        <h3>Add Team</h3>
                        <div class="form-field">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" placeholder="Team name"> 
                        </div>
                        <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                        <input type="submit" name="create_team" value="Add">
                    </form>
                </div>
                <?php
            }
            ?>

            <div class="table-box">
                <table class="box">
                    <tr>
                        <th></th>
                        <th>Team</th>
                        <th>Players</th>
                        <?php
                        if($tournaStatus != "Preparation") {
                            echo "<th>Score</th>";
                            echo "<th>Wins</th>";
                            echo "<th>Loses</th>";
                            echo "<th>Edit</th>";
                        }
                        else {
                            echo "<th>Edit</th>";
                            echo "<th>Remove</th>";
                        }
                        ?>
                    </tr>
                    <?php
                    $teams = getTeamsWithCount($tournaId);
                    foreach($teams as $i => $t) {
                        echo "<tr>";
                        $id = $t["id"];
                        $name = $t["name"];
                        echo "<td>".($i + 1)."</td>";
                        echo "<td>$name</td>";
                        
                        echo "<td>";
                        $players = getPlayers($t["id"], "name");
                        $pLen = count($players);
                        
                        for($i = 0; $i < $pLen; $i++) {
                            $p = $players[$i];
                            echo $i < $pLen - 1 && $pLen > 1? $p["name"]." - " : $p["name"];
                        }
                        
                        if(empty($players)) echo "Team doesn't have players yet.";
                        echo "</td>";
                        
                        if($tournaStatus != "Preparation") {
                            $score = $t["score"];
                            $wins = $t["wins"];
                            $loses = $t["loses"];
                            
                            echo "<td>$score</td>";
                            echo "<td>$wins</td>";
                            echo "<td>$loses</td>";
                            echo "<td><a href=\"edit.php?tourna_id=$tournaId&team_id=$id\">Edit</a></td>";
                        }
                        else {
                            echo "<td><a href=\"edit.php?tourna_id=$tournaId&team_id=$id\">Edit</a></td>";
                            ?>
                            <td>
                                <form action="dteam.php" method="post">
                                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                    <input type="hidden" name="team_id" value="<?php echo $id; ?>">
                                    <input class="danger" type="submit" name="delete_team" value="Remove">
                                </form>
                            </td>
                            <?php   
                        }
                        echo "</tr>";
                    }
                    if(empty($teams)) echo "<td colspan=\"100%\">No participating teams yet.</td>";
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
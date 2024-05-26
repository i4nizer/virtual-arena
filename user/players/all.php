<?php
require '../../funcs/player.php';
require '../../funcs/team.php';
require '../../funcs/tourna.php';

// Check if Logged In
session_start();
if(!isset($_SESSION["user_id"])) header("Location: ../../auth/signin.php");

$userId = $_SESSION["user_id"];
$userName = $_SESSION["username"];

// Redirect if there is no tourna_id
if( !(isset($_POST["tourna_id"]) || isset($_GET["tourna_id"])) ) {
    header("Location: ../dashboard/index.php");
    exit();
}

// Get All Tournaments of the user
$tournas = getTournas($userId, "id, title");     // title, id

// Get tourna_id
$tournaId = isset($_POST["tourna_id"])? $_POST["tourna_id"] : $_GET["tourna_id"];
if($tournaId == "new") {
    header("Location: ../dashboard/ctourna.php");
    exit();
}

// Get Selected Tournament of the user *
$selectedTourna = getTourna($userId, $tournaId);
if(empty($selectedTourna)) header("Location: ../dashboard/ctourna.php");

// Get tourna status
$tournaStatus = getTimeStatus($selectedTourna["start_dt"], $selectedTourna["end_dt"], $selectedTourna["timezone"]);
if($tournaStatus != "Preparation") {
    header("Location: view.php?tourna_id=$tournaId&team_id=$teamId");
    exit();
}

// Get All Teams
$teams = getTeams($tournaId);

// Get All Players
$players = getAllPlayers($tournaId);



// Notifier
$msg = "";
$msgState = "";





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
                    <a href="../dashboard/index.php" class="nav-btn">Tournaments</a>
                    <ul class="dropdown">
                        <!-- Tournament List -->
                        <?php 
                        foreach($tournas as $tourna) {
                            $id = $tourna["id"];
                            $title = htmlspecialchars($tourna["title"]);
                            echo "<li><a href=\"../dashboard/index.php?tourna_id=$id\">$title</a></li>";
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
                    <a href="#" class="nav-btn selected">Players</a>
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
            <?php if($msg != "") echo "<div class=\"msg $msgState\">$msg</div>"; ?>
            
            <!-- Player List Table mixed with Team Name -->
                <div class="table-box box">
                    <table>
                        <tr>
                            <th>Team Name</th>
                            <th>Player Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Score</th>
                            <th>Wins</th>
                            <th>Loses</th>
                            <th>Edit</th>
                            <th>Remove</th>
                        </tr>
                    <?php if($players) {
                            // Loop through
                            foreach($players as $player) { ?>
                                
                                <tr>
                                    <td><?php echo $player["team_name"]; ?></td>
                                    <td><?php echo $player["name"]; ?></td>
                                    <td><?php echo $player["email"]; ?></td>
                                    <td><?php echo $player["contact_no"]; ?></td>
                                    <td><?php echo $player["score"]; ?></td>
                                    <td><?php echo $player["wins"]; ?></td>
                                    <td><?php echo $player["loses"]; ?></td>
                                    <td><a href="edit.php?team_id=<?php echo $player["team_id"]; ?>&player_id=<?php echo $player["id"]; ?>&tourna_id=<?php echo $tournaId; ?>">Edit</a></td>
                                    <td>
                                        <form class="form-quick" action="" method="post">
                                            <input type="hidden" name="player_id" value="<?php echo $player["id"]; ?>">
                                            <input type="submit" name="remove_player" value="Remove" class="danger">
                                        </form>
                                    </td>
                                </tr>

                            <?php }
                        } // has players
                        // no players
                        else { ?> <tr> <td colspan="9">No Players Found</td> </tr> <?php }
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

<?php
require '../../funcs/tourna.php';
require '../../funcs/player.php';
require '../../funcs/team.php';

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

// Get team_id
$teamId = isset($_POST["team_id"])? $_POST["team_id"] : NULL;
$teamId = $teamId ?? isset($_GET["team_id"])? $_GET["team_id"] : NULL;
if($teamId == NULL) {
    header("Location: all.php?tourna_id=$tournaId");
    exit();
}

// Get player_id
$playerId = isset($_POST["player_id"])? $_POST["player_id"] : NULL;
$playerId = $playerId ?? isset($_GET["player_id"])? $_GET["player_id"] : NULL;
if($playerId == NULL) {
    header("Location: index.php?tourna_id=$tournaId&team_id=$teamId");
    exit();
}
$player = getPlayer($playerId);

// Get Selected Tournament of the user *
$selectedTourna = getTourna($userId, $tournaId);
if(empty($selectedTourna)) header("Location: ../dashboard/ctourna.php");

// Get tourna status
$tournaStatus = getTimeStatus($selectedTourna["start_dt"], $selectedTourna["end_dt"], $selectedTourna["timezone"]);
if($tournaStatus != "Preparation") {
    header("Location: view.php?tourna_id=$tournaId&team_id=$teamId");
    exit();
}

// Get Teams
$teams = getTeams($tournaId);

// Get Players
$playerLimit = $selectedTourna["max_entry_player"];
$players = getPlayers($teamId);



// Notifier
$msg = "";
$msgState = "";



// Updates player
if(isset($_POST["update_player"])) {
    // Success
    if(updatePlayer($_POST["player_id"], $_POST["name"], $_POST["email"], $_POST["contact_no"], $_POST["score"], $_POST["wins"], $_POST["loses"])) {
        $msg = "Player updated successfully.";
        $msgState = "success";
        header("Location: index.php?tourna_id=$tournaId&team_id=$teamId");
    }
    // Fail
    else {
        $msg = "An error occured, failed to update player.";
        $msgState = "failed";
    }
}
// Remove player
else if(isset($_POST["remove_player"])) {
    // Success
    if(removePlayer($_POST["player_id"])) {
        $msg = "Player removed successfully.";
        $msgState = "success";
    }
    // Fail
    else {
        $msg = "An error occured, failed to remove player.";
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
                    <a href="all.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn selected">Players</a>
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
            
            <!-- Edit Player Data -->
            <div class="form-box box">
                <form action="" method="post">
                    <h3>Edit Player</h3>
                    <div class="form-field">
                        <label for="name">Player Name</label>
                        <input type="text" name="name" id="name" max="255" placeholder="Name" value="<?php echo $player["name"]; ?>">
                    </div>
                    <div class="form-field">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" max="255" placeholder="Email" value="<?php echo $player["email"]; ?>">
                    </div>
                    <div class="form-field">
                        <label for="contact_no">Contact</label>
                        <input type="tel" name="contact_no" id="contact_no" max="255" placeholder="Contact number" value="<?php echo $player["contact_no"]; ?>">
                    </div>
                    <div class="form-field">
                        <label for="score">Score</label>
                        <input type="number" name="score" id="score" max="255" placeholder="Score" value="<?php echo $player["score"]; ?>">
                    </div>
                    <div class="form-field">
                        <label for="wins">Wins</label>
                        <input type="number" name="wins" id="wins" max="255" placeholder="Wins" value="<?php echo $player["wins"]; ?>">
                    </div>
                    <div class="form-field">
                        <label for="loses">Loses</label>
                        <input type="number" name="loses" id="loses" max="255" placeholder="Loses" value="<?php echo $player["loses"]; ?>">
                    </div>
                    <input type="hidden" name="player_id" value="<?php echo $playerId; ?>">
                    <input type="hidden" name="team_id" value="<?php echo $teamId; ?>">
                    <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                    <input type="submit" name="update_player" value="Save">
                </form>
            </div>

            <!-- Player List Table mixed with Team Name -->
            <div class="table-box box">
                <table>
                    <tr>
                        <th>Team</th>
                        <th>Player</th>
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
                                <td><a href="edit.php?team_id=<?php echo $teamId; ?>&player_id=<?php echo $player["id"]; ?>&tourna_id=<?php echo $tournaId; ?>">Edit</a></td>
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

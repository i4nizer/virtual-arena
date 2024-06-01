<?php
require '../../funcs/tourna.php';
require '../../funcs/time.php';
require '../../funcs/round.php';
require '../../funcs/team.php';

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

// Notif
$msg = isset($_GET["msg"])? $_GET["msg"] : "";
$msgState = isset($_GET["msg_state"])? $_GET["msg_state"] : "";

// Target tourna to edit
$tourna = getTournaWithSetup($tournaId);
if(empty($tourna)) header("Location: index.php?tourna_id=$tournaId&msg=No tourna with such id.&msg_state=failed");




?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/table.css">
    <title>Edit - <?php echo $tourna["title"]; ?></title>
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
                    <a href="index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn selected">Tournaments</a>
                    <ul class="dropdown">
                        <li><a href="index.php?category=all&tourna_id=<?php echo $tournaId; ?>">All</a></li>
                        <li><a href="index.php?category=preparation&tourna_id=<?php echo $tournaId; ?>">Preparation</a></li>
                        <li><a href="index.php?category=ongoing&tourna_id=<?php echo $tournaId; ?>">Ongoing</a></li>
                        <li><a href="index.php?category=ended&tourna_id=<?php echo $tournaId; ?>">Ended</a></li>
                        <li><a href="create.php">Create New</a></li>
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
                        <li><a href="../teams/index.php?tourna_id=<?php echo $tournaId; ?>&team_id=">All</a></li>
                        <?php
                        $teams = getTeams($tournaId, "id, name");
                        foreach($teams as $team) {
                            $id = $team["id"];
                            $name = htmlspecialchars($team["name"]);
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

            <!-- Update Tourna -->
            <div class="form-box box">
                <form action="utourna.php" method="post">
                    <h3>Edit Tournament</h3>
                    <div class="col-box">
                        <div class="form-field">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" max="255" placeholder="Tournament title" value="<?php echo $tourna["title"]; ?>">
                        </div>
                        <div class="form-field">
                            <label for="format">Format</label>
                            <select name="format" id="format">
                                <option value="Single Elimination" <?php echo $tourna["format"] == "Single Elimination"? "selected":""; ?>>Single Elimination</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="timezone">Tournament Timezone</label>
                            <select name="timezone" id="timezone">
                                <?php 
                                $timezones = DateTimeZone::listIdentifiers();
                                if($timezones) foreach($timezones as $timezone) {
                                    $selected = $tourna["timezone"] == $timezone? "selected" : "";
                                    echo "<option value=\"$timezone\" $selected>$timezone</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="max_entry">Max Entries</label>
                            <input type="number" name="max_entry" id="max_entry" max="1000" placeholder="0" value="<?php echo $tourna["max_entry"]; ?>">
                        </div>
                        <div class="form-field">
                            <label for="start_dt">Start DateTime</label>
                            <input type="datetime" name="start_dt" id="start_dt" placeholder="00-00-00 00:00:00" value="<?php echo $tourna["start_dt"]; ?>">
                        </div>
                        <div class="form-field">
                            <label for="max_entry_player">Players per Entry</label>
                            <input type="number" name="max_entry_player" id="max_entry_player" max="1000" placeholder="0" value="<?php echo $tourna["max_entry_player"]; ?>">
                        </div>
                        <div class="form-field">
                            <label for="end_dt">End DateTime</label>
                            <input type="datetime" name="end_dt" id="end_dt" placeholder="00-00-00 00:00:00" value="<?php echo $tourna["end_dt"]; ?>">
                        </div>
                        <div class="form-field">
                            <label for="pairing">Pairing</label>
                            <select name="pairing" id="pairing">
                                <option value="Random" <?php echo $tourna["pairing"] == "Random"? "selected":""; ?>>Random</option>
                                <option value="Order" <?php echo $tourna["pairing"] == "Order"? "selected":""; ?>>Order</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" placeholder="Brief description"><?php echo $tourna["description"]; ?></textarea>
                        </div>
                        <div class="form-field">
                            <div class="form-field">
                                <label for="is_public" class="switch">
                                    <input type="checkbox" name="is_public" id="is_public" <?php echo $tourna["is_public"]? "checked":""; ?>>
                                    <div><div></div></div>
                                    <span>Public</span>
                                </label>
                            </div>
                            <div class="form-field">
                                <label for="is_open" class="switch">
                                    <input type="checkbox" name="is_open" id="is_open" <?php echo $tourna["is_open"]? "checked":""; ?>>
                                    <div><div></div></div>
                                    <span>Allow registration</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="tourna_id" value="<?php echo $tourna["tourna_id"]; ?>">
                    <input type="submit" name="update_tourna" value="Save">
                </form>
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
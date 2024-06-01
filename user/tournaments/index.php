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

// Get Tournament Category
$category = isset($_GET["category"])? $_GET["category"] : NULL;
$category = $category ?? "all";

// Get Categorize tourna
$catTournas = $category == "all"? $tournas : array_filter($tournas, function($t) {
    $startDt = new DateTime($t["start_dt"], new DateTimeZone($t["timezone"]));
    $endDt = new DateTime($t["end_dt"], new DateTimeZone($t["timezone"]));
    $currentDt = new DateTime('now', new DateTimeZone($t["timezone"]));

    GLOBAL $category;
    if($category == "ended") return $currentDt >= $endDt;
    else if($category == "ongoing") return $currentDt >= $startDt && $currentDt < $endDt;
    else return $currentDt < $startDt;
});

// Init tourna_id
$tournaId = isset($_POST["tourna_id"])? $_POST["tourna_id"] : NULL;
$tournaId = $tournaId ?? (isset($_GET["tourna_id"])? $_GET["tourna_id"] : NULL);
$tournaId = $tournaId ?? $tournas[0]["id"];

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
                    <a href="#" class="nav-btn selected">Tournaments</a>
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

            <div class="head-box">
                <div class="nav-box">
                    <h3>Category:</h3>
                    <div class="nav-item">
                        <h3 class="nav-btn"><?php echo ucfirst($category); ?></h3>
                        <ul class="dropdown">
                            <li><a href="index.php?category=all&tourna_id=<?php echo $tournaId; ?>">All</a></li>
                            <li><a href="index.php?category=preparation&tourna_id=<?php echo $tournaId; ?>">Preparation</a></li>
                            <li><a href="index.php?category=ongoing&tourna_id=<?php echo $tournaId; ?>">Ongoing</a></li>
                            <li><a href="index.php?category=ended&tourna_id=<?php echo $tournaId; ?>">Ended</a></li>
                            <li><a href="create.php">Create New</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tournament List -->
            <div class="table-box">
                <table class="box">
                    <tr>
                        <th></th>
                        <th>Tournament</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                        <th>View</th>
                        <th>Link</th>
                    </tr>
                    <?php
                    foreach($catTournas as $i => $ct) {
                        echo "<tr>";
                        
                        $id = $ct["id"];
                        $title = $ct["title"];
                        $desc = $ct["description"];
                        $status = getTimeStatus($ct["start_dt"], $ct["end_dt"], $ct["timezone"]);
                        
                        echo "<td>".($i + 1)."</td>";
                        echo "<td>$title</td>";
                        echo "<td>$desc</td>";
                        echo "<td>$status</td>";
                        echo "<td><a href=\"edit.php?tourna_id=$id\">Edit</a></td>";
                        
                        ?>
                        <td>
                            <form class="form-quick" action="dtourna.php" method="post">
                                <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                <input class="danger" type="submit" name="delete_tourna" value="Delete">
                            </form>
                        </td>
                        <?php

                        echo "<td><a href=\"../../viewer/dashboard/index.php?tourna_id=$tournaId\">View</a></td>";
                        echo "<td><a class=\"copy-link\" href=\"localhost/virtual-arena/viewer/dashboard/index.php?tourna_id=$tournaId\">Copy</a></td>";
                        echo "</tr>";
                    }
                    if(empty($catTournas)) echo "<tr><td colspan=\"5\">No $category tournaments</td></tr>";
                    ?>
                </table>
            </div>

        </div>
    </main>
    <!-- Main Content -->

    

    <script>
        window.onload = async function() {
            console.log("DOM Loaded")
            
            const msg = document.getElementById('msg')
            if(msg != null) setTimeout(() => msg.style.opacity = '0', 3000)

            // Select all elements with the class 'copy-link'
            const copyLinks = document.querySelectorAll('.copy-link');

            // Attach event listener to each 'copy-link' element
            copyLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const linkUrl = this.getAttribute('href');
                    
                    // Create a temporary input element
                    const tempInput = document.createElement('input');
                    tempInput.value = linkUrl;
                    document.body.appendChild(tempInput);
                    
                    // Select the text and copy it to clipboard
                    tempInput.select();
                    document.execCommand('copy');
                    
                    // Remove the temporary input element
                    document.body.removeChild(tempInput);
                    
                    alert('Link copied to clipboard: ' + linkUrl);
                });
            });

        }
    </script>


</body>
</html>
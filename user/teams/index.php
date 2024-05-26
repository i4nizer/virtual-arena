<?php
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
if($tournaId == "new") header("Location: ../dashboard/ctourna.php");

// Get Selected Tournament of the user *
$selectedTourna = getTourna($userId, $tournaId);
if(empty($selectedTourna)) header("Location: ../dashboard/ctourna.php");

// Get tourna status
$tournaStatus = getTimeStatus($selectedTourna["start_dt"], $selectedTourna["end_dt"], $selectedTourna["timezone"]);
if($tournaStatus != "Preparation") {
    header("Location: view.php?tourna_id=$tournaId");
    exit();
}

// Get Teams
$teamLimit = $selectedTourna["max_entry"];
$teams = getTeams($tournaId);


// Notifier
$msg = "";
$msgState = "";



// Creates a Team
if(isset($_POST["add_team"]) && count($teams) < $teamLimit) {
    // Success
    if(createTeam($_POST["name"], $_POST["tourna_id"])) {
        $msg = "Team created successfully.";
        $msgState = "success";
    }
    // Fail
    else {
        $msg = "An error occured, failed to create team.";
        $msgState = "failed";
    }
}
// Remove Team
else if(isset($_POST["remove_team"])) {
    // Success
    if(removeTeam($_POST["team_id"])) {
        $msg = "Team removed successfully.";
        $msgState = "success";
    }
    // Fail
    else {
        $msg = "An error occured, failed to remove team.";
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
                    <a href="#" class="nav-btn selected">Teams</a>
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
                    <a href="../players/index.php?tourna_id=<?php echo $tournaId; ?>" class="nav-btn">Players</a>
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
            <?php
            // Watch Team Limit (max_entry) 
            if(empty($teams) || count($teams) < $teamLimit) { ?>
                <!-- Add Team Form -->
                <div class="form-box box">
                    <form action="" method="post">
                        <h3>Add Team</h3>
                        <div class="form-field">
                            <label for="name">Team Name</label>
                            <input type="text" name="name" id="name" max="255" placeholder="Team name">
                        </div>
                        <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                        <input type="submit" name="add_team" value="Add Team">
                    </form>
                </div>
            <?php } ?>

            <!-- Team List Table -->
            <div class="table-box box">
                <table>
                    <tr>
                        <th>Team</th>
                        <th>Players</th>
                        <th>Remove</th>
                    </tr>
                <?php if($teams) {
                        // Loop through
                        foreach($teams as $team) { ?>
                            
                            <tr>
                                <td><?php echo $team["name"]; ?></td>
                                <td>   <a href="../players/index.php?team_id=<?php echo $team["id"]; ?>&tourna_id=<?php echo $tournaId; ?>">Manage</a>   </td>
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="team_id" value="<?php echo $team["id"]; ?>">
                                        <input type="hidden" name="tourna_id" value="<?php echo $tournaId; ?>">
                                        <input type="submit" name="remove_team" value="Remove" class="danger">
                                    </form>
                                </td>
                            </tr>

                        <?php }
                    } // has teams
                    // no teams
                    else { ?> <tr> <td colspan="3">No Teams Found</td> </tr> <?php }
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

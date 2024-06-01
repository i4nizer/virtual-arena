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

// Get Viewed Tourna
$tourna = getTournaWithCount($tournaId);
$tournaStatus = getTimeStatus($tourna["start_dt"], $tourna["end_dt"], $tourna["timezone"]);

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
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
                    <a href="#" class="nav-btn selected">Dashboard</a>
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

            <div class="chart-box">
                <canvas id="myChart" width="1000px" height="500px"></canvas>
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


            // Get the context of the canvas element we want to select
            const chart = document.getElementById('myChart')
            const ctx = chart.getContext('2d')
            
            
            // Fetch and store cdt
            let teamCountPerDT = []
            let playerCountPerDT = []

            // Get Team & Player Stats
            await fetch('stats-api.php?tourna_id=<?php echo $tournaId; ?>&req=team-player-dt')
                .then(res => res.json())
                .then(data => {
                    teamCountPerDT = data.team
                    playerCountPerDT = data.player
                })
            .catch(err => console.log(err))
            
            // Sort by cdt
            if(teamCountPerDT.length <= 0 && playerCountPerDT.length <= 0) {
                chart.parentElement.remove()
                return
            }
            teamCountPerDT.sort((a, b) => { return (new Date(a.team_cdt)) - (new Date(b.team_cdt)) })
            playerCountPerDT.sort((a, b) => { return (new Date(a.player_cdt)) - (new Date(b.player_cdt)) })
            
            // Get Min Max 
            const currDT = new Date(Date.now())
            const teamMinDT = teamCountPerDT.length == 0? currDT : new Date(teamCountPerDT[0].team_cdt)
            const teamMaxDT = teamCountPerDT.length == 0? currDT : new Date(teamCountPerDT[teamCountPerDT.length - 1].team_cdt)
            const playerMinDT = playerCountPerDT.length == 0? currDT : new Date(playerCountPerDT[0].player_cdt)
            const playerMaxDT = playerCountPerDT.length == 0 ? currDT : new Date(playerCountPerDT[playerCountPerDT.length - 1].player_cdt)
            const minDT = teamMinDT < playerMinDT ? teamMinDT : playerMinDT
            const maxDT = teamMaxDT < playerMaxDT ? teamMaxDT : playerMaxDT

            // Generate date labels incrementing days
            let dtLbl = []
            let i = minDT.getTime();
            for (; i < maxDT.getTime(); i += 86400000) {   // increments 24hrs | 1day
                dtLbl.push(new Date(i))
            }

            // Get data from team & player
            let teamData = []
            let totalTeams = 0
            teamCountPerDT.forEach(t => {
                totalTeams += t.team_count
                teamData.push({ x: t.team_cdt, y: totalTeams })
            })

            let playerData = []
            let totalPlayers = 0
            playerCountPerDT.forEach(p => {
                totalPlayers += p.player_count
                playerData.push({ x: p.player_cdt, y: totalPlayers })
            })


            const data = {
                labels: dtLbl,
                datasets: [{
                    label: 'Team Entry',
                    backgroundColor: 'rgba(0, 184, 147, 0.5)',
                    borderColor: 'rgb(0, 184, 147)',
                    fill: false,
                    data: teamData
                }, {
                    label: 'Player Entry',
                    backgroundColor: 'rgba(135, 206, 250, 0.5)',
                    borderColor: 'lightskyblue',
                    fill: false,
                    data: playerData
                }]
            };

            const config = {
                type: 'line',
                data: data,
                options: {
                    plugins: {
                        title: {
                            text: 'Team and Player Entry Over Time',
                            color: 'white',
                            font: {
                                size: '16px'
                            },
                            display: true
                        },
                        legend: {
                            labels: {
                                color: 'white'
                            }
                        }
                    },
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                tooltipFormat: 'MM-dd-yyyy hh:mm a'
                            },
                            title: {
                                display: true,
                                text: 'Date',
                                color: 'white'
                            },
                            ticks: {
                                color: 'white'
                            },
                            grid: {
                                color: 'gray'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Team/Player Count',
                                color: 'white'
                            },
                            ticks: {
                                color: 'white'
                            },
                            grid: {
                                color: 'gray'
                            }
                        }
                    }
                }
            };

            
            // Create a new Chart instance
            new Chart(ctx, config);

        }
    </script>





</body>
</html>
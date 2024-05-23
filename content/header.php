<?php
// Check if Logged In
$signedIn = isset($_GET["signed"]) && isset($_SESSION["user_id"]);
$tournaId = isset($_GET["tourna_id"])? $_GET["tourna_id"] : NULL;
$tournaTitle = isset($_GET["title"])? $_GET["title"] : NULL;


// --------------------------> Logged In
if($signedIn) {
    // Need funcs
    require_once "../pdo.php";

    // Get All Tournaments
    $userId = $_SESSION["user_id"];
    $userName = $_SESSION["name"];
    $tournas = getTourna($userId);
?>

    <!-- Provides header and -> $signedIn & $tournaId -->
    <header>
        <div class="header-row">
            <div class="header-section">
                <h3>Virtual Arena</h3>
                <?php
                // Has tournas
                if($tournas) {
                    ?> <select name="tournament" id="tournament"> <?php

                    // Create options
                    $index = 0;
                    foreach($tournas as $tourna) {
                        $title = $tourna["title"];
                        $id = $tourna["id"];
                        
                        // Get first tourna
                        if($index++ == 0) {
                            $tournaId = $id;
                            $tournaTitle = $title;
                        }

                        ?> <option value="<?php echo $id; ?>">
                            <a href="/index.php?tourna_id=<?php echo $id; ?>"><?php echo $title; ?></a>
                        </option><?php
                    }

                    ?> </select> <?php
                }
                ?>
                </select>
            </div>
            <div class="header-section">
                <h4><?php echo $userName; ?></h4>
                <a class="btn" href="../auth/signout.php">Sign Out</a>
            </div>
        </div>
        <?php
        // Show navs if there are tournas & tournaID
        if($tournas) { ?>
            
        <div class="header-row">
            <div class="header-nav">
                <a href="dashboard.php?tourna_id=<?php echo $tournaId; ?>">Dashboard</a>
                <a href="../view-tourna/index.php?tourna_id=<?php echo $tournaId; ?>" target="_blank">View</a>
                <a href="players.php?tourna_id=<?php echo $tournaId; ?>">Players</a>
                <a href="matches.php?tourna_id=<?php echo $tournaId; ?>">Matches</a>
                <a href="results.php?tourna_id=<?php echo $tournaId; ?>">Results</a>
                <a href="contact.php?tourna_id=<?php echo $tournaId; ?>">Contact</a>
            </div>
        </div>

        <?php }
        ?>
    </header>

<?php }
// --------------------------> Not Logged In
else { ?>

    <header>
        <div class="header-row">
            <div class="header-section">
                <h3>Virtual Arena</h3>
                <?php echo "<h4>".$tournaTitle."</h4>"; ?>
            </div>
            <div class="header-section h-section">
                <a href="auth/signin.php">Sign In</a>
                <a href="auth/signup.php">Sign Up</a>
            </div>
        </div>
    </header>

<?php }
?>


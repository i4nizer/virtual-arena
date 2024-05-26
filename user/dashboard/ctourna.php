<?php
require '../../funcs/tourna.php';

// Check if Logged In
session_start();
if(!isset($_SESSION["user_id"])) header("Location: ../../auth/signin.php");

$userId = $_SESSION["user_id"];
$userName = $_SESSION["username"];

// Get All Tournaments of the user
$tournas = getTournas($userId, "id, title");     // title, id

// Notifier
$msg = "";
$msgState = "";



// Creates tournament
if(isset($_POST["submit"])) {
    // Success creating tourna
    if(createTourna($_POST["title"], $_POST["timezone"], $_POST["format"], $_POST["max_entry"], $_POST["max_entry_player"], $_POST["start_dt"], $_POST["end_dt"], $_POST["pairing"], isset($_POST["is_public"]), isset($_POST["is_open"]), $_POST["description"], $_POST["creator_id"])) {
        $msg = "Tournament created";
        $msgState = "success";
        
        // Redirect to index
        $tournaId = $pdo->lastInsertId();
        header("Location: index.php?tourna_id=$tournaId");
    }
    // Failed
    else {
        $msg = "An error occured, failed creating tournament.";
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
    <title>Create Tournament</title>
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
                        <!-- Tournament List -->
                        <?php 
                        foreach($tournas as $tourna) {
                            $id = $tourna["id"];
                            $title = htmlspecialchars($tourna["title"]);
                            echo "<li><a href=\"index.php?tourna_id=$id\">$title</a></li>";
                        }
                        ?>
                        <li><a href="index.php?tourna_id=new">New</a></li>
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
            <!-- Create Tourna -->
            <div class="form-box box">
                <form action="" method="post">
                    <h3>Create Tournament</h3>
                    <div class="form-field">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" max="255" placeholder="Tournament title">
                    </div>
                    <div class="form-field">
                        <label for="timezone">Tournament Timezone</label>
                        <select name="timezone" id="timezone">
                            <?php 
                            $timezones = DateTimeZone::listIdentifiers();
                            if($timezones) foreach($timezones as $timezone) echo "<option value=\"$timezone\">$timezone</option>";
                            ?>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="format">Format</label>
                        <select name="format" id="format">
                            <option value="Single Elimination">Single Elimination</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="max_entry">Max Entries</label>
                        <input type="number" name="max_entry" id="max_entry" max="1000" placeholder="0">
                    </div>
                    <div class="form-field">
                        <label for="max_entry_player">Players per Entry</label>
                        <input type="number" name="max_entry_player" id="max_entry_player" max="1000" placeholder="0">
                    </div>
                    <div class="form-field">
                            <label for="start_dt">Start DateTime</label>
                            <input type="datetime" name="start_dt" id="start_dt" placeholder="00-00-00 00:00:00">
                    </div>
                    <div class="form-field">
                            <label for="end_dt">End DateTime</label>
                            <input type="datetime" name="end_dt" id="end_dt" placeholder="00-00-00 00:00:00">
                    </div>
                    <div class="form-field">
                        <label for="pairing">Pairing</label>
                        <select name="pairing" id="pairing">
                            <option value="Random">Random</option>
                            <option value="Order">Order</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="is_public" class="switch">
                            <input type="checkbox" name="is_public" id="is_public">
                            <div><div></div></div>
                            <span>Public</span>
                        </label>
                    </div>
                    <div class="form-field">
                        <label for="is_open" class="switch">
                            <input type="checkbox" name="is_open" id="is_open">
                            <div><div></div></div>
                            <span>Allow registration</span>
                        </label>
                    </div>
                    <div class="form-field">
                        <label for="description">Description</label>
                        <textarea name="description" id="description"></textarea>
                    </div>
                    <input type="hidden" name="creator_id" value="<?php echo $userId; ?>">
                    <input type="submit" name="submit" value="Create">
                </form>
            </div>
        </div>
    </main>



    <script>
        window.onload = function() {

            // Set Timezone (input)
            const timeZoneElem = document.getElementById('timezone')
            if (timeZoneElem != null) {
                const clientTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone
                for(let i = 0; i < timeZoneElem.options.length; i++) {
                    let opt = timeZoneElem.options[i]
                    opt.selected = opt.value == clientTimezone
                }
            }

            const msg = document.getElementById('msg')
            if(msg == null) return
            setTimeout(() => msg.style.opacity = '0', 3000)
        }
    </script>


</body>
</html>
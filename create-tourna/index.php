<?php
require_once "../pdo.php";

// Check if Logged In
session_start();
if(!isset($_SESSION["user_id"])) header("Location: auth/signin.php");

$userId = $_SESSION["user_id"];
$userName = $_SESSION["username"];

// Creates tournament
if(isset($_POST["create_tourna"])) {
    // Success creating tourna
    if(createTourna($_POST["title"], $_POST["format"], $_POST["max_entry"], $_POST["max_entry_player"], $_POST["pairing"], $_POST["is_public"], $_POST["is_open"], $_POST["description"], $_POST["creator_id"])) {
        header("Location: ../index.php");
    }
    // Failed
    else {
        echo "Failed creating tournament.";
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Create Tournament</title>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="header-row">
            <div class="header-section">
                <h3>Virtual Arena</h3>
            </div>
            <div class="header-section">
                <h4><?php echo $userName; ?></h4>
                <a class="btn" href="auth/signout.php">Sign Out</a>
            </div>
        </div>
    </header>



    <!-- Main Content -->
    <main>
        <div class="content-box">
            <div class="tourna-box">

                <!-- Create Tourna -->
                <form action="" method="post">
                    <div class="form-field">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" max="255">
                    </div>  
                    <div class="form-field">
                        <label for="format">Format</label>
                        <select name="format" id="format">
                            <option value="Single Elimination">Single Elimination</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="max_entry">Max Entries</label>
                        <input type="number" name="max_entry" id="max_entry" max="1000">
                    </div>
                    <div class="form-field">
                        <label for="max_entry_player">Players per Entry</label>
                        <input type="number" name="max_entry_player" id="max_entry_player" max="1000">
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
                            <span>Public</span>
                            <input type="checkbox" name="is_public" id="is_public">
                            <div><div></div></div>
                        </label>
                    </div>
                    <div class="form-field">
                        <label for="is_open" class="switch">
                            <span>Allow registration</span>
                            <input type="checkbox" name="is_open" id="is_open">
                            <div><div></div></div>
                        </label>
                    </div>
                    <div class="form-field">
                        <label for="description">Description</label>
                        <textarea name="description" id="description"></textarea>
                    </div>
                    <input type="hidden" name="creator_id" value="<?php echo $userId; ?>">
                    <input type="submit" name="create" value="Create">
                    <a href="../index.php">Back</a>
                </form>

            </div>
        </div>
    </main>

</body>
</html>

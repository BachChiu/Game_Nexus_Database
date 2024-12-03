<?php
session_start();
require('./common.php');
if (isset($_SESSION['user_authentication']) and $_SESSION['user_authentication'] != '') {
    $currentUser = $_SESSION['user_authentication'];
    if (isset($_SESSION['error'])) {
        echo "<p style=\"color:red;\">" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
} else {
    $currentUser = '';
    $_SESSION['error'] = 'Please login to access your account profile.';
    header('Location: ./login.php');
}
if (isset($_POST["creatorID"])) {
    $creatorID = intval($_POST["creatorID"]);
    $db = getDB();
    $sql = "DELETE FROM follow WHERE userID = ? AND creatorID = ?";
    $statement = $db->prepare($sql);
    $statement->bind_param("si", $currentUser, $creatorID);
    $statement->execute();
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Profile</title>
    <link rel="stylesheet" href="styles2.css">
</head>

<body>
    <nav>
        <div class="container">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="personalGames.php">My Games List</a></li>
                <li><a href="contentcreator.php">Content Creators</a></li>
                <li><a href="recomm.php">Recommendations</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </div>
    </nav>

    <main class="container" id="profileSection">
        <div class="profile-container">
            <div class="profile-sidebar card">
                <h1><?= $currentUser ?></h1>

                <form id="updatePassword" action="updatePassword.php" method="POST">
                    <label for="oldPassword">Current Password:</label>
                    <input type="password" id="oldPassword" name="oldPassword" required>

                    <label for="password">New password:</label>
                    <input type="password" id="password" name="password" required>

                    <label for="passConfirmation">Confirm New Password:</label>
                    <input type="password" id="passConfirmation" name="passConfirmation" required>

                    <button class="btn edit-profile-btn" type="submit">Update Password</button>
                </form>
            </div>

            <div class="main-content card2">
                <h1>Gaming Profile</h1>

                <section class="card">
                    <h2>Favorite Genres</h2>
                    <div class="game-preferences">
                        <?php
                        $db = getDB();
                        $sql = "SELECT genre, COUNT(*) AS genreCount FROM favorite JOIN games ON favorite.gameID = games.gameID 
                        JOIN classified_as ON favorite.gameID = classified_as.gameID WHERE userID = ? GROUP BY genre ORDER BY genreCount DESC LIMIT 3";
                        $statement = $db->prepare($sql);
                        $statement->bind_param("s", $currentUser);
                        $statement->execute();
                        $intermediate = $statement->get_result();
                        while ($result = $intermediate->fetch_assoc()) { ?>
                            <span class="preference-tag"><?= $result["genre"] ?>&nbsp&nbsp&nbsp&nbsp&nbsp</span>
                        <?php }
                        ?>
                    </div>
                </section>

                <section class="card">
                    <h2>Gaming Platforms</h2>
                    <div class="game-preferences">
                        <?php
                        $db = getDB();
                        $sql = "SELECT platform, COUNT(*) AS platformCount FROM favorite JOIN games ON favorite.gameID = games.gameID JOIN available_on ON favorite.gameID = available_on.gameID WHERE userID =?
                        GROUP BY platform ORDER BY platformCount DESC";
                        $statement = $db->prepare($sql);
                        $statement->bind_param("s", $currentUser);
                        $statement->execute();
                        $intermediate = $statement->get_result();
                        while ($result = $intermediate->fetch_assoc()) { ?>
                            <span class="preference-tag"><?= $result["platform"] ?>&nbsp&nbsp&nbsp&nbsp&nbsp</span>
                        <?php }
                        ?>
                    </div>
                </section>
            </div>
            <div class="main-content card2">
                <h1>Follow list</h1>
                <section class="card">
                    <form id="followListForm" method="POST">
                        <ul id="gamesList">
                            <?php
                            $db = getDB();
                            $sql = "SELECT follow.creatorID, creatorName FROM follow JOIN content_creator ON follow.creatorID = content_creator.creatorID WHERE userID=?";
                            $statement = $db->prepare($sql);
                            $statement->bind_param("s", $currentUser);
                            $statement->execute();
                            $intermediate = $statement->get_result();
                            while ($result = $intermediate->fetch_assoc()) {
                                echo "<li class='game-item'>
                            <p><h4>" . $result["creatorName"] . "</h4><strong>
                            <button class='btn remove-btn' type='submit' name='creatorID' value=" . $result["creatorID"] . ">Remove</button>
                            </li><br>";
                            }
                            $db->close();
                            ?>
                        </ul>
                    </form>
                </section>

            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Game Database Application. All rights reserved.</p>
    </footer>
    <script>
        $(document).ready(function () {
            $('#followListForm').submit(function (event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: 'profile.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#profileSection').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + error);
                    }
                });
            });
        });
    </script>
</body>

</html>
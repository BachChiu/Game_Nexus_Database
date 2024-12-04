<?php
session_start();
require('./common.php');
if (isset($_SESSION['user_authentication']) and $_SESSION['user_authentication'] != '') {
    $currentUser = $_SESSION['user_authentication'];
} else {
    $currentUser = '';
    $_SESSION['error'] = 'Please login to access your personal game list.';
    header('Location: ./login.php');
}
if (isset($_POST["gameID"])) {
    $gameID = intval($_POST["gameID"]);
    $db = getDB();
    $sql = "DELETE FROM favorite WHERE userID = ? AND gameID = ?";
    $statement = $db->prepare($sql);
    $statement->bind_param("si", $currentUser, $gameID);
    $statement->execute();
    $db->close();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Games List</title>
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

    <main class="container">
        <section id="gamesSection" class="card2">
            <h1>My Games List</h1>
            <p style="color:white">Your curated list of games. Manage your favorites, track progress, and get updates on
                games you love.</p>
            <br>
            <h2>Your Games</h2>
            <form id="gamesListForm" class="card" method="POST">
                <ul id="gamesList">
                    <?php
                    $db = getDB();
                    $sql = "SELECT DISTINCT favorite.gameID, games.gameName, GROUP_CONCAT(DISTINCT classified_as.genre SEPARATOR ', ') as 'genres',
                    GROUP_CONCAT(DISTINCT available_on.platform SEPARATOR ', ') AS 'platforms' FROM favorite LEFT JOIN games ON
                    favorite.gameID = games.gameID LEFT JOIN classified_as ON games.gameID = classified_as.gameID
                    LEFT JOIN available_on ON classified_as.gameID = available_on.gameID WHERE userID = ? GROUP BY favorite.gameID, games.gameName";
                    $statement = $db->prepare($sql);
                    $statement->bind_param("s", $currentUser);
                    $statement->execute();
                    $result = $statement->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<li class='game-item'>
                            <p><h4>" . $row["gameName"] . "</h4><strong>Genre:</strong>" . $row["genres"] . "<br>
                            <strong>Platform:</strong>" . $row["platforms"] . "</p>
                            <button class='btn remove-btn' type='submit' name='gameID' value=" . $row["gameID"] . ">Remove</button>
                        </li><br>";
                    }
                    $db->close();
                    ?>
                </ul>

            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Game Database Application. Keep playing and keep slaying!</p>
    </footer>
    <script>
        $(document).ready(function () {
            $('#gamesListForm').submit(function (event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: 'personalGames.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#gamesSection').html(response);
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
<?php
session_start();
require('./common.php');
if (isset($_SESSION['user_authentication']) and $_SESSION['user_authentication'] != '') {
    $currentUser = $_SESSION['user_authentication'];
    $validUser = true;
} else {
    $validUser = false;
    $currentUser = '';
}
if (isset($_POST["genre"]) and $_POST["genre"] != '') {
    $genreFilter = (string) $_POST["genre"];
    if (strcmp($genreFilter, "all") == 0) {
        $allGenre = true;
    } else {
        $allGenre = false;
    }
    $genreAvailable = true;
} else {
    $allGenre = false;
    $genreAvailable = false;
}
if (isset($_POST["platform"]) and $_POST["platform"] != '') {
    $platformFilter = (string) $_POST["platform"];
    if (strcmp($platformFilter, "all") == 0) {
        $allPlatform = true;
    } else {
        $allPlatform = false;
    }
    $platformAvailable = true;
} else {
    $allPlatform = false;
    $platformAvailable = false;
}
if (isset($_POST["searchBar"]) and $_POST["searchBar"] != '') {
    $searchAvailable = true;
    $searchBar = (string) $_POST["searchBar"];
} else {
    $searchBar = '';
    $searchAvailable = false;
}
if (isset($_POST["favoriteGameName"])) {
    $gameTitle = $_POST["favoriteGameName"];
    $db1 = getDB();
    $gameSql = "SELECT MIN(gameID) AS gameID FROM games WHERE gameName=?";
    $getGame = $db1->prepare($gameSql);
    $getGame->bind_param("s", $gameTitle);
    $getGame->execute();
    $gameIDIntermediate = $getGame->get_result();
    $gameIDResult = $gameIDIntermediate->fetch_assoc();
    $gameID = $gameIDResult["gameID"];
    $db1->close();
    $db = getDB();
    $sql = "SELECT userID, gameID FROM favorite WHERE userID=? AND gameID=?";
    $statement = $db->prepare($sql);
    $statement->bind_param("si", $currentUser, $gameID);
    $statement->execute();
    $intermediate = $statement->get_result();
    $result = $intermediate->fetch_assoc();
    $added = false;
    if (!$result) {
        $addToFavorite = "INSERT INTO favorite (userID, gameID) VALUES (?,?)";
        $favoriteStatement = $db->prepare($addToFavorite);
        $favoriteStatement->bind_param("si", $currentUser, $gameID);
        $favoriteStatement->execute();
        $added = true;
    } else {
        $added = false;
    }
    $db->close();
}

?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Game Database Application</title>
    <link rel="stylesheet" href="styles2.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="moving-background">
    <div id="homeContent" class="container">
        <?php if (isset($_POST["favoriteGameName"]) and $added) {
            unset($_POST["favoriteGameName"]);
            echo "<script>alert('Game has been added to your game list.');</script>";
        } else if (isset($_POST["favoriteGameName"])) {
            unset($_POST["favoriteGameName"]);
            echo "<script>alert('This game is already in your game list.');</script>";
        } ?>
        <!-- Navigation bar-->
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

        <!-- Main Section-->
        <main>
            <section id="introduction" class="card">
                <h1 class="site-title">Game Nexus Database</h1>
                <p>
                    Our application allows users to find games across all platforms that match their preferences.
                    Use the search feature to filter games by genre, platform, and more. Create an account to save
                    your favorite games and receive personalized recommendations.
                </p>
            </section>
        </main>

        <!-- User Registration -->
        <?php if (!$validUser): ?>
            <!-- User Registration -->
            <section id="register" class="card">
                <h2>Register</h2>
                <p>Already have an account? <a href="login.php">Login Here</a>.</p>

                <form id="registerForm" action="register.php" method="POST">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo "<p style=\"color:red;\">" . $_SESSION['error'] . "</p>";
                        unset($_SESSION['error']);
                    }
                    ?>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>

                    <label for="passConfirmation">Confirm Password:</label>
                    <input type="password" id="passConfirmation" name="passConfirmation" required>
                    <span id="passwordError" class="error"></span>

                    <button type="submit" class="btn" class="submit" id="submit">Register</button>
                </form>
            <?php else: ?>
                <br>
                <!-- Welcome message for logged in users -->
                <form id="logoutForm" action="logout.php" method="POST" class="card">
                    <h3>Welcome back, <?php echo htmlspecialchars($currentUser); ?>!</h3>
                    <button type="submit" class="btn">Log out</button>
                </form>
            <?php endif; ?>
        </section>

        <!-- Game Search -->
        <section id="search" class="card">
            <h2>Search for Games</h2>
            <form id="searchForm" method="POST">
                <label for="searchBar">Search by Title:</label>
                <input type="text" id="searchBar" name="searchBar" placeholder="Enter game title">

                <label for="genre">Filter by Genre:</label>
                <select id="genre" name="genre">
                    <option value="all">All Genres</option>
                    <?php
                    $db = getDB();
                    $genreListSql = "SELECT genre FROM genres";
                    $genreList = $db->query($genreListSql);
                    while ($result = $genreList->fetch_assoc()) {
                        echo "<option value='" . $result["genre"] . "'>" . $result["genre"] . "</option>";
                    }
                    $db->close();
                    ?>
                </select>

                <label for="platform">Filter by Platform:</label>
                <select id="platform" name="platform">
                    <option value="all">All Platforms</option>
                    <?php
                    $db = getDB();
                    $platformListSql = "SELECT platform FROM platforms";
                    $platformList = $db->query($platformListSql);
                    while ($result = $platformList->fetch_assoc()) {
                        echo "<option value='" . $result["platform"] . "'>" . $result["platform"] . "</option>";
                    }
                    $db->close();
                    ?>
                </select>

                <button type="submit" class="btn">Search</button>
            </form>
        </section>
        <?php
        $db = getDB();
        $searchSql = "SELECT DISTINCT gameName, releaseDate, reviews, rating, descriptions FROM games ";
        echo "
                <table>
                    <tr>
                        <th>Game</th>
                        <th>Release Date</th>
                        <th>Reviews</th>
                        <th>Rating</th>
                        <th>Description</th>
                        <th></th>
                    </tr>";
        if ($searchAvailable and $genreAvailable and $platformAvailable) {
            if ($allGenre and $allPlatform) {
                $searchSql .= "WHERE (gameName LIKE CONCAT('%', ? ,'%')) ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                $statement = $db->prepare($searchSql);
                $statement->bind_param("s", $searchBar);
                $statement->execute();
                $gameList = $statement->get_result();
                while ($result = $gameList->fetch_assoc()) {
                    printGame($result);
                }
            } else if ($allGenre and !$allPlatform) {
                $searchSql .= "JOIN available_on ON games.gameID = available_on.gameID WHERE (gameName LIKE CONCAT('%', ? ,'%')) AND available_on.platform = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                $statement = $db->prepare($searchSql);
                $statement->bind_param("ss", $searchBar, $platformFilter);
                $statement->execute();
                $gameList = $statement->get_result();
                while ($result = $gameList->fetch_assoc()) {
                    printGame($result);
                }
            } else if (!$allGenre and $allPlatform) {
                $searchSql .= "JOIN classified_as ON games.gameID = classified_as.gameID WHERE (gameName LIKE CONCAT('%', ? ,'%')) AND classified_as.genre = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                $statement = $db->prepare($searchSql);
                $statement->bind_param("ss", $searchBar, $genreFilter);
                $statement->execute();
                $gameList = $statement->get_result();
                while ($result = $gameList->fetch_assoc()) {
                    printGame($result);
                }
            } else if (!$allGenre and !$allPlatform) {
                $searchSql .= "JOIN classified_as ON games.gameID = classified_as.gameID JOIN available_on ON games.gameID = available_on.gameID WHERE (gameName LIKE CONCAT('%', ? ,'%')) AND available_on.platform = ? AND classified_as.genre = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                $statement = $db->prepare($searchSql);
                $statement->bind_param("sss", $searchBar, $platformFilter, $genreFilter);
                $statement->execute();
                $gameList = $statement->get_result();
                while ($result = $gameList->fetch_assoc()) {
                    printGame($result);
                }
            }
        } else {
            if ($allGenre and $allPlatform) {
                $searchSql .= " ORDER BY reviews DESC, rating DESC, releaseDate DESC LIMIT 1000";//Limited due to memory issue
                $gameList = $db->query($searchSql);
                while ($result = $gameList->fetch_assoc()) {
                    printGame($result);
                }
            } else if ($allGenre and !$allPlatform) {
                $searchSql .= "JOIN available_on ON games.gameID = available_on.gameID WHERE available_on.platform = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                $statement = $db->prepare($searchSql);
                $statement->bind_param("s", $platformFilter);
                $statement->execute();
                $gameList = $statement->get_result();
                while ($result = $gameList->fetch_assoc()) {
                    printGame($result);
                }
            } else if (!$allGenre and $allPlatform) {
                $searchSql .= "JOIN classified_as ON games.gameID = classified_as.gameID WHERE classified_as.genre = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                $statement = $db->prepare($searchSql);
                $statement->bind_param("s", $genreFilter);
                $statement->execute();
                $gameList = $statement->get_result();
                while ($result = $gameList->fetch_assoc()) {
                    printGame($result);
                }
            } else if (!$allGenre and !$allPlatform) {
                $searchSql .= "JOIN classified_as ON games.gameID = classified_as.gameID JOIN available_on ON games.gameID = available_on.gameID WHERE available_on.platform = ? AND classified_as.genre = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                $statement = $db->prepare($searchSql);
                $statement->bind_param("ss", $platformFilter, $genreFilter);
                $statement->execute();
                $gameList = $statement->get_result();
                while ($result = $gameList->fetch_assoc()) {
                    printGame($result);
                }
            }
        }
        echo "</table>";
        $db->close();

        ?>
        <!-- Footer -->
        <footer>
            <p>&copy; 2024 Game Database Application. All rights reserved.</p>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#searchForm').submit(function (event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: 'home.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#homeContent').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + error);
                    }
                });
            });
            $('.favoriteBtn').click(function (event) {
                var buttonID = $(this).val();
                $.ajax({
                    url: 'home.php',
                    method: 'POST',
                    data: {
                        favoriteGameName: buttonID,
                        genre: "<?php echo $genreFilter; ?>",
                        platform: "<?php echo $platformFilter; ?>",
                        searchBar: "<?php echo $searchBar; ?>"
                    },
                    success: function (response) {
                        $('#homeContent').html(response);
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
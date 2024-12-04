<?php
session_start();
require('./common.php');
if (isset($_SESSION['user_authentication']) and $_SESSION['user_authentication'] != '') {
    $currentUser = $_SESSION['user_authentication'];
    $validUser = true;
    if (isset($_POST["favoriteGameName"])) {
        $gameTitle = $_POST["favoriteGameName"];
        $added = addToFavoriteList($gameTitle,$currentUser);
    }
} else {
    $added = false;
    $currentUser = '';
    $validUser=false;
    $_SESSION['error'] = 'Please login to access your personal recommendation.';
    header('Location: ./login.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Recommendation</title>
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

    <header>
        <h1 class="site-title">Game Nexus Database</h1>
    </header>

    <main class="container">
        <section class="card2" id="recommendationsSection">
            <h2>Game Recommendations</h2>
            <p>Explore games you might enjoy based on your preferences. Add them to your list and start playing!</p><br>
            <section class="card">
                <h2>Recommended Games</h2>
                <form id="recommendationForm" method="POST" action="recomm.php">
                    <ul>
                    <?php
                    $favorited = countFavorite($currentUser);
                    if ($favorited >= 5) {
                        $userGenre = genrePreference($currentUser);
                        $userPlatform = platformPreference($currentUser);
                        $userPublisher = publisherPreference(($currentUser));
                        $following = countFollowing($currentUser);
                        if ($following > 0) {
                            $followGenre = followListGenrePreference($currentUser);
                            $followPlatform = platformPreference($currentUser);
                            $followPublisher = followListPublisherPreference($currentUser);
                            while ($row = $followGenre->fetch_assoc()) {
                                $followGenreList[$row['genre']] = floatval($row['genreCount'] / $following);
                            }
                            while ($row = $followPlatform->fetch_assoc()) {
                                $followPlatformList[$row['platform']] = floatval($row['platformCount'] / $following);
                            }
                            while ($row = $followPublisher->fetch_assoc()) {
                                $followPublisherList[$row['developer']] = floatval($row['publisherCount'] / $following);
                            }
                        }
                        $userGenreList = array();
                        $userPlatformList = array();
                        $userPublisherList = array();
                        $followGenreList = array();
                        $followPlatformList = array();
                        $followPublisherList = array();
                        while ($row = $userGenre->fetch_assoc()) {
                            $userGenreList[$row['genre']] = floatval($row['genreCount'] / $favorited);
                        }
                        while ($row = $userPlatform->fetch_assoc()) {
                            $userPlatformList[$row['platform']] = floatval($row['platformCount'] / $favorited);
                        }
                        while ($row = $userPublisher->fetch_assoc()) {
                            $userPublisherList[$row['developer']] = floatval($row['publisherCount'] / $favorited);
                        }
                        $combinedGenreList = array();
                        $combinedPlatformList = array();
                        $combinedPublisherList = array();
                        $combinedGenreListLength = 0;
                        $combinedPlatformListLength = 0;
                        $combinedPublisherListLength = 0;
                        foreach ($userGenreList + $followGenreList as $key => $value) {
                            $combinedGenreListLength += 1;
                            $combinedGenreList[$key] = 0.7 * ($userGenreList[$key] ?? 0) + 0.3 * ($followGenreList[$key] ?? 0);
                        }
                        foreach ($userPlatformList + $followPlatformList as $key => $value) {
                            $combinedPlatformListLength += 1;
                            $combinedPlatformList[$key] = 0.7 * ($userPlatformList[$key] ?? 0) + 0.3 * ($followPlatformList[$key] ?? 0);
                        }
                        foreach ($userPublisherList + $followPublisherList as $key => $value) {
                            $combinedPublisherListLength += 1;
                            $combinedPublisherList[$key] = 0.7 * ($userPublisherList[$key] ?? 0) + 0.3 * ($followPublisherList[$key] ?? 0);
                        }
                        $genreOrderTableSql = "CREATE TEMPORARY TABLE genreOrder(genre varChar(100),genreWeight double);";
                        $genreOrderTableSql2 = "INSERT INTO genreOrder (genre, genreWeight) VALUES ";
                        $count = 0;
                        foreach ($combinedGenreList as $key => $value) {
                            $count += 1;
                            if (($count == $combinedGenreListLength or $count == 5) and $count <= 5) {
                                $genreOrderTableSql2 .= "(\"" . $key . "\"," . $value . ");";
                            } else if ($count < 5) {
                                $genreOrderTableSql2 .= "(\"" . $key . "\"," . $value . "),";
                            }
                        }
                        $platformOrderTableSql = "CREATE TEMPORARY TABLE platformOrder(platform varChar(100),platformWeight double);";
                        $platformOrderTableSql2 = "INSERT INTO platformOrder (platform, platformWeight) VALUES ";
                        $count = 0;
                        foreach ($combinedPlatformList as $key => $value) {
                            $count += 1;
                            if (($count == $combinedPlatformListLength or $count == 5) and $count <= 5) {
                                $platformOrderTableSql2 .= "
                                (\"" . $key . "\"," . $value . ");";
                            } else if ($count < 5) {
                                $platformOrderTableSql2 .= "(\"" . $key . "\"," . $value . "),";
                            }
                        }
                        $publisherOrderTableSql = "CREATE TEMPORARY TABLE publisherOrder(developer varChar(200),publisherWeight double);";
                        $publisherOrderTableSql2 = "INSERT INTO publisherOrder (developer, publisherWeight) VALUES ";
                        $count = 0;
                        foreach ($combinedPublisherList as $key => $value) {
                            $count += 1;
                            if (($count == $combinedPublisherListLength or $count == 5) and $count <= 5) {
                                $publisherOrderTableSql2 .= "(\"" . $key . "\"," . $value . ");";
                            } else if ($count < 5) {
                                $publisherOrderTableSql2 .= "(\"" . $key . "\"," . $value . "),";
                            }
                        }
                        $db = getDB();
                        $db->query($genreOrderTableSql);
                        $db->query($genreOrderTableSql2);
                        $db->query($platformOrderTableSql);
                        $db->query($platformOrderTableSql2);
                        $db->query($publisherOrderTableSql);
                        $db->query($publisherOrderTableSql2);
                        $recommendSql = "SELECT DISTINCT g.gameName, g.rating, g.releaseDate, g.reviews, g.descriptions, GROUP_CONCAT(DISTINCT ca.genre SEPARATOR ', ') as 'genres', 
                        GROUP_CONCAT(DISTINCT ao.platform SEPARATOR ', ') AS 'platforms', GROUP_CONCAT(DISTINCT p.developer SEPARATOR ', ') AS 'developers',
                        MAX(platformOrder.platformWeight) AS 'platformWeight', MAX(genreOrder.genreWeight) AS 'genreWeight', MAX(COALESCE(publisherOrder.publisherWeight,0)) AS 'publisherWeight' FROM games g
                        JOIN classified_as ca ON g.gameID = ca.gameID 
                        JOIN available_on ao ON g.gameID = ao.gameID 
                        JOIN published p ON g.gameID = p.gameID 
                        JOIN platformOrder ON ao.platform = platformOrder.platform
                        JOIN genreOrder ON ca.genre = genreOrder.genre
                        LEFT JOIN publisherOrder ON p.developer = publisherOrder.developer
                        WHERE g.gameName NOT IN (SELECT gameName FROM favorite JOIN games ON favorite.gameID = games.gameID WHERE userID = ?)
                        AND g.gameID NOT IN (SELECT favorite.gameID FROM favorite JOIN games ON favorite.gameID = games.gameID WHERE userID = ?)
                        GROUP BY g.gameName, g.gameID
                        ORDER BY platformOrder.platformWeight DESC, platformOrder.platform, genreOrder.genreWeight DESC, genreOrder.genre, g.reviews DESC, publisherOrder.publisherWeight DESC, publisherOrder.developer, g.rating DESC LIMIT 10";
                        $statement = $db->prepare($recommendSql);
                        $statement->bind_param("ss", $currentUser, $currentUser);
                        $statement->execute();
                        $recommendList = $statement->get_result();
                        if (isset($_POST["favoriteGameName"]) AND $added AND $validUser) {
                            unset($_POST["favoriteGameName"]);
                            echo "<script>alert('Game has been added to your game list.');</script>";
                        }
                        while ($row = $recommendList->fetch_assoc()) {
                            echo "
                            <li class='recommendation-item'>
                                <h3>". $row['gameName']."</h3>
                                <p><strong>Platform: </strong>". $row['platforms']."</p>
                                <p><strong>Genre: </strong>". $row['genres']."</p>
                                <p><strong>Rating: </strong>". $row['rating']."</p>
                                <p><strong>Description: </strong>". $row['descriptions']."</p>";
                                echo '<td><button class="btn favoriteBtn" type="submit" name="favoriteGameName" value="' . $row["gameName"] . '">Add to your game list</button></td>
                            </li><br>';
                        }
                        $db->close();
                    } else {
                        $_SESSION['error2'] = 'You need at least 5 favorited games for recommendation system to function properly.';
                        echo "<p style=\"color:red;\">" . $_SESSION['error2'] . "</p>";
                        unset($_SESSION['error2']);
                    }
                    ?>
                    </ul>
                </form>
            </section>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 XP Vault. Keep exploring and keep gaming!</p>
    </footer>
</body>

</html>
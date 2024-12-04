<?php
session_start();
require('./common.php');
if (isset($_SESSION['user_authentication']) and $_SESSION['user_authentication'] != '') {
    $currentUser = $_SESSION['user_authentication'];
} else {
    $currentUser = '';
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
                <form id="recommendationForm" method="POST">
                    <?php
                    $favorited = countFavorite($currentUser);
                    if ($favorited >= 5) {
                        $userGenre = genrePreference($currentUser);
                        $userPlatform = platformPreference($currentUser);
                        $userPublisher = publisherPreference(($currentUser));
                        $following = countFollowing($currentUser);
                        if($following > 0)
                        {
                            $followGenre = followListGenrePreference($currentUser);
                            $followPlatform = platformPreference($currentUser);
                            $followPublisher = followListPublisherPreference($currentUser);
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
                        while ($row = $followGenre->fetch_assoc()) {
                            $followGenreList[$row['genre']] = floatval($row['genreCount'] / $following);
                        }
                        while ($row = $followPlatform->fetch_assoc()) {
                            $followPlatformList[$row['platform']] = floatval($row['platformCount'] / $following);
                        }
                        while ($row = $followPublisher->fetch_assoc()) {
                            $followPublisherList[$row['developer']] = floatval($row['publisherCount'] / $following);
                        }
                        $combinedGenreList = array();
                        $combinedPlatformList = array();
                        $combinedPublisherList = array();
                        $combinedGenreListLength = 0;
                        $combinedPlatformListLength = 0;
                        $combinedPublisherListLength = 0;
                        foreach ($userGenreList + $followGenreList as $key => $value)
                        {
                            $combinedGenreListLength += 1;
                            $combinedGenreList[$key] = 0.7 * ($userGenreList[$key] ?? 0) + 0.3 * ($followGenreList[$key] ?? 0);
                        }
                        foreach ($userPlatformList + $followPlatformList as $key => $value)
                        {
                            $combinedPlatformListLength += 1;
                            $combinedPlatformList[$key] = 0.7 * ($userPlatformList[$key] ?? 0) + 0.3 * ($followPlatformList[$key] ?? 0);
                        }
                        foreach ($userPublisherList + $followPublisherList as $key => $value)
                        {
                            $combinedPublisherListLength += 1;
                            $combinedPublisherList[$key] = 0.7 * ($userPublisherList[$key] ?? 0) + 0.3 * ($followPublisherList[$key] ?? 0);
                        }
                        $genreKey = array_keys($combinedGenreList);
                        $platformKey = array_keys($combinedPlatformList);
                        $publisherKey = array_keys($combinedPublisherList);
                        $genreSet = array_map('addQuote', $genreKey);
                        $platformSet = array_map('addQuote', $platformKey);
                        $publisherSet = array_map('addQuote', $publisherKey);
                        $genreSet = implode(",",$genreSet);
                        $platformSet = implode(",",$platformSet);
                        $publisherSet = implode(",",$publisherSet);
                        $genreOrderTableSql = "CREATE TEMPORARY TABLE genreOrder(genre varChar(100),genreWeight double);";
                        $genreOrderTableSql2 = "INSERT INTO genreOrder (genre, genreWeight) VALUES ";
                        $count = 0;
                        foreach ($combinedGenreList as $key => $value)
                        {
                            $count += 1;
                            if(($count == $combinedGenreListLength OR $count ==5) AND $count <= 5)
                            {
                                $genreOrderTableSql2 .= "(\"". $key . "\",". $value . ");";
                            }
                            else if($count <5)
                            {
                                $genreOrderTableSql2 .= "(\"". $key . "\",". $value . "),";
                            }
                        }
                        echo $genreOrderTableSql. $genreOrderTableSql2. "<br><br>";
                        $platformOrderTableSql = "CREATE TEMPORARY TABLE platformOrder(platform varChar(100),platformWeight double);";
                        $platformOrderTableSql2 = "INSERT INTO platformOrder (platform, platformWeight) VALUES ";
                        $count = 0;
                        foreach($combinedPlatformList as $key => $value)
                        {
                            $count += 1;
                            if(($count == $combinedPlatformListLength OR $count == 5) AND $count <= 5)
                            {
                                $platformOrderTableSql2 .= "
                                (\"". $key . "\",". $value . ");";
                            }
                            else if($count < 5)
                            {
                                $platformOrderTableSql2 .= "(\"". $key . "\",". $value . "),";
                            }
                        }
                        echo $platformOrderTableSql . $platformOrderTableSql2 . "<br><br>";
                        $publisherOrderTableSql = "CREATE TEMPORARY TABLE publisherOrder(developer varChar(200),publisherWeight double);";
                        $publisherOrderTableSql2 = "INSERT INTO publisherOrder (developer, publisherWeight) VALUES ";
                        $count = 0;
                        foreach ($combinedPublisherList as $key => $value)
                        {
                            $count += 1;
                            if(($count == $combinedPublisherListLength OR $count == 5) AND $count <= 5)
                            {
                                $publisherOrderTableSql2 .= "(\"". $key . "\",". $value . ");";
                            }
                            else if($count < 5)
                            {
                                $publisherOrderTableSql2 .= "(\"". $key . "\",". $value . "),";
                            }
                        }
                        echo $publisherOrderTableSql. $publisherOrderTableSql2 ."<br><br>";
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
                        ORDER BY platformOrder.platformWeight DESC, genreOrder.genreWeight DESC, g.reviews DESC, publisherOrder.publisherWeight DESC, g.rating DESC LIMIT 10";
                        echo $recommendSql;
                        $statement = $db->prepare($recommendSql);
                        $statement->bind_param("ss", $currentUser,$currentUser);
                        $statement->execute();
                        $recommendList = $statement->get_result();
                        while($row = $recommendList->fetch_assoc())
                        {
                            $stringToPrint = $row['gameName'];
                            var_dump($stringToPrint);
                        }

/*CREATE TEMPORARY TABLE genreOrder(genre varChar(100),genreWeight double);INSERT INTO genreOrder (genre, genreWeight) VALUES ("Strategy",0.7875),("Simulator",0.4125),("Turn Based Strategy",0.3375),("Adventure",0.3125),("RPG",0.3125),("Real Time Strategy",0.1375),("Indie",0.1),("Tactical",0.1),("MOBA",0.1125),("Racing",0.0375),("Platform",0.0375);

CREATE TEMPORARY TABLE platformOrder(platform varChar(100),platformWeight double);INSERT INTO platformOrder (platform, platformWeight) VALUES ("Windows PC",0.9625),("Mac",0.6875),("Linux",0.4125),("PlayStation 5",0.4125),("Xbox Series",0.4125),("PlayStation 4",0.275),("Xbox One",0.275),("Android",0.1375),("iOS",0.1375),("Nintendo Switch",0.1375), ("Google Stadia",0.1375);

CREATE TEMPORARY TABLE publisherOrder(developer varChar(200),publisherWeight double);INSERT INTO publisherOrder (developer, publisherWeight) VALUES ("2K Games",0.2375),("Firaxis Games",0.2375),("Paradox Development Studio",0.2375),("Paradox Interactive",0.2375),("Innersloth",0.1),("Bandai Namco Entertainment",0.1),("FromSoftware",0.1),("Larian Studios",0.1),("Tencent Holdings",0.1125),("Riot Games",0.1125),("Mojang Studios",0.075),("Fancy Force",0.0375);
SELECT DISTINCT g.gameName, g.rating, g.releaseDate, g.reviews, g.descriptions, GROUP_CONCAT(DISTINCT ca.genre SEPARATOR ', ') as 'genres', 
                        GROUP_CONCAT(DISTINCT ao.platform SEPARATOR ', ') AS 'platforms', GROUP_CONCAT(DISTINCT p.developer SEPARATOR ', ') AS 'developers' FROM games g
                        JOIN classified_as ca ON g.gameID = ca.gameID 
                        JOIN available_on ao ON g.gameID = ao.gameID 
                        JOIN published p ON g.gameID = p.gameID 
                        JOIN platformOrder ON ao.platform = platformOrder.platform
                        JOIN genreOrder ON ca.genre = genreOrder.genre
                        JOIN publisherOrder ON p.developer = publisherOrder.developer
                        WHERE g.gameName NOT IN (SELECT gameName FROM favorite JOIN games ON favorite.gameID = games.gameID WHERE userID = 'vibach2003')
                        AND g.gameID NOT IN (SELECT favorite.gameID FROM favorite JOIN games ON favorite.gameID = games.gameID WHERE userID = 'vibach2003')
                        GROUP BY g.gameName, g.gameID
                        ORDER BY platformOrder.platformWeight DESC, genreOrder.genreWeight DESC, g.reviews DESC, publisherOrder.publisherWeight DESC, g.rating DESC 
*/ 
    
/*SELECT DISTINCT gameName, gameID FROM games WHERE gameName NOT IN (SELECT gameName FROM favorite JOIN games ON favorite.gameID = games.gameID WHERE userID = "vibach2003") 
AND gameID NOT IN (SELECT favorite.gameID FROM favorite JOIN games ON favorite.gameID = games.gameID WHERE userID = "vibach2003") ORDER BY reviews DESC; */
/*CREATE TEMPORARY TABLE genreOrder
(
    genre varChar(100),
    genreWeight double
);
INSERT INTO genreOrder (genre, genreWeight) VALUES
("Strategy", 0.77),
("Simulator", 0.46),
("Turn Based Strategy", 0.3875),
("Adventure", 0.345);
SELECT DISTINCT gameName, rating, releaseDate, reviews, descriptions, classified_as.genre FROM games JOIN classified_as ON games.gameID = classified_as.gameID JOIN genreOrder ON classified_as.genre = genreOrder.genre WHERE classified_as.genre IN ("Strategy","Simulator","Turn Based Strategy","Adventure") ORDER BY genreOrder.genreWeight DESC, reviews DESC, rating DESC; */
                    } 
                    else 
                    {
                        $_SESSION['error'] = 'You need at least 5 favorited games for recommendation system to function properly.';
                        echo "<p style=\"color:red;\">" . $_SESSION['error'] . "</p>";
                        unset($_SESSION['error']);
                    }
                    ?>
                    <ul>

                    </ul>
                </form>
                <ul id="recommendationsList">
                    <li class="recommendation-item">
                        <h3>Elden Ring</h3>
                        <p><strong>Genre:</strong> Action RPG</p>
                        <button class="btn add-btn">Add to List</button>
                    </li>
                    <li class="recommendation-item">
                        <h3>Cyberpunk 2077</h3>
                        <p><strong>Genre:</strong> Open World RPG</p>
                        <button class="btn add-btn">Add to List</button>
                    </li>
                    <li class="recommendation-item">
                        <h3>Red Dead Redemption 2</h3>
                        <p><strong>Genre:</strong> Adventure</p>
                        <button class="btn add-btn">Add to List</button>
                    </li>
                </ul>
            </section>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 XP Vault. Keep exploring and keep gaming!</p>
    </footer>
    <script>
        $(document).ready(function () {
            $('#recommondationForm').submit(function (event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: 'recomm.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#recommendationsSection').html(response);
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
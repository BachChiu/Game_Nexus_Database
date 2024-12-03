<?php
    session_start();
    require_once('./common.php');
    if(isset($_SESSION['user_authentication']) AND $_SESSION['user_authentication'] != '')
    {
        $currentUser = $_SESSION['user_authentication'];
        if(isset($_SESSION['error']))
	    {
		    echo "<p style=\"color:red;\">".$_SESSION['error']."</p>";
		    unset($_SESSION['error']);
	    }
    }
    else
    {
        $currentUser = '';
        $_SESSION['error'] = 'Please login to access your account profile.';
        header('Location: ./login.php'); 
        exit();
    }

    $genreFilter = $searchBar = '';

    // if (isset($_POST["genre"]) and $_POST["genre"] != '') {
    //     $genreFilter = (string) $_POST["genre"];
    //     if (strcmp($genreFilter, "all") == 0) {
    //         $allGenre = true;
    //     } else {
    //         $allGenre = false;
    //     }
    //     $genreAvailable = true;
    // } else {
    //     $allGenre = false;
    //     $genreAvailable = false;
    // }

    if (isset($_POST["searchBar"]) and $_POST["searchBar"] != '') {
        $searchAvailable = true;
        $searchBar = (string) $_POST["searchBar"];
    } else {
        $searchAvailable = false;
    }
    if(isset($_POST["favoriteGameName"]))
    {
        $gameTitle = $_POST["favoriteGameName"];
        $db1 = getDB();
        $gameSql = "SELECT MIN(gameID) AS gameID FROM games WHERE gameName=?";
        $getGame = $db1->prepare($gameSql);
        $getGame->bind_param("s", $gameTitle);
        $getGame->execute();
        $gameIDIntermediate = $getGame->get_result();
        $gameIDResult = $gameIDIntermediate->fetch_assoc();
        $gameID =$gameIDResult["gameID"];
        $db1->close();
        $db = getDB();
        $sql = "SELECT userID, gameID FROM favorite WHERE userID=? AND gameID=?";
        $statement= $db->prepare($sql);
        $statement->bind_param("si", $currentUser, $gameID);
        $statement->execute();
        $intermediate= $statement->get_result();
        $result = $intermediate->fetch_assoc();
        $added = false;
        if(!$result)
        {
            $addToFavorite = "INSERT INTO favorite (userID, gameID) VALUES (?,?)";
            $favoriteStatement = $db->prepare($addToFavorite);
            $favoriteStatement->bind_param("si", $currentUser, $gameID);
            $favoriteStatement->execute();
            $added = true;
        }
        else
        {
            $added = false;
        }
        $db->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="creatorContent">
        </div>
        <nav>
            <div class="container">
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="personalGames.php">My Games List</a></li>
                    <li><a href="contentcreator.php">Content Creators</a></li>
                    <li><a href="recomm.html">Recommendations</a></li>
                    <li><a href="profile.php">Profile</a></li>
                </ul>
            </div>
        </nav>

        <main class="container">
            <div">
                    <h1><?= $currentUser ?></h1>

                <div class="main-content">
                    
                    <!-- Content Creator Search -->
                    <section id="search">
                        <h2>Search for Content Creators</h2>
                        <form id="searchForm" method="POST">
                            <label for="searchBar">Search by Name:</label>
                            <input type="text" id="searchBar" name="searchBar" placeholder="Enter name">

                            <!-- <label for="genre">Filter by Genre:</label>
                            <select id="genre" name="genre">
                                <option value="all">All Genres</option>
                                <!-- <?php
                                $db = getDB();
                                $genreListSql = "SELECT genre FROM genres";
                                $genreList = $db->query($genreListSql);
                                while ($result = $genreList->fetch_assoc()) {
                                    echo "<option value='" . $result["genre"] . "'>" . $result["genre"] . "</option>";
                                }
                                $db->close();
                                ?> -->
                            </select>
                            <button type="submit">Search</button>
                        </form>
                    </section>
                </div>
            </div>
        <?php
            $db = getDB();
            $searchSql = "
                SELECT  
                    DISTINCT cc.creatorName,  
                    GROUP_CONCAT(DISTINCT ao.platform SEPARATOR ', ') AS platforms,
                    GROUP_CONCAT(DISTINCT ca.genre SEPARATOR ', ') AS genres,
                    GROUP_CONCAT(DISTINCT g.gameName SEPARATOR ', ') AS games
                FROM content_creator cc
                LEFT JOIN plays p ON cc.creatorID = p.creatorID
                LEFT JOIN games g ON p.gameID = g.gameID
                LEFT JOIN classified_as ca ON g.gameID = ca.gameID
                LEFT JOIN available_on ao ON g.gameID = ao.gameID
                GROUP BY cc.creatorname
                ORDER BY cc.creatorName ASC";
            echo "
                    <table>
                        <tr>
                            <th>Creator Name</th>
                            <th>Platforms</th>
                            <th>Genres</th>
                            <th>Games</th>
                            <th></th>
                        </tr>";

                        // table filters
                        // if ($searchAvailable && $genreAvailable) {
                        //     if ($allGenre) {
                        //         // When "All Genres" is selected
                        //         $searchSql .= " WHERE cc.creatorName LIKE CONCAT('%', ? ,'%') ORDER BY cc.creatorName ASC";
                        //         $statement = $db->prepare($searchSql);
                        //         $statement->bind_param("s", $searchBar);
                        //     } else {
                        //         // When a specific genre is selected
                        //         $searchSql .= " WHERE cc.creatorName LIKE CONCAT('%', ? ,'%') AND ca.genre = ? ORDER BY cc.creatorName ASC";
                        //         $statement = $db->prepare($searchSql);
                        //         $statement->bind_param("ss", $searchBar, $genreFilter);
                        //     }
                        //     $statement->execute();
                        //     $creatorList = $statement->get_result();
                        //     while ($result = $creatorList->fetch_assoc()) {
                        //         printCreator($result);
                        //     }
                        // } else {
                        //     if ($allGenre) {
                        //         // No search, but "All Genres" is selected
                        //         $searchSql .= " ORDER BY cc.creatorName ASC LIMIT 10000";
                        //         $creatorList = $db->query($searchSql);
                        //         while ($result = $creatorList->fetch_assoc()) {
                        //             printCreator($result);
                        //         }
                        //     } else {
                        //         // No search and a specific genre is selected
                        //         $searchSql .= " WHERE ca.genre = ? ORDER BY cc.creatorName ASC";
                        //         $statement = $db->prepare($searchSql);
                        //         $statement->bind_param("s", $genreFilter);
                        //         $statement->execute();
                        //         $creatorList = $statement->get_result();
                        //         while ($result = $creatorList->fetch_assoc()) {
                        //             printCreator($result);
                        //         }
                        //     }
                        // }

                        if ($searchAvailable) {
                            $searchSql .= " WHERE cc.creatorName LIKE CONCAT('%', ?, '%')";
                            $statement = $db->prepare($searchSql);
                            $statement->bind_param("s", $searchBar);
                            $statement->execute();
                            $creatorList = $statement->get_result();
                        } else {
                            $creatorList = $db->query($searchSql);
                        }
                        while ($result = $creatorList->fetch_assoc()) {
                            printCreator($result);
                        }
                
                        echo "</table>";
                        $db->close();
                        ?>
                    </main>
    </div>
    <footer>
        <p>&copy; 2024 Game Database Application. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#searchForm').submit(function (event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: 'contentcreator.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#creatorContent').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + error);
                    }
                });
            });
            $('.favoriteBtn').click(function (event)
            {
                var buttonID = $(this).val();
                $.ajax({
                    url: 'contentcreator.php',
                    method: 'POST',
                    data: {creatorID: buttonID,
                        // genre: "<?php echo $genreFilter; ?>",
                        searchBar: "<?php echo $searchBar; ?>"
                    },
                    success: function (response) {
                        $('#creatorContent').html(response);
                    },
                    error: function (xhr, status, error)
                    {
                        console.error('AJAX Error: ' + error);
                    }
                });
            });
        });
    </script>
</body>
</html>
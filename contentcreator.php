<?php
session_start();
require_once('./common.php');
if (isset($_SESSION['user_authentication']) and $_SESSION['user_authentication'] != '') {
    $currentUser = $_SESSION['user_authentication'];
} else {
    $currentUser = '';
    $_SESSION['error'] = 'Please login to access content creator search.';
    header('Location: ./login.php');
    exit();
}
if (isset($_POST["searchBar"]) and $_POST["searchBar"] != '') {
    $searchAvailable = true;
    $searchBar = (string) $_POST["searchBar"];
} else {
    $searchBar = '';
    $searchAvailable = false;
}
if (isset($_POST["creatorName"])) {
    $creatorName = $_POST["creatorName"];
    $db1 = getDB();
    $creatorSql = "SELECT creatorID FROM content_creator WHERE creatorName=?";
    $getCreator = $db1->prepare($creatorSql);
    $getCreator->bind_param("s", $creatorName);
    $getCreator->execute();
    $creatorIDIntermediate = $getCreator->get_result();
    $creatorIDResult = $creatorIDIntermediate->fetch_assoc();
    $creatorID = $creatorIDResult["creatorID"];
    $db1->close();
    $db = getDB();
    $sql = "SELECT userID, creatorID FROM follow WHERE userID=? AND creatorID=?";
    $statement = $db->prepare($sql);
    $statement->bind_param("si", $currentUser, $creatorID);
    $statement->execute();
    $intermediate = $statement->get_result();
    $result = $intermediate->fetch_assoc();
    $added = false;
    if (!$result) {
        $addToFollow = "INSERT INTO follow (userID, creatorID) VALUES (?,?)";
        $favoriteStatement = $db->prepare($addToFollow);
        $favoriteStatement->bind_param("si", $currentUser, $creatorID);
        $favoriteStatement->execute();
        $added = true;
    } else {
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
    <title>Content Creator List</title>
    <link rel="stylesheet" href="styles2.css">
</head>

<body>
    <div id="creatorContent">
        <?php if (isset($_POST["creatorName"]) and $added) {
            unset($_POST["favoriteGameName"]);
            echo "<script>alert('You are now following this creator.');</script>";
        } else if (isset($_POST["creatorName"])) {
            unset($_POST["favoriteGameName"]);
            echo "<script>alert('You already followed this creator.');</script>";
        } ?>
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

        <main class="container card">
            <div>
                <h1><?= $currentUser ?></h1>

                <div class="main-content">

                    <!-- Content Creator Search -->
                    <section id="search">
                        <h2>Search for Content Creators</h2>
                        <form id="searchForm" method="POST">
                            <label for="searchBar">Search by Name:</label>
                            <input type="text" id="searchBar" name="searchBar" placeholder="Enter name">
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
                LEFT JOIN available_on ao ON g.gameID = ao.gameID";
            echo "
                    <table>
                        <tr>
                            <th>Creator Name</th>
                            <th>Platforms</th>
                            <th>Genres</th>
                            <th>Games</th>
                            <th></th>
                        </tr>";
            
            if ($searchAvailable) {
                $searchSql .= " WHERE cc.creatorName LIKE CONCAT('%', ?, '%') GROUP BY cc.creatorname
                            ORDER BY cc.creatorName ASC";
                $statement = $db->prepare($searchSql);
                $statement->bind_param("s", $searchBar);
                $statement->execute();
                $creatorList = $statement->get_result();
            } else {
                $searchSql .= " GROUP BY cc.creatorname
                            ORDER BY cc.creatorName ASC";
                $creatorList = $db->query($searchSql);
            }
            while ($result = $creatorList->fetch_assoc()) {
                printCreator($result);
            }

            echo "</table>";
            $db->close();
            ?>
        </main>
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
            $('.followBtn').click(function (event) {
                var buttonID = $(this).val();
                $.ajax({
                    url: 'contentcreator.php',
                    method: 'POST',
                    data: {
                        creatorName: buttonID,
                        searchBar: "<?php echo $searchBar; ?>"
                    },
                    success: function (response) {
                        $('#creatorContent').html(response);
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
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
        <div class="profile-container">
                <h1><?= $currentUser ?></h1>

            <div class="main-content">
                
                <!-- Content Creator Search -->
                <section id="search">
                    <h2>Search for Content Creators</h2>
                    <form id="searchForm" method="POST">
                        <label for="searchBar">Search by Name:</label>
                        <input type="text" id="searchBar" name="searchBar" placeholder="Enter name">

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

                        <button type="submit">Search</button>
                    </form>
                </section>
            </div>
        </div>
    </main>
    <?php
        $db = getDB();
        $searchSql = "SELECT DISTINCT creatorName from content_creator ";
        echo "
                <table>
                    <tr>
                        <th>Content Creators</th>
                    </tr>";
        $result = $db->query($searchSql);

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>". htmlspecialchars($row['creatorName']) . "</td></tr>";
            }
        } else{
            echo "<tr><td>No content creators found</td></tr>";
        }

        echo "</table>";
        $db->close();

        ?>

    <footer>
        <p>&copy; 2024 Game Database Application. All rights reserved.</p>
    </footer>
</body>
</html>
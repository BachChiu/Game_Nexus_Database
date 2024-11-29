<?php
    session_start();
    require('./common.php');
    if(isset($_SESSION['user_authentication']) AND $_SESSION['user_authentication'] != '')
    {
        $currentUser = $_SESSION['user_authentication'];
    }
    else
    {
        $currentUser = ''; 
    }
    if(isset($_POST["genre"]) AND $_POST["genre"] !='')
    {
        $genreFilter = (string) $_POST["genre"];
        if(strcmp($genreFilter, "all") == 0)
        {
            $allGenre = true;
        }
        else
        {
            $allGenre = false;
        }
        $genreAvailable = true;
    }
    else
    {
        $allGenre = false;
        $genreAvailable = false;
    }
    if(isset($_POST["platform"]) AND $_POST["platform"] !='')
    {
        $platformFilter = (string) $_POST["platform"];
        if(strcmp($platformFilter, "all") == 0)
        {
            $allPlatform = true;
        }
        else
        {
            $allPlatform = false;
        }
        $platformAvailable = true;
    }
    else
    {
        $allPlatform = false;
        $platformAvailable = false;
    }
    if(isset($_POST["searchBar"]) AND $_POST["searchBar"] !='')
    {
        $searchAvailable = true;
        $searchBar = (string) $_POST["searchBar"];
    }
    else
    {
        $searchAvailable = false;
    }
?>
<html>
    <head>
        <title>Game Database Application</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <div id="homeContent">        
        <!-- Navigation bar-->
         <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="#">My Games List</a></li>
                    <li><a href="#">Recommendations</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <input type="text" placeholder="Search Games...">
                </ul>
         </nav>

         <!-- Main Section-->
          <main>
                <section id="introduction">
                    <h1>Welcome to the Game Database Application</h1>
                    <p>
                        Our application allows users to find games across all platforms that match their preferences.
                        Use the search feature to filter games by genre, platform, and more. Create an account to save
                        your favorite games and receive personalized recommendations.
                    </p>
                </section>
          </main>

          <!-- User Registration -->
        <section id="register">
            <h2>Register</h2>
            <p>Already have an account? <a href="login.php">Login Here</a>.</p>

            <form id="registerForm" action="register.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="passConfirmation">Confirm Password:</label>
                <input type="password" id="passConfirmation" name="passConfirmation" required>
                
                
                <button type="submit">Register</button>
            </form>
        </section>

        <!-- Game Search -->
        <section id="search">
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
                        while($result = $genreList->fetch_assoc())
                        {
                            echo "<option value='".$result["genre"]."'>".$result["genre"]."</option>";
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
                        while($result = $platformList->fetch_assoc())
                        {
                            echo "<option value='".$result["platform"]."'>".$result["platform"]."</option>";
                        }
                        $db->close();
                    ?>
                </select>

                <button type="submit">Search</button>
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
                    </tr>";
                if($searchAvailable AND $genreAvailable AND $platformAvailable)
                {
                    if($allGenre AND $allPlatform)
                    { 
                        $searchSql.= "WHERE (gameName LIKE CONCAT('%', ? ,'%')) ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                        $statement = $db->prepare($searchSql);
                        $statement->bind_param("s",$searchBar);
                        $statement->execute();
                        $gameList = $statement->get_result();
                        while($result = $gameList->fetch_assoc())
                        {
                           printGame($result);
                        }
                    }
                    else if($allGenre AND !$allPlatform)
                    {
                        $searchSql.= "JOIN available_on ON games.gameID = available_on.gameID WHERE (gameName LIKE CONCAT('%', ? ,'%')) AND available_on.platform = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                        $statement = $db->prepare($searchSql);
                        $statement->bind_param("ss", $searchBar, $platformFilter);
                        $statement->execute();
                        $gameList = $statement->get_result();
                        while($result = $gameList->fetch_assoc())
                        {
                            printGame($result);
                        }
                    }
                    else if(!$allGenre AND $allPlatform)
                    {
                        $searchSql.= "JOIN classified_as ON games.gameID = classified_as.gameID WHERE (gameName LIKE CONCAT('%', ? ,'%')) AND classified_as.genre = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                        $statement = $db->prepare($searchSql);
                        $statement->bind_param("ss",$searchBar, $genreFilter);
                        $statement->execute();
                        $gameList = $statement->get_result();
                        while($result = $gameList->fetch_assoc())
                        {
                            printGame($result);
                        }
                    }
                    else if(!$allGenre AND !$allPlatform)
                    {
                        $searchSql.= "JOIN classified_as ON games.gameID = classified_as.gameID JOIN available_on ON games.gameID = available_on.gameID WHERE (gameName LIKE CONCAT('%', ? ,'%')) AND available_on.platform = ? AND classified_as.genre = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                        $statement = $db->prepare($searchSql);
                        $statement->bind_param("sss",$searchBar, $platformFilter, $genreFilter);
                        $statement->execute();
                        $gameList = $statement->get_result();
                        while($result = $gameList->fetch_assoc())
                        {
                            printGame($result);
                        }
                    }
                }
                else
                {
                    if($allGenre AND $allPlatform)
                    {
                        $searchSql.= " ORDER BY reviews DESC, rating DESC, releaseDate DESC LIMIT 10000";
                        $gameList = $db->query($searchSql);
                        while($result = $gameList->fetch_assoc())
                        {
                            printGame($result);
                        }
                    }
                    else if($allGenre AND !$allPlatform)
                    { 
                        $searchSql.= "JOIN available_on ON games.gameID = available_on.gameID WHERE available_on.platform = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                        $statement = $db->prepare($searchSql);
                        $statement->bind_param("s",$platformFilter);
                        $statement->execute();
                        $gameList = $statement->get_result();
                        while($result = $gameList->fetch_assoc())
                        {
                            printGame($result);
                        }
                    }
                    else if(!$allGenre AND $allPlatform)
                    {
                        $searchSql.= "JOIN classified_as ON games.gameID = classified_as.gameID WHERE classified_as.genre = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                        $statement = $db->prepare($searchSql);
                        $statement->bind_param("s", $genreFilter);
                        $statement->execute();
                        $gameList = $statement->get_result();
                        while($result = $gameList->fetch_assoc())
                        {
                            printGame($result);
                        }
                    }
                    else if(!$allGenre AND !$allPlatform)
                    {
                        $searchSql.= "JOIN classified_as ON games.gameID = classified_as.gameID JOIN available_on ON games.gameID = available_on.gameID WHERE available_on.platform = ? AND classified_as.genre = ? ORDER BY reviews DESC, rating DESC, releaseDate DESC";
                        $statement = $db->prepare($searchSql);
                        $statement->bind_param("ss", $platformFilter, $genreFilter);
                        $statement->execute();
                        $gameList = $statement->get_result();
                        while($result = $gameList->fetch_assoc())
                        {
                            printGame($result);
                        }
                    }
                }
                echo "</table>";
                $db->close();
                
            ?>

        <!-- User's Game List -->
        <section id="gameList">
            <h2>Your Game List</h2>
            <p>Manage the games in your list below:</p>
            <ul id="userGameList">
                <!-- Dynamically generated list of user's games -->
            </ul>
            <button id="clearListBtn">Clear List</button>
        </section>

        <!-- Recommendations -->
        <section id="recommendations">
            <h2>Recommended Games for You</h2>
            <ul id="recommendationsList">
                <!-- Dynamically generated list of recommended games -->
            </ul>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Game Database Application. All rights reserved.</p>
    </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() 
        {
            $('#searchForm').submit(function(event) {
                event.preventDefault();
                var formData = $(this).serialize(); 
                $.ajax({
                    url: 'home.php', 
                    method: 'POST',  
                    data: formData,    
                    success: function(response) 
                    {
                        $('#homeContent').html(response);
                    },
                    error: function(xhr, status, error) 
                    {
                        console.error('AJAX Error: ' + error);
                    }
                });
            });
        });
    </script>
    </body>
</html>



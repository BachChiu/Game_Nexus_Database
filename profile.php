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
        header('Location: ./home.php'); 
    }
    if(isset($_SESSION['error']))
	{
		echo "<p style=\"color:red;\">".$_SESSION['error']."</p>";
		unset($_SESSION['error']);
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
                <li><a href="recomm.html">Recommendations</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="profile-container">
            <div class="profile-sidebar card">
                <h2><?= $currentUser ?></h2>
                
                <div class="gaming-stats">
                    <h3>Gaming Stats</h3>
                    <div class="stat-item">
                        <span>Games Favorited</span>
                        <span>142</span>
                    </div>
                    <div class="stat-item">
                        <span>Achievements</span>
                        <span>1,337</span>
                    </div>
                    <div class="stat-item">
                        <span>Play Time</span>
                        <span>2,456 hrs</span>
                    </div>
                    <div class="stat-item">
                        <span>Reviews</span>
                        <span>47</span>
                    </div>
                </div>
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

            <div class="main-content">
                <h1>Gaming Profile</h1>
                
                <section class="card">
                    <h2>Favorite Genres</h2>
                    <div class="game-preferences">
                        <span class="preference-tag">RPG</span>
                        <span class="preference-tag">Action</span>
                        <span class="preference-tag">Strategy</span>
                        <span class="preference-tag">Adventure</span>
                        <span class="preference-tag">FPS</span>
                    </div>
                </section>

                <section class="card">
                    <h2>Gaming Platforms</h2>
                    <div class="game-preferences">
                        <span class="preference-tag">PC</span>
                        <span class="preference-tag">PS5</span>
                        <span class="preference-tag">Switch</span>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Game Database Application. All rights reserved.</p>
    </footer>
</body>
</html>

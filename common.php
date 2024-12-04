<?php
function getDB()
{
    //Hidden implementation due to sensitive data, access site at http://75.40.51.27/Game_Nexus_Database/home.php
}
function checkUser($userToBeCheck)
{
    $db = getDB();
    $sql = "SELECT userID FROM users WHERE userID=?";
    $statement = $db->prepare($sql);
    $statement->bind_param("s", $userToBeCheck);
    $statement->execute();
    $intermediate = $statement->get_result();
    $result = $intermediate->fetch_assoc();
    $db->close();
    if (!$result) {
        return false;
    } else {
        return true;
    }
}
function getPassword($currentUser)
{
    $db = getDB();
    $sql = "SELECT userPass FROM users WHERE userID =?";
    $statement = $db->prepare($sql);
    $statement->bind_param("s", $currentUser);
    $statement->execute();
    $intermediate = $statement->get_result();
    $result = $intermediate->fetch_assoc();
    $db->close();
    return $result["userPass"];
}
function printGame($inputRow)
{
    echo "
    <tr>
        <td>" . $inputRow["gameName"] . "</td>
        <td>" . $inputRow["releaseDate"] . "</td>
        <td>" . $inputRow["reviews"] . "</td>
        <td>" . $inputRow["rating"] . "</td>
        <td>" . $inputRow["descriptions"] . "</td>";
    echo '<td><button class="btn favoriteBtn" name="favoriteGameName" value="' . $inputRow["gameName"] . '">Add to your game list</button></td>
    </tr>'; //Some games have single quote in the name and that broke this code, thus the adjustment.
}
function printCreator($inputRow2)
{
    echo "
        <tr>
            <td>{$inputRow2['creatorName']}</td>
            <td>{$inputRow2['platforms']}</td>
            <td>{$inputRow2['genres']}</td>
            <td>{$inputRow2['games']}</td>
            <td><button class='btn followBtn' name='favoriteCreator' value='" . $inputRow2["creatorName"] . "'>Add to your follow list</button></td>
        </tr>";
}
function genrePreference($currentUser)
{
    $db = getDB();
    $genresql = "SELECT genre, COUNT(*) AS genreCount FROM favorite JOIN games ON favorite.gameID = games.gameID 
            JOIN classified_as ON favorite.gameID = classified_as.gameID WHERE userID = ? GROUP BY genre ORDER BY genreCount DESC";
    $genrestatement = $db->prepare($genresql);
    $genrestatement->bind_param("s", $currentUser);
    $genrestatement->execute();
    $genrePreference = $genrestatement->get_result();
    $db->close();
    return $genrePreference;
}
function platformPreference($currentUser)
{
    $db = getDB();
    $sql = "SELECT platform, COUNT(*) AS platformCount FROM favorite JOIN games ON favorite.gameID = games.gameID JOIN available_on ON favorite.gameID = available_on.gameID WHERE userID =?
    GROUP BY platform ORDER BY platformCount DESC";
    $statement = $db->prepare($sql);
    $statement->bind_param("s", $currentUser);
    $statement->execute();
    $intermediate = $statement->get_result();
    $db->close();
    return $intermediate;
}
function followListGenrePreference($currentUser)
{
    $db = getDB();
    $sql = "SELECT classified_as.genre, COUNT(*) AS genreCount FROM follow
            JOIN content_creator ON follow.creatorID = content_creator.creatorID
            LEFT JOIN plays ON content_creator.creatorID = plays.creatorID 
            LEFT JOIN games ON plays.gameID = games.gameID
            LEFT JOIN classified_as ON games.gameID = classified_as.gameID
            WHERE follow.userID = ?
            GROUP BY classified_as.genre
            ORDER BY genreCount DESC, classified_as.genre DESC";
    $statement = $db->prepare($sql);
    $statement->bind_param("s", $currentUser);
    $statement->execute();
    $intermediate = $statement->get_result();
    $db->close();
    return $intermediate;   
}
function followListPlatformPreference($currentUser)
{
    $db = getDB();
    $sql ="SELECT available_on.platform, COUNT(*) AS platformCount FROM follow
    JOIN content_creator ON follow.creatorID = content_creator.creatorID
    LEFT JOIN plays ON content_creator.creatorID = plays.creatorID
    LEFT JOIN games ON plays.gameID = games.gameID
    LEFT JOIN available_on ON games.gameID = available_on.gameID
    WHERE follow.userID = ?
    GROUP BY available_on.platform
    ORDER BY platformCount DESC, available_on.platform DESC";
    $statement = $db->prepare($sql);
    $statement->bind_param("s", $currentUser);
    $statement->execute();
    $intermediate = $statement->get_result();
    $db->close();
    return $intermediate;
}
function countFavorite($currentUser)
{
    $db = getDB();
    $sql = "SELECT COUNT(*) AS count FROM favorite WHERE userID = ?";
    $statement = $db->prepare($sql);
    $statement->bind_param("s", $currentUser);
    $statement->execute();
    $intermediate = $statement->get_result();
    $result = $intermediate->fetch_assoc();
    $favoriteCount = $result["count"];
    $db->close();
    return $favoriteCount;
}
function countFollowing($currentUser)
{
    $db = getDB();
    $sql = "SELECT COUNT(*) AS count FROM follow WHERE userID = ?";
    $statement = $db->prepare($sql);
    $statement->bind_param("s", $currentUser);
    $statement->execute();
    $intermediate = $statement->get_result();
    $result = $intermediate->fetch_assoc();
    $followingCount = $result["count"];
    $db->close();
    return $followingCount;
}
function publisherPreference($currentUser)
{
    $db = getDB();
    $sql ="SELECT published.developer, COUNT(*) AS publisherCount FROM favorite
    JOIN games ON favorite.gameID = games.gameID 
    JOIN published ON games.gameID = published.gameID
    WHERE favorite.userID =?
    GROUP BY published.developer
    ORDER BY publisherCount DESC";
    $statement = $db->prepare($sql);
    $statement->bind_param("s", $currentUser);
    $statement->execute();
    $intermediate = $statement->get_result();
    $db->close();
    return $intermediate;
}
function followListPublisherPreference($currentUser)
{
    $db = getDB();
    $sql = "SELECT published.developer, COUNT(*) AS publisherCount FROM follow
    JOIN content_creator ON follow.creatorID = content_creator.creatorID 
    LEFT JOIN plays ON content_creator.creatorID = plays.creatorID
    LEFT JOIN games ON plays.gameID = games.gameID
    LEFT JOIN published ON games.gameID = published.gameID
    WHERE follow.userID = ?
    GROUP BY published.developer
    ORDER BY publisherCount DESC, published.developer DESC";
    $statement = $db->prepare($sql);
    $statement->bind_param("s", $currentUser);
    $statement->execute();
    $intermediate = $statement->get_result();
    $db->close();
    return $intermediate;
}
function addQuote($text)
{
    return("\"". $text ."\"");
}
?>
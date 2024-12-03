<?php
function getDB() {
    $host = 'localhost';
    $db = 'game_nexus';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    return $mysqli;
}
function checkUser($userToBeCheck)
{
    $db = getDB();
    $sql = "SELECT userID FROM users WHERE userID=?";
    $statement = $db->prepare($sql);
    $statement->bind_param("s",$userToBeCheck);
    $statement->execute();
    $intermediate = $statement->get_result();
    $result = $intermediate->fetch_assoc();
    $db->close();
    if(!$result)
    {
        return false;
    }
    else
    {
        return true;
    }
}
function getPassword($currentUser)
{
    $db = getDB();
    $sql = "SELECT userPass FROM users WHERE userID =?";
    $statement = $db->prepare($sql);
    $statement->bind_param("s",$currentUser);
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
        <th>". $inputRow["gameName"]."</th>
        <th>". $inputRow["releaseDate"]."</th>
        <th>". $inputRow["reviews"]."</th>
        <th>". $inputRow["rating"]."</th>
        <th>". $inputRow["descriptions"]."</th>
        <td><button class='btn favoriteBtn' name='favoriteGameName' value=". $inputRow["gameName"].">Add to your game list</button></td>
    </tr>";
}

function printCreator($inputRow) {
    echo "
        <tr>
            <td>{$inputRow['creatorName']}</td>
            <td>{$inputRow['platforms']}</td>
            <td>{$inputRow['genres']}</td>
            <td>{$inputRow['games']}</td>
            <td><button class='btn favoriteBtn' name='favoriteCreator' value=". $inputRow["creatorName"].">Add to your game list</button></td>
        </tr>";
}
?>
?>

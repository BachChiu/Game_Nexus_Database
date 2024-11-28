<?php
function getDB()
{
    $host = "75.40.51.27";
    $username = "gameNexusUser";
    $password = "Boon1/3Noob";
    $database = "game_nexus";

    return new mysqli($host,$username,$password,$database);
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
function printGame($inputRow)
{
    echo "
    <tr>
        <th>". $inputRow["gameName"]."</th>
        <th>". $inputRow["releaseDate"]."</th>
        <th>". $inputRow["reviews"]."</th>
        <th>". $inputRow["rating"]."</th>
        <th>". $inputRow["descriptions"]."</th>
    </tr>";
}
?>
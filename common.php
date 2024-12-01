<?php
function getDB()
{
    //Implementation hidden due to sensitive data, the program works on the actual hosted site (http://75.40.51.27/Game_Nexus_Database/home.php)
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
        <td>". $inputRow["gameName"]."</td>
        <td>". $inputRow["releaseDate"]."</td>
        <td>". $inputRow["reviews"]."</td>
        <td>". $inputRow["rating"]."</td>
        <td>". $inputRow["descriptions"]."</td>
        <td><button class='btn favoriteBtn' name='favoriteGameID' value=". $inputRow["gameID"].">Add to your game list</button></td>
    </tr>";
}
?>
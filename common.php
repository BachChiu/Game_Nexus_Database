<?php
function getDB()
{
    $host = "75.40.51.27";
    $username = "gameNexusUser";
    $password = "Boon1/3Noob";
    $database = "game_nexus";

    return new mysqli($host,$username,$password,$database);
}
?>
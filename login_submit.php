<?php
    session_start();
    $_SESSION['error'] = '';
    require('./common.php');
    if($_SERVER["REQUEST_METHOD"] == 'POST')
    {
        $username = $_POST['username'];
        $pass = $_POST['password'];
        $valid = checkUser($username);
        if(!$valid)
        {
            $_SESSION['error'] = 'Invalid username';
            header('Location: ./login.php');
        }
        else
        {
            $db = getDB();
            $sql = "SELECT userPass FROM users WHERE userID =?";
            $statement = $db->prepare($sql);
            $statement->bind_param("s",$username);
            $statement->execute();
            $intermediate = $statement->get_result();
            $result = $intermediate->fetch_assoc();
            $actualPass = $result["userPass"];
            $db->close();
            if(strcmp($pass, $actualPass) == 0)
            {
                $_SESSION['user_authentication'] = $username;
                header('Location: ./profile.html');
            }
            else
            {
                $_SESSION['error'] = 'Invalid password';
                header('Location: ./login.php');
            }
        }
    }
?>
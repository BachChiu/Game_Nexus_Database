<?php
    session_start();
    $_SESSION['error'] = '';
    require_once('./common.php');
    if($_SERVER["REQUEST_METHOD"] == 'POST')
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if(strcmp($_POST['password'],$_POST['passConfirmation']) == 0)
        {
            $existingUser = checkUser($username);
            if(!$existingUser)
            {
                $db = getDB();
                $sql = "INSERT INTO users(userID, userPass) VALUES (?,?)";
                $statement = $db->prepare($sql);
                $statement->bind_param("ss",$username,$password);
                $statement->execute();
                $db->close();
                #Not an actual error, just figure its easier to make use of the existing code
                $_SESSION['error'] = 'Successful registration, you can now login with your account information.';
                header('Location: ./login.php');
            }
            else
            {
                $_SESSION['error'] = 'The username is already being used, please try again';
                header('Location: ./home.php');
            }
        }
        else
        {
            $_SESSION['error'] = 'The passwords did not match each other, please try again';
            header('Location: ./home.php');
        }
    }
?>
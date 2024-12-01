<?php
    session_start();
    $_SESSION['error'] = '';
    require_once('./common.php');
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
            $actualPass = getPassword($username);
            if(strcmp($pass, $actualPass) == 0)
            {
                $_SESSION['user_authentication'] = $username;
                header('Location: ./profile.php');
            }
            else
            {
                $_SESSION['error'] = 'Invalid password';
                header('Location: ./login.php');
            }
        }
    }
?>
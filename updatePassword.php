<?php
    session_start();
    $_SESSION['error'] = '';
    require('./common.php');
    $currentUser = $_SESSION['user_authentication'];
    if($_SERVER["REQUEST_METHOD"] == 'POST')
    {
        if(strcmp($_POST['password'],$_POST['passConfirmation']) == 0)
        {
            $newPass = $_POST['password'];
            $currentPass = $_POST['oldPassword'];
            $actualPass = getPassword($currentUser);
            if(strcmp($currentPass, $actualPass) == 0)
            {
                $db = getDB();
                $sql = "UPDATE users SET userPass = ? WHERE userID = ?";
                $statement = $db->prepare($sql);
                $statement->bind_param("ss",$newPass, $currentUser);
                $statement->execute();
                $db->close();
                $_SESSION['error'] = 'Your password has been successfully updated';
                header('Location: ./profile.php');
            }
            else
            {
                $_SESSION['error'] = 'Current password is incorrect, please try again';
                header('Location: ./profile.php');
            }
        }
        else
        {
            $_SESSION['error'] = 'The new passwords did not match each other, please try again';
            header('Location: ./profile.php');
        }    
    }
?>
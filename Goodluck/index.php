<?php
if(!isset($_SESSION)){
    session_start();  
}

if (!isset($_SESSION['tag'])) {
    $_SESSION['msg'] = "You must log in first";
    header('Location: ./loginUser.php');
}else{
    header('Location: ./playgame.php');
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ./loginUser.php");
}
?>

<?php
require('inputUserData.php');

if (isset($_POST['update'])) {
    updateHighScore($_POST['seed'],$_POST['score']);
}
if (isset($_POST['set'])) {
    addPuzzle($_POST['seed'],$_POST['score'], $_POST['puzname']);
}
header("Location: ./puzzleList.php");
?>



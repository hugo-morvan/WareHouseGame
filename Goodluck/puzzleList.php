<?php
if(!isset($_SESSION)){
    session_start();  
}
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Puzzle List</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <nav>
            <input type="checkbox" id="check">
            <label for="check" class="checkbtn">
                <i class="fas fa-bars"></i>
            </label>
            <label class="logo">Warehouse Game</label>
            <ul>
                <li><a href="playgame.php">Play</a></li>
                <li><a href="instruction.html">Instructions</a></li>
                <li><a class="active" href="puzzleList.php">Puzzle list</a></li>
            </ul>
        </nav>
        
        <section>
            <div class="little-box">
            <b>Logged in as <?php echo $_SESSION['tag'] ?> </b>
            <?php if (isset($_SESSION['tag'])) : ?>
                    <p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
            <?php endif ?>
            </div>
            <div>
                <h1 class="inst">Puzzle List</h1>
                <div class="content2">
                <?php
            require('dblogin.php');  //download or copy: defines $db_host,...,$db_name
            $con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
            if (!$con) {
                die("could not connect to db: <br />" . mysqli_error($con));
            }
            //print " Successful connection!!";

            $q = "SELECT * FROM `puzzles`";      //a very simple query. NOTE the funky ` tick marks NOT '
            $r = mysqli_query($con, $q);           //making the query
            if (!$r) {
                die("query failed: <br />" . mysqli_error($con));
            }
            print "<table class='content-table'> <thead> <tr> <th>Puzzle Name </th><th> Best Player </th><th> Cost </th></tr></thead>";
            print" <tbody>";
            while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                $puz = $row['puzname'];    //Yes arrays are maps. puzname is a column name
                $nm = $row['recname'];
                $sc = $row['score'];
                $seed = $row['seedlev'];
                $date =$row['when']; //<th> $date <th>
                if ($_SESSION['tag']== $nm){
                    $u = 'style=color:red;';
                }else{
                    $u= '';
                }
                print "<tr> <th> <a href='playgame.php?seed=$seed'>$puz</a> </th> <th $u> $nm </th><th> $sc </th> <tr>";
            }
            print "</tbody></table>"
            ?>
                </div>
            </div>
        </section>
    </body>
</html>

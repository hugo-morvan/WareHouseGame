<?php
if(!isset($_SESSION)){
    session_start();  
}
// initializing variables
$errors = array();

// connect to the database
require('dblogin.php');  //download or copy: defines $db_host,...,$db_name
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$con) {
    die("could not connect to db: <br />" . mysqli_error($con));
}

// ---------------REGISTER USER---------------
if (isset($_POST['reg_user'])) {
// receive all input values from the form
    $tag = mysqli_real_escape_string($con, filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_SPECIAL_CHARS));
    $email = mysqli_real_escape_string($con, filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password_1 = mysqli_real_escape_string($con, filter_input(INPUT_POST, 'password_1', FILTER_SANITIZE_SPECIAL_CHARS));
    $password_2 = mysqli_real_escape_string($con, filter_input(INPUT_POST, 'password_2', FILTER_SANITIZE_SPECIAL_CHARS));

// form validation: ensure that the form is correctly filled ...
// by adding (array_push()) corresponding error unto $errors array
    if (empty($tag)) {
        array_push($errors, "Username is required");
    }
    if (empty($email)) {
        array_push($errors, "Email is required");
    }
    if (empty($password_1)) {
        array_push($errors, "Password is required");
    }
    if ($password_1 != $password_2) {
        array_push($errors, "The two passwords do not match");
    }

// first check the database to make sure 
// a user does not already exist with the same username and/or email
    $user_check_query = "SELECT * FROM users WHERE tag='$tag' OR email='$email' LIMIT 1";
    $result = mysqli_query($con, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) { // if user exists
        if ($user['tag'] === $tag) {
            array_push($errors, "Username already exists");
        }

        if ($user['email'] === $email) {
            array_push($errors, "email already exists");
        }
    }
// Finally, register user if there are no errors in the form
    if (count($errors) == 0) {
        $password = md5($password_1); //encrypt the password before saving in the database

        $query = "INSERT INTO users (tag, email, password) 
  			  VALUES('$tag', '$email', '$password')";
        mysqli_query($con, $query);
        $_SESSION['tag'] = $tag;
        $_SESSION['success'] = "You are now logged in";
        
        header('Location: ./playgame.php');
    }
}
//-------------LOGIN--------------
if (isset($_POST['login_user'])) {
    $tag = mysqli_real_escape_string($con, filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = mysqli_real_escape_string($con, filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

    if (empty($tag)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $password = md5($password);
        $query = "SELECT * FROM users WHERE tag='$tag' AND password='$password'";
        $results = mysqli_query($con, $query);
        if (mysqli_num_rows($results) == 1) {
            $_SESSION['tag'] = $tag;
            $_SESSION['success'] = "You are now logged in";
            header('Location: ./playgame.php');
        } else {
            array_push($errors, "Wrong username/password combination");
        }
    }
}
//
//
function isNewPuzzle($seed) {
    require('dblogin.php');
    $con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    $puz_in_list_query = "SELECT * FROM puzzles WHERE `seedlev`='$seed'";
    $my_q = mysqli_query($con, $puz_in_list_query);
    if(mysqli_num_rows($my_q)==0){ //if the puzzle is not in the list return 1
        print "1";
    } else {
        print "0"; //if the puzzle is in th elist return 0
    }
}
//--------------Win update score---------------
function winscript($seed) {
    require('dblogin.php');
    $con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$con) {
        die("could not connect to db: <br />" . mysqli_error($con));
    }$query = "SELECT `score` FROM `puzzles` WHERE `seedlev`='$seed'";
    $high_score = mysqli_query($con, $query);
    if (!$high_score) {
        die("query failed: <br />" . mysqli_error($con));
    }$result = $high_score->fetch_array()[0] ?? '';
    if ($result === '') {
        echo '9999';
    } else {
        echo $result;
    }
}

//-----------Update high score--------------
function updateHighScore($seed, $score) {
    $db_user = 'root'; $db_pass = 'root'; $db_name = 'warehousedb'; $db_host = '127.0.0.1';
    $con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    $date = date('y-m-d h:i:s');
    $intscore = (int)$score;
    $intseed = (int)$seed;
    $user = $_SESSION['tag'];
    $query1 = "UPDATE `puzzles` SET `score`=$intscore, `recname`='$user', `when`='$date' WHERE `seedlev`='$intseed';";
   
    if ($con->query($query1) === TRUE) {
       // echo "Score updated successfully";
    } else {
       // echo "Error updating database: " . $con->error;
    }$con->close();
   // header('location: ./puzzleList.php');
}

//-------------Add new Puzzle-------------
function addPuzzle($seed, $score, $name) {
    $db_user = 'root'; $db_pass = 'root'; $db_name = 'warehousedb'; $db_host = '127.0.0.1';
    $con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    $intscore = (int)$score;
    $intseed = (int)$seed;
    $user = $_SESSION['tag'];
    $query = "INSERT INTO `puzzles` (`seedlev`, `puzname`, `name`,`recname`,`score`,`when`)
                            VALUES ($intseed, '$name', '$user','$user',$intscore,NOW())";
    if ($con->query($query) === TRUE) {
        //echo "New record created successfully";
    } else {
       // echo "Error: " . $query . "<br>" . $con->error;
    }
    $con->close();
   
}

?>

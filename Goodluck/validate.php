<?php

/*
 * A set of functions for validating form data.
 */

function validAlphaString($s) {   //all alphabetic an spaces, no spaces on ends
    $s = trim($s);
    $p = '/^[A-Za-z\s]+$/';
    return (boolean) preg_match($p, $s);
}

function validNumber($s) { //one or more digits
    $s = trim($s);
    $p = '/^\d+$/';
    return (boolean) preg_match($p, $s);
}

function validEmail($s) {    // stuff@stuff.stuff  where stuff is 1 or more word chars and periods
    $s = trim($s);
    $p = '/^[\w.]+\@[\w.]+\.\w+$/';
    return (boolean) preg_match($p, $s);
}

function validPass($s) { /* all in ascii !..~  */
    $p = '/^[!-~]+$/';
    return (boolean) preg_match($p, $s);
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function contains($str, array $arr) {
    foreach ($arr as $a) {
        if (stripos($str, $a) !== false) {
            return true;
        }
    }return false;
}

?>
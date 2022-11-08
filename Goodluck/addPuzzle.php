<?php include('inputUserData.php') ?>
<!DOCTYPE html>
<!--
I needed to pu ththis form in a seperate fill because the keyboard was not picking the letters but rather the bobcat movement
-->
<html lang="en">
    <head>
        <title>Add puzzle</title>
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
    </head>
    <body>
        <nav>
            <label class="logo">Warehouse Game</label> 
        </nav>
        <section>
            <h1 class ="inst">Login</h1>
            <div id="login">
                <ul><li>
                        <form method='post' action='forms.php'>
                            <label> Choose a name for the puzzle</label>
                            <input type='text' name='puzname' />
                            <input type='submit' name='set' class='button' value='Add puzzle' />
                            <input hidden type='text' name='seed'  value="<?php echo filter_input(INPUT_POST, 'seed', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
                            <input hidden type='text' name='score' value="<?php echo filter_input(INPUT_POST, 'score', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
                            <input hidden type='text' name='tag' value="<?php echo filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_SPECIAL_CHARS);?>" />
                        </form>
                    </li></ul>
                
            </div>
        </section>

    </body>
</html>

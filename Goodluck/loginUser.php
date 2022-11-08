<?php include('inputUserData.php') ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
    </head>
    <body>
        <nav>
            <label class="logo">Warehouse Game</label> 
        </nav>
        <section>
            <h1 class ="inst">Login</h1>
            <div id="login" style="text-align: center">
                <ul><li>
                        <form method="post" action="loginUser.php">
                            <?php include('errors.php'); ?>
                            <div class="input-group">
                                <label>Username</label>
                                <input type="text" name="tag" >
                            </div>
                            <div class="input-group">
                                <label>Password</label>
                                <input type="password" name="password">
                            </div>
                            <div class="input-group">
                                <button type="submit" class="button" name="login_user">Login</button>
                            </div>
                            <p>
                                Not registered? <a href="register.php">Sign up</a>
                            </p>
                        </form>
                    </li></ul>

            </div>
        </section>

    </body>
</html>
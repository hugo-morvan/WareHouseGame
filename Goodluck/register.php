<?php include('inputUserData.php') ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Registration</title>
        <link rel="stylesheet" type="text/css" href="stylesheet.css">
    </head>
    <body>
        <nav>
            <label class="logo">Warehouse Game</label>
        </nav>
        <section>
            <h1 class ="inst">Register</h1>
            <div style="text-align:center">
                <ul><li>
                        <form method="post" action="register.php">
                            <?php include('errors.php'); ?>
                            <div class="input-group">
                                <label>Username</label>
                                <input type="text" name="tag" value="<?php echo $tag; ?>">
                            </div>
                            <div class="input-group">
                                <label>Email</label>
                                <input type="email" name="email" value="<?php echo $email; ?>">
                            </div>
                            <div class="input-group">
                                <label>Password</label>
                                <input type="password" name="password_1">
                            </div>
                            <div class="input-group">
                                <label>Confirm password</label>
                                <input type="password" name="password_2">
                            </div>
                            <div class="input-group">
                                <button  type="submit" class="button" name="reg_user">Register</button>
                            </div>
                            <p>
                                Already registered? <a href="loginUser.php">Sign in</a>
                            </p>
                        </form>
                    </li></ul>


            </div>
        </section>
    </body>
</html>
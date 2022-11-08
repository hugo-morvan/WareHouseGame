<?php include('inputUserData.php') ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Warehouse Game</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="stylesheet.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script type="text/javascript">
            "use strict";

            var seed = "<?php $seed = filter_input(INPUT_GET, 'seed', FILTER_SANITIZE_SPECIAL_CHARS);echo $seed ?>";
            var username = " <?php $user = $_SESSION['tag'];echo $user ?> ";
            console.log("puzzle seed: " + seed);
            if (seed === "") { //if it is a random puzzle
                seed = parseInt(Math.random() * 100000);
                console.log("generated seed: " + seed);
            }
            function restart() { //have to use a function so that it is called after the page is fully loaded.
                document.getElementById("restart").innerHTML = "<a href='playgame.php?seed=" + seed + "' style='background:white; border: 1px solid red; border-radius: 3px; text-decoration: none; padding:1px; font-weight:bold; margin:5px;'>Restart</a>";
                console.log("restart seed set");
            } //restart button function
            var Util = {
                random: function (newSeed) {   //[0 - 1.0)  optional seeding     
                    if (newSeed)
                        Util.seed = newSeed;
                    Util.seed = (Util.seed * 9301 + 49297) % 233280;
                    var ret = Util.seed / 233280;
                    if (ret >= 1)
                        ret = .99999; //maybe rounding error?
                    return ret;
                },
                drawRotatedImage: function (ctx, theta, image, x, y, wid, ht) {
                    //using context ctx, draws image rotated theta radians cw and
                    //scaled to wid, ht  with its ulc at x, y, 
                    var xcen = x + wid / 2, ycen = y + ht / 2;
                    ctx.save();
                    ctx.translate(xcen, ycen);
                    ctx.rotate(theta);
                    ctx.translate(-xcen, -ycen);
                    ctx.drawImage(image, x, y, wid, ht);
                    ctx.restore();
                },
                getCookie: function (name) {  //return value of cookie with givn name (or "")
                    if (document.cookie.length > 0) {
                        let s = document.cookie.indexOf(name + "=");
                        if (s !== -1) {
                            s = s + name.length + 1;
                            let e = document.cookie.indexOf(";", s);
                            if (e === -1)
                                e = document.cookie.length;
                            return unescape(document.cookie.substring(s, e));
                        }
                    }
                    return "";
                }
            }; //various tools
            let Game = function (seed) {  //THIS IS A CONSTRUCTOR FOR A GAME OBJECT it must be called us new
                //private game data
                var boom = new Audio('sounds/boom.wav');
                var vroom = new Audio('sounds/vroom.wav');
                var ran = Util.random(seed);
                let n = parseInt(ran * 5) + 8; //number of rows and columns    
                let b = []; //initb (below) initializes to random weights and n x n
                let bob = {}; //the bobcat has a row,column, and direction (use 0=North,1=E,2=S,3=W)
                let score = 0;
                let crateWeight = function () {
                    let num = Util.random();
                    if (num < 0.5)
                        return 0;
                    else if (num < 0.75)
                        return 1;
                    else if (num < 0.9)
                        return 2;
                    else
                        return 3;
                };
                b = []; //board initialisation using crateWeight poundarated random
                for (var i = 0; i < n; i++) {
                    let a = [];
                    for (var j = 0; j < n; j++) {
                        a[j] = crateWeight();
                    }
                    b[i] = a;
                }
                //target crate init.
                let target_row = parseInt(Util.random() * (n - 2) + 1);
                let target_col = parseInt(Util.random() * (n - 2) + 1);
                b[target_row][target_col] += 5; //target crate has +5 weight
                if (b[target_row][target_col] === 5)
                    b[target_row][target_col] += 1; //target crate cannot be zero weigth
                //bob start position init
                bob.row = n - 1;
                bob.col = parseInt(Util.random() * (n - 1)); //random position on the bottom row
                bob.dir = 0; //headed north
                b[bob.row][bob.col] = 0; //bob cannot start ontop of a crate

                const initRow = bob.row;
                const initCol = bob.col;

                // ===========  public methods  =================

                function crateUpdate(d) {
                    let dr = [-1, 0, 1, 0]; //nesw
                    let dc = [0, 1, 0, -1];
                    let one_front;
                    try {
                        one_front = b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]]; //the crate in front of the bobcat
                    } catch (e) {
                        console.log("location out of bound");
                    }
                    if (one_front === undefined) //our of bound
                        one_front = 4; //not movable
                    console.log("1front : " + one_front);
                    //-----------
                    let two_front;
                    try {
                        two_front = b[bob.row + dr[bob.dir] * 2][bob.col + dc[bob.dir] * 2];
                    } catch (e) {
                        console.log("location out of bound");
                    }
                    if (two_front === undefined)
                        two_front = 4; //not movable
                    console.log("2front : " + two_front);
                    let three_front;
                    try {
                        three_front = b[bob.row + dr[bob.dir] * 3][bob.col + dc[bob.dir] * 3];
                    } catch (e) {
                        console.log("location out of bound");
                    }
                    if (three_front === undefined)
                        three_front = 4; //not movable
                    console.log("3front : " + three_front);
                    let four_front;
                    try {
                        four_front = b[bob.row + dr[bob.dir] * 4][bob.col + dc[bob.dir] * 4];
                    } catch (e) {
                        console.log("location out of bound");
                    }
                    if (four_front === undefined)
                        four_front = 4; //not movable
                    console.log("4front : " + four_front);
                    if (two_front === 0 && one_front <= 3) {
                        b[bob.row + dr[bob.dir] * 2][bob.col + dc[bob.dir] * 2] = one_front;
                        b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]] = 0;
                        //insert puschiing sound
                        move_bob(d);
                        return;
                    }
                    //case 2
                    else if ((one_front + two_front) <= 3 && three_front === 0) { //can only push crats if total weight is >= 3 and empty spot
                        b[bob.row + dr[bob.dir] * 3][bob.col + dc[bob.dir] * 3] = two_front;
                        b[bob.row + dr[bob.dir] * 2][bob.col + dc[bob.dir] * 2] = one_front;
                        b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]] = 0;
                        //insert pushing sound
                        move_bob(d);
                        return;
                    } else if ((one_front + two_front + three_front) <= 3 && four_front === 0) { //case where pushing three 1s crate
                        b[bob.row + dr[bob.dir] * 4][bob.col + dc[bob.dir] * 4] = three_front;
                        b[bob.row + dr[bob.dir] * 3][bob.col + dc[bob.dir] * 3] = two_front;
                        b[bob.row + dr[bob.dir] * 2][bob.col + dc[bob.dir] * 2] = one_front;
                        b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]] = 0;
                        //insert pushing sound
                        move_bob(d);
                        return;
                    } else if (one_front === 6 || one_front === 7 || one_front === 8) { //target crate in one front
                        if (two_front === 0) {
                            b[bob.row + dr[bob.dir] * 2][bob.col + dc[bob.dir] * 2] = one_front;
                            b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]] = 0;
                            move_bob(d);
                            return;
                        } else if ((one_front + two_front) <= 8 && three_front === 0) {
                            b[bob.row + dr[bob.dir] * 3][bob.col + dc[bob.dir] * 3] = two_front;
                            b[bob.row + dr[bob.dir] * 2][bob.col + dc[bob.dir] * 2] = one_front;
                            b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]] = 0;
                            //insert pushing sound
                            move_bob(d);
                            return;
                        } else if ((one_front + two_front + three_front) <= 8 && four_front === 0) {
                            b[bob.row + dr[bob.dir] * 4][bob.col + dc[bob.dir] * 4] = three_front;
                            b[bob.row + dr[bob.dir] * 3][bob.col + dc[bob.dir] * 3] = two_front;
                            b[bob.row + dr[bob.dir] * 2][bob.col + dc[bob.dir] * 2] = one_front;
                            b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]] = 0;
                            //insert pushing sound
                            move_bob(d);
                            return;
                        }
                    } else if (two_front === 6 || two_front === 7 || two_front === 8) { //target crate in two-front
                        if (one_front > 0 && (one_front + two_front) <= 8 && three_front === 0) {
                            b[bob.row + dr[bob.dir] * 3][bob.col + dc[bob.dir] * 3] = two_front;
                            b[bob.row + dr[bob.dir] * 2][bob.col + dc[bob.dir] * 2] = one_front;
                            b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]] = 0;
                            //insert pushing sound
                            move_bob(d);
                            return;
                        } else if (one_front > 0 && (one_front + two_front + three_front) <= 8 && four_front === 0) {
                            b[bob.row + dr[bob.dir] * 4][bob.col + dc[bob.dir] * 4] = three_front;
                            b[bob.row + dr[bob.dir] * 3][bob.col + dc[bob.dir] * 3] = two_front;
                            b[bob.row + dr[bob.dir] * 2][bob.col + dc[bob.dir] * 2] = one_front;
                            b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]] = 0;
                            //insert pushing sound
                            move_bob(d);
                            return;
                        }
                    } else if (three_front === 6 || three_front === 7 || three_front === 8) { //target crate in three-front
                        if (one_front > 0 && two_front > 0(one_front + two_front + three_front) <= 8 && four_front === 0) {
                            b[bob.row + dr[bob.dir] * 4][bob.col + dc[bob.dir] * 4] = three_front;
                            b[bob.row + dr[bob.dir] * 3][bob.col + dc[bob.dir] * 3] = two_front;
                            b[bob.row + dr[bob.dir] * 2][bob.col + dc[bob.dir] * 2] = one_front;
                            b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]] = 0;
                            //insert pushing sound
                            move_bob(d);
                            return;
                        }
                    } else { //no movement possible, return error message
                        console.log("Not possible to move");
                        //insert error sound
                        return;
                    }
                }
                ;
                function move_bob(d) {
                    let dr = [-1, 0, 1, 0]; //nesw
                    let dc = [0, 1, 0, -1];
                    if (bob.row + dr[d] >= 0 && bob.row + dr[d] < n) {
                        bob.row += dr[d];
                        score += 1;
                        
                        vroom.play();
                    }
                    if (bob.col + dc[d] >= 0 && bob.col + dc[d] < n) {
                        bob.col += dc[d];
                    }
                }
                ;
                this.move = function (dirch) {
                    //dirch is converted to  0123 meaning NESW 
                    let d = -1;
                    if (dirch === 38)
                        d = 0; //arrow up
                    if (dirch === 37)
                        d = 3; //arrow left
                    if (dirch === 40)
                        d = 2; //arrow down
                    if (dirch === 39)
                        d = 1; //arrow right 
                    if (dirch === 32)
                        d = 5; //spacebar
                    if (d < 0)
                        return; //ignore bad keys

                    let dr = [-1, 0, 1, 0]; //nesw
                    let dc = [0, 1, 0, -1];
                    if (d === 5) { //----------------------------crate explosion
                        b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]] = 0;
                        
                        boom.play();
                        console.log("explosion sound");
                        score += 10;
                    } else if (d === bob.dir) {   //move the bob one cell in its direction
                        let target;
                        try {
                            target = b[bob.row + dr[bob.dir]][bob.col + dc[bob.dir]]; //the crate in front of the bobcat
                        } catch (e) {
                            console.log("location out of bound");
                        }
                        if (target > 0) { // movecrate then move
                            crateUpdate(d);
                        } else { //move directly
                            move_bob(d);
                        }
                    } else { //just turning
                        bob.dir = d;
                    }
                };
                this.getScore = function () { //displays the scores
                    document.getElementById("score").innerHTML = "Cost: $" + score;
                };
                //--------------------------------------------------------------------------------------
                this.winScript = function (seed) {
                    if (b[initRow][initCol] === 6 || b[initRow][initCol] === 7 || b[initRow][initCol] === 8) {//the player has won
                        console.log("winScript trigered");
            
                        document.getElementById("win").innerHTML = "You win ! Your score is " + score + " on the seed " + seed;
                        document.cookie = "scorePHP="+score+"";
                         var check =<?php echo isNewPuzzle($seed)?>;          
                        if(check===0){ //aka if the puzzle is in the list
                            console.log("seed is <?php echo $seed ?>");
                            var highScore = <?php winscript($seed) ?>;
                            console.log("highscore " + highScore);

                            if (highScore === '') {
                                console.log("No best score detected");
                                
                            } else if (highScore >= score) { //-------------the intersting part
                                console.log("New best score !");
                                document.getElementById("win").innerHTML = "You win ! Your score of " + score + " is the new best score on the seed " + seed;
                                //update puzzlelist here
                                document.getElementById("control").innerHTML = "<form method='post' action='forms.php'><input type='submit' name='update' class='button' value='Update score' /><input hidden type='text' name='seed'  value='"+seed+"' /><input hidden type='text' name='score' value='"+score+"' /><input hidden type='text' name='tag' value='"+username+"' /></form>";
                                
                            } else if (score >= highScore) {
                                console.log("Not a new best score");
                                document.getElementById("win").innerHTML = "You win ! Your score of " + score + " is higher than the best score of " + highScore + " on the seed " + seed;
                            }   
                        }else {// aka if the puzzle is not in the list
                            console.log("New best Score (new puzzle)");
                            //to do : ask the user if he wants to add the puzzle to the list
                            
                            document.getElementById("control").innerHTML = "<form method='post' action='addPuzzle.php'><input type='submit' name='set' class='button' value='Save puzzle' /><input hidden type='text' name='seed'  value='"+seed+"' /><input hidden type='text' name='score' value='"+score+"' /><input hidden type='text' name='tag' value='"+username+"' /></form>";
                            
                            
                        };
                    }
                    ;
                };
                //-------------------------------------------------------------------------
                Game.prototype.toString = function () {
                    let out = "";
                    for (let r = 0; r < n; r++) {
                        for (let c = 0; c < n; c++) {
                            //-----sprites-------
                            if (r === bob.row && c === bob.col) {//bobcat
                                if (bob.dir === 0)
                                    out = out + "<img src='images/bob_up.png' alt='^' style='width: 30px , height: 30px'/>";
                                else if (bob.dir === 1)
                                    out = out + "<img src='images/bob_right.png' alt='>' style='width: 30px , height: 30px'/>";
                                else if (bob.dir === 2)
                                    out = out + "<img src='images/bob_down.png' alt='v' style='width: 30px ,height: 30px'/>";
                                else
                                    out = out + "<img src='images/bob_left.png' alt='<' style='width: 30px ,height: 30px'/>";
                            } else if (r === initRow && c === initCol)//entrance
                                out = out + "<img src='images/start.png' alt='0' style='width: 30px, height: 30px, z=0'/>";
                            //crates
                            else if (b[r][c] === 0)
                                out = out + "<img src='images/crate0.png' alt='0' style='width: 30px, height: 30px'/>";
                            else if (b[r][c] === 1)
                                out = out + "<img src='images/crate1.png' alt='1' style='width: 30px ,height: 30px'/>";
                            else if (b[r][c] === 2)
                                out = out + "<img src='images/crate2.png' alt='2' style='width: 30px, height: 30px'/>";
                            else if (b[r][c] === 3)
                                out = out + "<img src='images/crate3.png' alt='3' style='width: 30px ,height: 30px'/>";
                            //target crates
                            else if (b[r][c] === 6)
                                out = out + "<img src='images/crate_target1.png' alt='t1' style='width: 30px ,height: 30px'/>";
                            else if (b[r][c] === 7)
                                out = out + "<img src='images/crate_target2.png' alt='t2' style='width: 30px, height: 30px'/>";
                            else if (b[r][c] === 8)
                                out = out + "<img src='images/crate_target3.png' alt='t3' style='width: 30px ,height: 30px'/>";
                            out = out + " ";
                        }
                        out = out + "\n";
                    }
                    return "<pre>" + out + "</pre>"; //avoid browser formatting
                     
                };
            }; //=========== end Game ================
            //=======================================================================
            let viewText = function (brd) {   //show in existing div myText    
                let inner = "<h1>" + brd + "</h1>";
                document.getElementById("myText").innerHTML = inner;
            };
            window.onload = function () {     //called when the window has finished loading
                console.log("onload");
                let brd = new Game(seed); //seed input
                restart(); //sets the seed value for the restart of the puzzle
                document.onkeydown = function (ev) {  //keydown event  
                    console.log("down ");
                    let key = ev.keyCode;
                    brd.move(key);
                    brd.getScore();
                    viewText(brd);
                    brd.winScript(seed);
                };
                viewText(brd);
            };
        </script>
    </head>
    <body>
        <nav>
            <input type="checkbox" id="check">
            <label for="check" class="checkbtn">
                <i class="fas fa-bars"></i>
            </label>
            <label class="logo">Warehouse Game</label>
            <ul>
                
                <li><a class="active" href="playgame.php">Play</a></li>
                <li><a href="instruction.html">Instructions</a></li>
                <li><a href="puzzleList.php">Puzzle list</a></li>
            </ul>
        </nav>
        <section>
            <div id='content'>
                <div id='myText'></div>
                <div id='score' style="font-weight:bold;"></div>
                <div id='restart'></div>
                <div id='win' style="font-weight:bold;"></div>
                <div id='control'>
                    <form id="f1" method="POST">
                        <input type="hidden" id="scorePHP" name="scorePHP" value="">
                    </form>
                </div>
            </div>
            <div class="little-box">
            <b>Logged in as <?php echo $_SESSION['tag'] ?> </b>
            <?php if (isset($_SESSION['tag'])) : ?>
                    <p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
            <?php endif ?>
            </div>
        </section>
    </body>
</html>

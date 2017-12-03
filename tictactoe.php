<?php

/**
 *  
 *  
 *  
 *
 *  CSI 3120, Assignment #4 - Part 1 - Question #1
 *
 *  Tic Tac Toe in PHP
 */

class tictactoe {

    var $player = "X";			// current player
    var $board = array();		// game board - 2D array
    var $totalMoves = 0;		// current total number of moves

    /**
     * Constructor, calls the newGame() method to initialize variables
     */
    function tictactoe() {
        $this->newGame();
    }

    /**
     * Initializes (or resets) attributes
     */
    function newGame() {
        $this->player = "X";     // (re)set current player to X
        $this->totalMoves = 0;   // (re)set total moves to 0
        $this->resetBoard();     // (re)set game board to empty cells
    }

    /**
     * Resets the board to a 2D array of 9x9 empty/null cells
     */
    function resetBoard() {
        $this->board = array();    // initialize board variable to empty array

        // initialize each cell to null
        for ($x = 0; $x <= 2; $x++) {
            for ($y = 0; $y <= 2; $y++) {
                $this->board[$x][$y] = null;
            }
        }
    }

    /**
     * Set up a new turn of the game using the game's state
     *
     * @param array $gameState State of the game passed through POST
     */
    function setupGame($gameState) {
        if ($this->player == "O") {
            $cell = $this->findEmptyCell();
            $gameState["$cell[0]".","."$cell[1]"] = $this->player;
        }

        // if the game is not won/tied and a move was made,
        // i.e. the game is setting up after a "take turn" button click
        if (!$this->gameOver() && isset($gameState['move'])) {
            $this->playTurn($gameState);
        }

        // player pressed the button to start a new game
        if (isset($gameState['newGame'])) {
            $this->newGame();
        }

        $this->display();
    }

    /**
     * Invoked when setupGame finds that a move was made, processes logic of registering player's move
     *
     * @param array $gameState State of the game
     */
    function playTurn($gameState) {
        if ($this->gameOver()) return;  // if the game is already won, terminate move processing

        $gameState = array_unique($gameState);  //remove duplicate entries on the board, i.e. only first 'move' registers

        // iterate through gameState values
        foreach ($gameState as $key => $value) {
            // if value of current gameState element is a board cell with the current player's sign (X/O)
            if ($value == $this->player) {
                $coords = explode(",", $key);   // get row and column from field name,
                $this->board[$coords[0]][$coords[1]] = $this->player;  // then update game board's value accordingly
                $this->player = $this->player == "X"? "O" : "X";  // change the turn to the next player
                $this->totalMoves++;  // increment total moves
            }
        }

        if ($this->gameOver()) return;
    }

    /**
     * Find an empty cell on the board
     */
    function findEmptyCell() {
        $emptyCells = array();
        for ($x = 0; $x <= 2; $x++) {
            for ($y = 0; $y <= 2; $y++) {
                if (!$this->board[$x][$y]){
                    $emptyCells[]= array($x, $y);
                }
            }
        }

        return $emptyCells[rand(0,count($emptyCells)-1)];
    }

    /**
     * display the game board and controls, or the win/tie message
     */
    function display(){
        // if the game is not won or tied
        if (!$this->gameOver()) {
            // opening tag for board HTML element
            echo "<div id=\"board\">";

            // iterate through the 2D array of the game board
            for ($x = 0; $x < 3; $x++) {
                for ($y = 0; $y < 3; $y++) {
                    echo $this->player == "X"? "<div>" : "<div style=\"display: none;\">";  // opening tag for each board cell

                    // if the cell is already filled with a player sign (X/O) then display that
                    if ($this->board[$x][$y]) echo "<div>{$this->board[$x][$y]}</div>";
                    // if the cell is empty
                    else {
                        // display a selection box with:
                        // name: coordinates as 'Row,Col'
                        // 2 options: blank and current player sign (X/O)
                        echo "<select name=\"{$x},{$y}\">
                                <option value=\"\"></option>
                                <option value=\"{$this->player}\">{$this->player}</option>
                              </select>";
                    }

                    echo "</div>";  // closing tag for board cell
                }

                echo "<br>";  // add a line break at the end of each row
            }

            // Take Turn button, submits the game board as a POST form with cells as fields
            // and sets 'move' post parameter so setupGame() knows to process a move
            $btnText = $this->player == "X"? "Take Turn" : "Process Turn";
            echo "<p align=\"center\">
                    <input type=\"submit\" name=\"move\" value=\"{$btnText}\" /><br/>
                    <b>It's player {$this->player}'s turn.</b></p>";

            echo "</div>";  // closing tag for board
        }
        // game is won/tied
        else {
            $msg = $this->gameOver() == "T"? "Game is tied!" : "Player ".$this->gameOver()." wins!"; // pick win or tie message
            echo $this->gameOver() == "O"? "<div class=\"loseMsg\">$msg</div>" : "<div class=\"winMsg\">$msg</div>";  // display formatted message

            session_destroy();  // destroy session so old data is destroyed and new game can be started safely

            // New Game button,
            // sets 'newGame' post parameter so setupGame() knows to start a new game (reset variables)
            echo "<p align=\"center\"><input type=\"submit\" name=\"newGame\" value=\"New Game\" /></p>";
        }
    }

    /**
     * Check if game is won/tied
     */
    function gameOver()
    {
        // if a player won, checks if any player's sign is in a full row, column, or diagonal

        // top row
        if ($this->board[0][0] && $this->board[0][0] == $this->board[0][1] && $this->board[0][1] == $this->board[0][2])
            return $this->board[0][0];

        // middle row
        if ($this->board[1][0] && $this->board[1][0] == $this->board[1][1] && $this->board[1][1] == $this->board[1][2])
            return $this->board[1][0];

        // bottom row
        if ($this->board[2][0] && $this->board[2][0] == $this->board[2][1] && $this->board[2][1] == $this->board[2][2])
            return $this->board[2][0];

        // first column
        if ($this->board[0][0] && $this->board[0][0] == $this->board[1][0] && $this->board[1][0] == $this->board[2][0])
            return $this->board[0][0];

        // second column
        if ($this->board[0][1] && $this->board[0][1] == $this->board[1][1] && $this->board[1][1] == $this->board[2][1])
            return $this->board[0][1];

        // third column
        if ($this->board[0][2] && $this->board[0][2] == $this->board[1][2] && $this->board[1][2] == $this->board[2][2])
            return $this->board[0][2];

        // 'decreasing' diagonal \
        if ($this->board[0][0] && $this->board[0][0] == $this->board[1][1] && $this->board[1][1] == $this->board[2][2])
            return $this->board[0][0];

        // 'increasing' diagonal /
        if ($this->board[0][2] && $this->board[0][2] == $this->board[1][1] && $this->board[1][1] == $this->board[2][0])
            return $this->board[0][2];

        // if game is tied (9 moves made and no winner)
        if ($this->totalMoves >= 9) return "T";
    }
}

// start session to store game object
session_start();

// if no game object is loaded, initialize a new one
if (!isset($_SESSION['tictactoe'])) $_SESSION['tictactoe'] = new tictactoe();

?>
<html>
<head>
    <title>PHP Tic Tac Toe</title>
    <style>
        /* format overall page body */
        body { margin: auto; padding: 10px; font-size: 12px; font-family: Arial; }
        body > div { margin: auto; padding: 10px; width: 60%; }

        /* format title header */
        h2 { margin: auto; text-align: center; padding-bottom: 20px; }

        /* format win messages */
        .winMsg { font-weight: bold; margin: auto; text-align: center; background-color: #66CC66; border: 1px solid #33AA33; padding: 5px;}
        /* format lose messages */
        .loseMsg { font-weight: bold; margin: auto; text-align: center; color: #FFF; background-color: #990000; border: 1px solid #660000; padding: 5px; }

        /* format the board */
        #board { margin: auto; width: 216px; }
        #board > div { float: left; width: 70px; height: 70px; text-align: center; border: 1px solid #000; }
        #board > div * {margin-top: 25px;}
    </style>
</head>
<body>
<div>
    <form id="boardForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h2>PHP Tic Tac Toe</h2>
        <?php
        // setup the game to start playing,
        // pass POST array as gameState to restore any previous values
        $_SESSION['tictactoe']->setupGame($_POST);
        ?>
    </form>
    <?php
    /*
    if ($game->player == "O") {
        $cell = $game->findEmptyCell();
        echo "<script type=\"text/javascript\">document.getElementById('boardForm').getElementsByName(\"{$cell[0]},{$cell[1]}\")[0].value = \"O\";document.getElementById('boardForm').submit();</script>";
    }
    */
    ?>
</div>
</body>
</html>
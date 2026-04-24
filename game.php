<?php
// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

// Set up the values for the game
$names = array('Rock', 'Paper', 'Scissors');
$human = isset($_POST["human"]) ? $_POST['human']+0 : -1;

$computer = rand(0,2);

// This function takes as its input the computer and human play
// and returns "Tie", "You Lose", "You Win" depending on play
function check($computer, $human) {
    if ( $human == $computer ) {
        return "Tie";
    } else if ( $human == 0 && $computer == 2 ) { // Rock crushes Scissors
        return "You Win";
    } else if ( $human == 1 && $computer == 0 ) { // Paper covers Rock
        return "You Win";
    } else if ( $human == 2 && $computer == 1 ) { // Scissors cuts Paper
        return "You Win";
    } else {
        return "You Lose";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Paewthong Phormma 613b16 e15ce14d - Game</title>
</head>
<body>
    <h1>Rock Paper Scissors</h1>
    <?php
    if ( isset($_REQUEST['name']) ) {
        echo "<p>Welcome: ".htmlentities($_REQUEST['name'])."</p>\n";
    }
    ?>
    <form method="post">
        <select name="human">
            <option value="-1">Select</option>
            <option value="0">Rock</option>
            <option value="1">Paper</option>
            <option value="2">Scissors</option>
            <option value="3">Test</option>
        </select>
        <input type="submit" value="Play">
        <input type="submit" name="logout" value="Logout">
    </form>

    <pre>
<?php
if ( $human == -1 ) {
    print "Please select a strategy and press Play.\n";
} else if ( $human == 3 ) {
    for($c=0;$c<3;$c++) {
        for($h=0;$h<3;$h++) {
            $r = check($c, $h);
            print "Human=$names[$h] Computer=$names[$c] Result=$r\n";
        }
    }
} else {
    print "Your Play=".$names[$human]." Computer Play=".$names[$computer]." Result=".check($computer, $human)."\n";
}
?>
    </pre>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Paewthong Phormma 613b16 e15ce14d</title>
</head>
<body>
    <h1>Welcome to my guessing game</h1>
    <p>
    <?php
      // Requirement: The correct answer must be 64
      $correct_answer = 64; 

      if ( ! isset($_GET['guess']) ) { 
        echo("Missing guess parameter");
      } else if ( strlen($_GET['guess']) < 1 ) {
        echo("Your guess is too short");
      } else if ( ! is_numeric($_GET['guess']) ) {
        echo("Your guess is not a number");
      } else if ( $_GET['guess'] < $correct_answer ) {
        echo("Your guess is too low");
      } else if ( $_GET['guess'] > $correct_answer ) {
        echo("Your guess is too high");
      } else {
        echo("Congratulations - You are right");
      }
    ?>
    </p>
</body>
</html>
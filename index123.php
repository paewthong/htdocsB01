<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MD5 Cracker</title>
</head>
<body>
<h1>MD5 cracker</h1>
<p>This application takes an MD5 hash of a four-digit PIN and attempts to hash all four-digit combinations to determine the original PIN.</p>
<pre>
Debug Output:
<?php
$goodtext = "Not found";

// If there is no parameter, this code is all skipped
if ( isset($_GET['md5']) ) {
    $time_pre = microtime(true);
    $md5 = $_GET['md5'];

    // This is our alphabet for the PIN (0-9)
    $txt = "0123456789";
    $show = 15;

    // Outer loop goes through the first digit
    for($i=0; $i<strlen($txt); $i++ ) 
        $ch1 = $txt[$i];

        // Inner loop goes through the second digit
        for($j=0; $j<strlen($txt); $j++ ) {
            $ch2 = $txt[$j];

            // Third digit
            for($k=0; $k<strlen($txt); $k++ ) {
                $ch3 = $txt[$k];

                // Fourth digit
                for($l=0; $l<strlen($txt); $l++ ) {
                    $ch4 = $txt[$l];

                    // Concatenate the characters to make the PIN as a string
                    $try = $ch1.$ch2.$ch3.$ch4;

                    // Hash it as a string, just like the assignment specifies
                    $check = hash('md5', $try);

                    // Check if the generated hash matches the target MD5
                    if ( $check == $md5 ) {
                        $goodtext = $try;
                    }

                    // Print out the first 15 attempts
                    if ( $show > 0 ) {
                        print "$check $try\n";
                        $show = $show - 1;
                    }
                }
            }
        }
    }
    
    // Compute elapsed time
    $time_post = microtime(true);
    print "Elapsed time: ";
    print $time_post-$time_pre;
    print "\n";
}
?>
</pre>

<p>PIN: <?= htmlentities($goodtext); ?></p>

<form method="GET">
    <input type="text" name="md5" size="40" />
    <input type="submit" value="Crack MD5"/>
</form>

<ul>
    <li><a href="index.php">Reset</a></li>
</ul>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Paewthong Phormma PHP</title>
</head>
<body>
    <h1>[Paewthong Phormma] PHP</h1>
    
    <p>The SHA256 hash of "Paewthong Phormma" is:</p>
    <?php
        // Make sure the name inside the quotes exactly matches the text you are hashing
        print hash('sha256', '[Paewthong Phormma]');
    ?>
    
    <p>ASCII Art:</p>
    <pre>
    *****
    * *
    * *
    *****
    * *
    * *
    * *
</pre>
    
    <p><a href="fail.php">Click here to check the error setting</a></p>
    <p><a href="check.php">Click here to cause a traceback</a></p>
</body>
</html>
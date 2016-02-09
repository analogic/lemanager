<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Not found</title>
    <style><?php include('style.css') ?></style>
</head>
<body>
    <center>
        <style> #main { margin-top: 156px; } input[type="button"] {width: 120px; display: block; } </style>
        <div id="main">
            <h1>Error 500 - Exception</h1>
            <pre><code><?php echo $e->getMessage() ?></code></pre>
            <pre><code><?php echo $e->getTraceAsString() ?></code></pre>
            <input type="button" value="Back" onclick="history.go( -1 );return true;">
        </div>
   </center>
</body>
</html>

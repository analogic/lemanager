<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Not found</title>
    <style><?php include('style.css') ?></style>
</head>
<body>
    <h1>Error 500 - Exception</h1>
    <pre><code><?php echo $e->getMessage() ?></code></pre>
    <pre><code><?php echo $e->getTraceAsString() ?></code></pre>
</body>
</html>
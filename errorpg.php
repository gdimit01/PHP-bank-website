<!DOCTYPE html>
<html>

<head>
    <title>Error</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<head>
    <title>Error</title>
</head>

<body>
    <h1>Error</h1>
    <p><?php echo $_GET['message']; ?></p>
    <form action="index.php" method="POST">
        <input type="hidden" name="home page">
        <input type="submit" value="Home Page">
    </form>
    <!-- <form>
        <input type="hidden" name="exit">
        <input type="submit" value="Exit" onclick="window.close()">
    </form> -->
</body>

</html>
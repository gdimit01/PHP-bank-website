<!DOCTYPE html>
<html>

<head>
    <title>Error</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<!-- /* Checking if the request method is POST and if the message is set. If it is, it will set the message
to the message and redirect to the error page with the message. */ -->

<body>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message'])) {
        $message = $_POST['message'];
        header('Location: errorpg.php?message=' . urlencode($message));
        exit();
    }
}
?>
    <!-- /* This is the error page. It will display the error message if there is one. If there is no error
    message, it will display "An error has occurred." */ -->
    <h1>Error</h1>
    <?php if (isset($_GET['message'])): ?>
    <p><?php echo $_GET['message']; ?></p>
    <?php else: ?>
    <p>An error has occurred.</p>
    <?php endif; ?>
    <img src="images/error.png" alt="error" width="200" height="200">
    <form action="index.php" method="POST">
        <input type="hidden" name="home page">
        <input type="submit" value="Home Page">
    </form>
</body>

</html>
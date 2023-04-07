<!-- adding buy button with class name for styling -->
<div class='button-group'>
    <form action='new.php' method='POST'>
        <input type='hidden' name='book'>
        <input class='button' type='submit' value='Buy Product'>
    </form>
</div>

<!-- adding account button with class name for styling -->
<div class=" button-group">
    <form action="accounts.php" method="POST">
        <input type="hidden" name="cid" value="<?php echo $cid ?>">
        <input type="hidden" name="name" value="<?php echo $name ?>">
        <input class="button" type="submit" value="Accounts">
    </form>
</div>


<!-- adding exit button with class name for styling -->
<form action="session_destroy.php" method="POST" id="exit-form"
    onsubmit="return confirm('Are you sure you want to exit?')">
    <input type="hidden" name="exit">
    <input type="submit" value="Exit">
</form>
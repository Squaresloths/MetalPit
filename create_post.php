<?php
session_start();
//var_dump($_SESSION);
//exit();
if (!isset($_SESSION['user_id'])) {
    // Not logged in? Redirect to login
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Create a New Post</title>
    <link rel="stylesheet" href="style.css">
</head>

<body style="background-color: #1a1a1a; text-align: center; color: #f0f0f0; margin: 0; padding: 20px;">
    <h2>Create a New Forum Post</h2>

    <form action="submit_post.php" method="POST">
        <label for="subject">Title:</label><br>
        <input type="text" name="subject" id="subject" required><br><br>

        <label for="body">Description:</label><br>
        <textarea name="body" id="body" rows="5" required></textarea><br><br>

        <button type="submit" style="height: 25px; width: 60px;">Post</button> <br> <br>
        <a href="home.html">← back home</a>
    </form>
</body>

</html>
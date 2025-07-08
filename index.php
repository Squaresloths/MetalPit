<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USER DASHBOARD | The Metal Pit</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.webp">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1>Welcome to the Dashboard</h1>
        <a href="logout.php" class="btn btn-warning">Logout</a>
    </div>
    <br>
    <div style="text-align: center">
        <button style="background-color: red; width: 75px; height: 40px;"><a href="home.html">Home</a></button>
    </div>

</body>

</html>
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN | The Metal Pit</title>
    <link rel="icon" href="favicon.webp">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <?php

        if (isset($_SESSION["user"])) {
            header("Location: index.php");
            exit;
        }

        require_once "database.php";

        if (isset($_POST["login"])) {
            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);

            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
            var_dump($user); // Debug output

            if ($user) {
                if (password_verify($password, $user["password"])) {
                    $_SESSION["user"] = "yes";
                    $_SESSION["user_id"] = $user["id"];
                    header("Location: index.php");
                    die();
                } else {
                    echo "<div class='alert alert-danger'>Password does not match!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Email does not match!</div>";
            }
        }
        ?>
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="email" placeholder="Enter email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter password" name="password" class="form-control">
            </div>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary">
            </div>
        </form>
        <br>
        <div style="text-align: center">
            <button style="background-color: red; width: 75px; height: 40px;"><a href="home.html">Home</a></button>
        </div>
        <br>
        <div style="background-color: grey; border-style: groove; border-width: 3px; border-color: black; text-align: center;">
            <p>Not registered yet? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>

</html>
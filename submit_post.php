<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "metalpit_forum");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$subject = $conn->real_escape_string($_POST['subject']);
$body = $conn->real_escape_string($_POST['body']);
$user_id = $_SESSION['user_id'];

$sql = "INSERT INTO posts (subject, body, user_id, created_at) VALUES ('$subject', '$body', $user_id, NOW())";

if ($conn->query($sql) === TRUE) {
    header("Location: posts.php");
    exit();
} else {
    echo "Error: " . $conn->error;
}

$conn->close();

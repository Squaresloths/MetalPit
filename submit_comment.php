<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$conn = new mysqli("localhost", "root", "", "metalpit_forum");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$comment = trim($_POST['comment']);
$post_id = (int) $_POST['post_id'];
$user_id = $_SESSION['user_id'];

if (!empty($comment)) {
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $comment);
    $stmt->execute();
}

header("Location: detail.php?id=" . $post_id);
exit;

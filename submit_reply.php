<?php
session_start();
$conn = new mysqli("localhost", "root", "", "metalpit_forum");

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];
$body = $_POST['body'];
$image_path = null;

if (!empty($_FILES['image']['name'])) {
    $target_dir = "uploads/";
    $image_path = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
}

$stmt = $conn->prepare("INSERT INTO replies (post_id, user_id, body, image_path) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $post_id, $user_id, $body, $image_path);
$stmt->execute();

header("Location: detail.php?id=" . $post_id);

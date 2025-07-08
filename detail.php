<?php
session_start();
$conn = new mysqli("localhost", "root", "", "metalpit_forum");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT posts.*, u.full_name AS username
        FROM posts
        JOIN login_register.users u ON posts.user_id = u.id
        WHERE posts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

// Fetch comments
$comments_sql = "SELECT c.comment, c.created_at, c.image_path, u.full_name, u.profile_picture
                 FROM comments c 
                 JOIN login_register.users u ON c.user_id = u.id 
                 WHERE c.post_id = ? 
                 ORDER BY c.created_at DESC";

$comment_stmt = $conn->prepare($comments_sql);
$comment_stmt->bind_param("i", $post_id);
$comment_stmt->execute();
$comment_result = $comment_stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($post['subject']) ?> | The Metal Pit</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.webp">
</head>

<body style="background-color: #1a1a1a; text-align: center; color: #f0f0f0; margin: 0; padding: 20px;">
    <h2><?= htmlspecialchars($post['subject']) ?></h2>
    <p><strong>By:</strong> <?= htmlspecialchars($post['username']) ?></p>
    <p><strong>Posted on:</strong> <?= htmlspecialchars($post['created_at']) ?></p>
    <p><?= nl2br(htmlspecialchars($post['body'])) ?></p>

    <br><a href="posts.php">← Back to posts</a>
    <hr><br>

    <h3>Comments</h3>

    <?php if ($comment_result->num_rows > 0): ?>
        <?php while ($c = $comment_result->fetch_assoc()): ?>
            <div style="margin: 10px auto; width: 60%; background-color: #ff0000; padding: 10px; border-radius: 5px; color:#1a1a1a">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <?php if (!empty($c['profile_picture']) && file_exists('uploads/' . $c['profile_picture'])): ?>
                        <img src="uploads/<?= htmlspecialchars($c['profile_picture']) ?>" alt="Profile Picture" style="width:40px; height:40px; border-radius:50%; object-fit: cover;">
                    <?php else: ?>
                        <img src="uploads/default_profile.jpg" alt="Default Profile Picture" style="width:40px; height:40px; border-radius:50%; object-fit: cover;">
                    <?php endif; ?>
                    <div>
                        <strong><?= htmlspecialchars($c['full_name']) ?></strong><br>
                        <small><em><?= htmlspecialchars($c['created_at']) ?></em></small>
                    </div>
                </div>
                <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>

                <!-- Show comment image if exists -->
                <?php if (!empty($c['image_path']) && file_exists('uploads/' . $c['image_path'])): ?>
                    <div>
                        <img src="uploads/<?= htmlspecialchars($c['image_path']) ?>" alt="Comment Image" style="max-width:100%; border-radius:8px; margin-top:10px;">
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No comments yet. Be the first!</p>
    <?php endif; ?>

    <br>

    <?php if (isset($_SESSION['user_id'])): ?>
        <h3>Leave a Comment</h3>
        <form action="submit_comment.php" method="post" enctype="multipart/form-data" style="margin-top: 10px;">
            <textarea name="comment" rows="4" cols="60" placeholder="Write your comment..." required></textarea><br>
            <input type="hidden" name="post_id" value="<?= $post_id ?>">
            <button type="submit" style="width: 250px; height: 36px; border-style: groove; border-width: 3px; border-color: black; border-radius: 3px; background-color:#ff0000; color:#1a1a1a">Submit Reply</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Log in</a> to leave a comment.</p>
    <?php endif; ?>
</body>

</html>
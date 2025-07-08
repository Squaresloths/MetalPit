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

if (!$post) {
    echo "Post not found.";
    exit;
}

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

// Fetch average rating
$rating_sql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_ratings FROM ratings WHERE post_id = ?";
$rating_stmt = $conn->prepare($rating_sql);
$rating_stmt->bind_param("i", $post_id);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result()->fetch_assoc();
$avg_rating = round($rating_result['avg_rating'], 1);
$total_ratings = $rating_result['total_ratings'];
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($post['subject']) ?> | The Metal Pit</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.webp">
    <script src="https://kit.fontawesome.com/ccd3e9a715.js" crossorigin="anonymous"></script>
</head>

<body style="background-color: #1a1a1a; text-align: center; color: #f0f0f0; margin: 0; padding: 20px;">
    <h2><?= htmlspecialchars($post['subject']) ?>
        <?php if ($post['closed'] == 1): ?>
            <i class="fa fa-lock" title="Closed Post" style="color:#ff5555;"></i>
        <?php endif; ?>
    </h2>
    <p><strong>By:</strong> <?= htmlspecialchars($post['username']) ?></p>
    <p><strong>Posted on:</strong> <?= htmlspecialchars($post['created_at']) ?></p>
    <p><?= nl2br(htmlspecialchars($post['body'])) ?></p>

    <br><a href="posts.php">← Back to posts</a>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
        <form action="toggle_post_status.php" method="post" style="margin-top: 20px;">
            <input type="hidden" name="post_id" value="<?= $post_id ?>">
            <input type="hidden" name="closed" value="<?= $post['closed'] == 1 ? 0 : 1 ?>">
            <button type="submit" style="padding: 8px 12px; background-color: <?= $post['closed'] == 1 ? '#4CAF50' : '#f44336' ?>; color: white; border: none; border-radius: 4px; cursor: pointer;">
                <?= $post['closed'] == 1 ? 'Reopen Post' : 'Close Post' ?>
            </button>
        </form>
    <?php endif; ?>

    <hr><br>

    <h3>Average Rating: <?= $avg_rating ?: 'No ratings yet' ?> / 5 (<?= $total_ratings ?> vote<?= $total_ratings != 1 ? 's' : '' ?>)</h3>

    <?php if ($post['closed'] == 1): ?>
        <p style="color: #ff5555; font-weight: bold;">This post is closed. No more comments or ratings allowed.</p>
    <?php else: ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Rating Form -->
            <form action="submit_rating.php" method="post" style="margin: 20px auto;">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <label for="rating">Rate this post:</label>
                <select name="rating" id="rating" required>
                    <option value="">--Select--</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" style="padding: 5px 10px;">Submit Rating</button>
            </form>

            <!-- Comment Form -->
            <form action="submit_comment.php" method="post" enctype="multipart/form-data" style="margin: 20px auto; max-width: 500px;">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <label for="comment">Add a comment:</label><br>
                <textarea name="comment" id="comment" rows="5" cols="50" required></textarea><br><br>
                <button type="submit" style="padding: 5px 10px;">Post Comment</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Login</a> to rate or comment on this post.</p>
        <?php endif; ?>
    <?php endif; ?>

    <hr><br>

    <h3>Comments (<?= $comment_result->num_rows ?>):</h3>

    <?php if ($comment_result->num_rows > 0): ?>
        <div style="max-width: 800px; margin: 0 auto; text-align: left; background-color: #ff0000; border-style: groove; border-color: #1a1a1a; border-width: 4px; border-radius: 3px;">
            <?php while ($comment = $comment_result->fetch_assoc()): ?>
                <div style="border-bottom: 4px groove #444; padding: 10px 0;">
                    <div style="display: flex; align-items: center; margin-left: 4px;">
                        <img src="uploads/default_image.jpg" alt="Default Profile" style="width:40px; height:40px; border-radius:50%; margin-right:10px;">
                        <strong><?= htmlspecialchars($comment['full_name']) ?></strong>
                        <span style="margin-left: auto; font-size: 0.9em; color: #aaa;"><?= htmlspecialchars($comment['created_at']) ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                    <?php if (!empty($comment['image_path'])): ?>
                        <img src="<?= htmlspecialchars($comment['image_path']) ?>" alt="Comment Image" style="max-width: 100%; max-height: 300px;">
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No comments yet.</p>
    <?php endif; ?>

</body>

</html>

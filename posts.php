<?php
session_start();
$conn = new mysqli("localhost", "root", "", "metalpit_forum");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$posts_per_page = 8;
$offset = ($page - 1) * $posts_per_page;

// Count total posts
$count_sql = "SELECT COUNT(*) as total_posts FROM posts";
$count_result = $conn->query($count_sql);
$total_posts = $count_result->fetch_assoc()['total_posts'];
$total_pages = ceil($total_posts / $posts_per_page);

// Fetch posts for current page
$sql = "SELECT posts.*, u.full_name AS username, 
               ROUND(AVG(r.rating), 1) AS avg_rating
        FROM posts 
        JOIN login_register.users u ON posts.user_id = u.id 
        LEFT JOIN ratings r ON posts.id = r.post_id
        GROUP BY posts.id
        ORDER BY posts.created_at DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $posts_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>POSTS | The Metal Pit</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="favicon.webp" />
    <script src="https://kit.fontawesome.com/ccd3e9a715.js" crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <h2><strong><u>The Metal Pit</u></strong></h2>
    </header>

    <nav class="navbar">
        <ul>
            <li><a href="home.html"><strong>Home</strong></a></li>
            <li><a href="AboutUs.html"><strong>About</strong></a></li>
            <li><a href="Contact.html"><strong>Contact</strong></a></li>
            <li><a href="http://metaltabs.com" target="_top"><strong>MetalTabs.com</strong></a></li>
            <li><a href="posts.php"><strong>Posts</strong></a></li>
        </ul>
    </nav>

    <main>
        <aside>
            <h2><b>Registration & Login:</b></h2>
            <p>You can register <a href="register.php">here</a><br />
                You can login <a href="login.php">here</a></p>
            <h2>Search:</h2>
            <form action="search_results.php" method="GET">
                <select name="filter">
                    <option value="everything">Everything</option>
                    <option value="subject">Titles</option>
                    <option value="body">Description</option>
                </select>
                <input type="text" name="q" placeholder="Search..." required />
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </aside>

        <section>
            <div class="containers">
                <div class="navigate">
                    <span><a href="#">My Forum - Forums</a> >> <a href="#">Latest Topics</a></span>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div style="margin: 10px 0;">
                        <a href="http://localhost/Web/create_post.php" class="button">+ Create New Post</a>
                    </div>
                <?php endif; ?>

                <div class="posts-table">
                    <div class="table-head">
                        <div class="status">Status</div>
                        <div class="subjects">Subjects</div>
                    </div>

                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()):
                            $avg = $row['avg_rating'];
                            if ($row['closed'] == 1) {
                                $icon = 'fa-lock';
                                $title = "Closed Topic";
                            } else {
                                if ($avg === null) {
                                    $icon = 'fa-frown-o'; // No rating yet
                                    $title = "No ratings yet";
                                } elseif ($avg < 2.5) {
                                    $icon = 'fa-frown-o'; // Low rating
                                    $title = "Low rating: $avg stars";
                                } elseif ($avg < 4) {
                                    $icon = 'fa-fire'; // Medium rating
                                    $title = "Medium rating: $avg stars";
                                } else {
                                    $icon = 'fa-rocket'; // High rating
                                    $title = "High rating: $avg stars";
                                }
                            }
                        ?>
                            <div class="table-row">
                                <div class="status" title="<?= htmlspecialchars($title) ?>"><i class="fa <?= $icon ?>"></i></div>
                                <div class="subjects">
                                    <a href="detail.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['subject']) ?></a><br />
                                    <span>Started by <a href="#"><?= htmlspecialchars($row['username']) ?></a>
                                        <?php if ($avg !== null && $row['closed'] == 0): ?>
                                            | Rated: <?= $avg ?> / 5
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="table-row">
                            <div class="subjects" colspan="4">No posts found.</div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="pagination" style="margin-top: 20px;">
                    Pages:
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <strong><?= $i ?></strong>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="note" style="margin-top: 40px;">
                <span><i class="fa fa-frown-o"></i>&nbsp; 0 Engagement Topic</span><br />
                <span><i class="fa fa-fire"></i>&nbsp; Interesting Topic</span><br />
                <span><i class="fa fa-rocket"></i>&nbsp; Popular Topic</span><br />
                <span><i class="fa fa-lock"></i>&nbsp; Closed Topic</span>
            </div>
        </section>
    </main>

    <footer>
        <h2>More info:</h2>
        <p>credits to <a href="https://pixel-soup.tumblr.com/">Pixel-Soup</a> for the favicon & <a
                href="https://ro.pinterest.com/">Pinterest</a> for the backgrounds</p>
        <p>&copy; All rights reserved</p>
    </footer>

    <script src="main.js"></script>
</body>

</html>

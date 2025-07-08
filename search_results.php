<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "metalpit_forum");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search query and filter
$filter = $_GET['filter'] ?? 'everything';
$search = $conn->real_escape_string($_GET['q'] ?? '');

$sql = "";

if ($filter === "subject") {
    $sql = "SELECT * FROM posts WHERE subject LIKE '%$search%'";
} elseif ($filter === "body") {
    $sql = "SELECT * FROM posts WHERE body LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM posts WHERE subject LIKE '%$search%' OR body LIKE '%$search%'";
}


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Search Results</title>
    <link rel="icon" href="favicon.webp">
    <link rel="stylesheet" href="search_style.css">
</head>

<body>
    <section>
        <h2>Search Results for: "<?php echo htmlspecialchars($search); ?>"</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <a href="detail.php?id=<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['subject']); ?>
                        </a><br>
                        <small><?php echo htmlspecialchars(substr($row['body'], 0, 100)); ?>...</small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No results found.</p>
        <?php endif; ?>

        <a href="home.html">← Back to Home</a>
    </section>
</body>

</html>

<?php
$conn->close();
?>
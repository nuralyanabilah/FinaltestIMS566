<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_comment"])) {
    $application_id = $_POST["application_id"];
    $author = $_POST["author"];
    $content = $_POST["content"];

    $stmt = $conn->prepare("INSERT INTO comments (application_id, author, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $application_id, $author, $content);
    $stmt->execute();
    $stmt->close();
}


if (isset($_GET["delete_id"])) {
    $id = $_GET["delete_id"];
    $conn->query("DELETE FROM comments WHERE id = $id");
    header("Location: comments.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Manage Comments</h3>
    <a href="index.php" class="btn btn-secondary btn-sm mb-3">Back</a>

    <!-- Add Comment Form -->
    <div class="mb-4">
        <form method="POST">
            <div class="mb-2">
                <label class="form-label">Application</label>
                <select name="application_id" class="form-select" required>
                    <option value="">Select Application</option>
                    <?php
                    $apps = $conn->query("SELECT id, name FROM applications");
                    while ($app = $apps->fetch_assoc()) {
                        echo "<option value='{$app['id']}'>{$app['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">Your Name</label>
                <input type="text" name="author" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Comment</label>
                <textarea name="content" class="form-control" required></textarea>
            </div>
            <button type="submit" name="add_comment" class="btn btn-primary">Add Comment</button>
        </form>
    </div>

    <!-- Existing Comments Table -->
    <h5>All Comments</h5>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>App Name</th>
                <th>Author</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("
                SELECT c.*, a.name AS app_name 
                FROM comments c 
                JOIN applications a ON c.application_id = a.id 
                ORDER BY c.created_at DESC
            ");
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['app_name']) ?></td>
                <td><?= htmlspecialchars($row['author']) ?></td>
                <td><?= htmlspecialchars($row['content']) ?></td>
                <td><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="comments.php?delete_id=<?= $row['id'] ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Delete this comment?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted">No comments found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

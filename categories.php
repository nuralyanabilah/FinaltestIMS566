<?php
include 'db.php';

$error = '';
$success = '';

// Add new category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_category"])) {
    $name = trim($_POST["name"]);
    
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $success = "Category added.";
        } else {
            $error = "Failed to add category.";
        }
        $stmt->close();
    } else {
        $error = "Category name required.";
    }
}

// Delete category
if (isset($_GET["delete_id"])) {
    $id = (int)$_GET["delete_id"];
    $check = $conn->query("SELECT COUNT(*) AS count FROM applications WHERE category_id = $id");
    $row = $check->fetch_assoc();

    if ($row['count'] == 0) {
        if ($conn->query("DELETE FROM categories WHERE id = $id")) {
            $success = "Category deleted.";
        } else {
            $error = "Failed to delete.";
        }
    } else {
        $error = "Cannot delete. Category is in use.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Manage Categories</h3>
    <a href="index.php" class="btn btn-secondary btn-sm mb-3">Back</a>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-3 d-flex gap-2">
        <input type="text" name="name" class="form-control" placeholder="New category name" required>
        <button type="submit" name="add_category" class="btn btn-primary">Add</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td>
                    <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this category?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="3" class="text-center">No categories found</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

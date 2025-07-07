<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Mobile App Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container mt-4">
    <h3 class="mb-4">Mobile App Reviews</h3>
    <a href="create.php" class="btn btn-primary mb-3">+ Add New Review</a>

    <!-- Filter Form -->
    <form method="GET" class="row g-2 mb-4 bg-light p-3 rounded">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search..." 
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php
                $categories = $conn->query("SELECT * FROM categories");
                while ($cat = $categories->fetch_assoc()) {
                    $selected = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '';
                    echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-secondary w-100" type="submit">Filter</button>
        </div>
    </form>

    <!-- Reviews Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Desc</th>
                    <th>Rating</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $search = $_GET['search'] ?? '';
            $category = $_GET['category'] ?? '';
            $status = $_GET['status'] ?? '';

            $sql = "SELECT a.*, c.name AS category_name FROM applications a 
                    LEFT JOIN categories c ON a.category_id = c.id 
                    WHERE (a.name LIKE '%$search%' OR a.description LIKE '%$search%')";

            if (!empty($category)) {
                $sql .= " AND a.category_id = $category";
            }
            if ($status !== '') {
                $sql .= " AND a.is_active = $status";
            }

            $result = $conn->query($sql);
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td>
                        <?php if ($row['image_path']): ?>
                            <img src="<?= $row['image_path'] ?>" width="60" class="img-thumbnail">
                        <?php else: ?>
                            <span class="text-muted">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars(substr($row['description'], 0, 50)) ?>...</td>
                    <td>
                        <?= $row['rating'] ?>
                        <small class="text-muted">/5</small>
                    </td>
                    <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                    <td>
                        <span class="badge bg-<?= $row['is_active'] ? 'success' : 'danger' ?>">
                            <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">View</a>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this review?')">Delete</a>
                            <a href="export_pdf.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">PDF</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No reviews found</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Navigation buttons -->
    <div class="mt-3 d-flex justify-content-between">
        <a href="categories.php" class="btn btn-outline-secondary">Manage Categories</a>
        <a href="comments.php" class="btn btn-outline-primary">View Comments</a>
    </div>
</div>
</body>
</html>

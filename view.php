<?php 
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$review = $conn->query("
    SELECT a.*, c.name as category_name 
    FROM applications a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.id = $id
")->fetch_assoc();

if (!$review) {
    header("Location: index.php");
    exit;
}

$comments = $conn->query("
    SELECT * FROM comments 
    WHERE application_id = $id 
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($review['name']) ?> - Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3">Back to Reviews</a>
    
    <div class="card mb-4">
        <div class="row g-0">
            <?php if ($review['image_path']): ?>
            <div class="col-md-4">
                <img src="<?= $review['image_path'] ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($review['name']) ?>">
            </div>
            <?php endif; ?>
            <div class="col-md-<?= $review['image_path'] ? '8' : '12' ?>">
                <div class="card-body">
                    <h2 class="card-title"><?= htmlspecialchars($review['name']) ?></h2>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-3">
                            <?php 
                            $fullStars = floor($review['rating']);
                            $halfStar = ($review['rating'] - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                            
                            for ($i = 0; $i < $fullStars; $i++) {
                                echo '<i class="fas fa-star text-warning"></i>';
                            }
                            if ($halfStar) {
                                echo '<i class="fas fa-star-half-alt text-warning"></i>';
                            }
                            for ($i = 0; $i < $emptyStars; $i++) {
                                echo '<i class="far fa-star text-warning"></i>';
                            }
                            ?>
                            <span class="ms-2"><?= number_format($review['rating'], 1) ?>/5.0</span>
                        </div>
                        <span class="badge bg-<?= $review['is_active'] ? 'success' : 'danger' ?>">
                            <?= $review['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                    <p class="text-muted mb-2">
                        <strong>Category:</strong> <?= htmlspecialchars($review['category_name']) ?>
                    </p>
                    <p class="text-muted">
                        <strong>Last Updated:</strong> <?= date('F j, Y g:i a', strtotime($review['updated_at'])) ?>
                    </p>
                    <div class="mt-3">
                        <a href="edit.php?id=<?= $review['id'] ?>" class="btn btn-warning">Edit</a>
                        <a href="delete.php?id=<?= $review['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this review?')">Delete</a>
                        <a href="export.php?id=<?= $review['id'] ?>" class="btn btn-info">Export</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Description</h4>
            <p class="card-text"><?= nl2br(htmlspecialchars($review['description'])) ?></p>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Comments</h4>
            
            <?php if ($comments->num_rows > 0): ?>
                <?php while ($comment = $comments->fetch_assoc()): ?>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <strong><?= htmlspecialchars($comment['author']) ?></strong>
                        <small class="text-muted"><?= date('M j, Y g:i a', strtotime($comment['created_at'])) ?></small>
                    </div>
                    <p class="mb-0"><?= htmlspecialchars($comment['content']) ?></p>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">No comments yet.</p>
            <?php endif; ?>
            
            <div class="mt-4">
                <h5>Add Comment</h5>
                <form method="POST" action="comments.php">
                    <input type="hidden" name="application_id" value="<?= $review['id'] ?>">
                    <div class="mb-3">
                        <input type="text" name="author" class="form-control" placeholder="Your Name" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="content" class="form-control" placeholder="Your Comment" required></textarea>
                    </div>
                    <button type="submit" name="add_comment" class="btn btn-primary">Submit Comment</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
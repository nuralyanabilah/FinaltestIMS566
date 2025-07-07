<?php
include 'db.php';

if (!isset($_GET['id'])) {
    echo "No review ID provided.";
    exit;
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM applications WHERE id=$id");
$review = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $rating = $_POST["rating"];
    $category_id = $_POST["category_id"];
    $is_active = isset($_POST["is_active"]) ? 1 : 0;
    
    // Handle image upload
    $image_path = $review['image_path'];
    if (isset($_POST['remove_image']) && $image_path) {
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        $image_path = '';
    }
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Delete old image if exists
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }
        
        $upload_dir = 'media/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir);
        }
        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        }
    }
    
    $sql = "UPDATE applications SET 
            name='$name', 
            description='$description', 
            rating=$rating, 
            category_id=$category_id, 
            image_path='$image_path', 
            is_active=$is_active 
            WHERE id=$id";
            
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Edit Mobile App Review</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Back to List</a>

    <form method="POST" enctype="multipart/form-data" class="border p-4 rounded">
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">App Name</label>
            <div class="col-sm-10">
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($review['name']) ?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
                <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($review['description']) ?></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Rating</label>
            <div class="col-sm-10">
                <input type="number" name="rating" class="form-control" value="<?= $review['rating'] ?>" min="0" max="5" step="0.1" required>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Category</label>
            <div class="col-sm-10">
                <select name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php
                    $categories = $conn->query("SELECT * FROM categories");
                    while ($cat = $categories->fetch_assoc()) {
                        $selected = ($cat['id'] == $review['category_id']) ? 'selected' : '';
                        echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Status</label>
            <div class="col-sm-10">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?= $review['is_active'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Image</label>
            <div class="col-sm-10">
                <?php if ($review['image_path']): ?>
                    <img src="<?= $review['image_path'] ?>" class="img-thumbnail mb-2" style="max-height: 200px;">
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remove_image" class="form-check-input" id="remove_image">
                        <label class="form-check-label" for="remove_image">Remove current image</label>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
        </div>

        <div class="row">
            <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn btn-primary">Update Review</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>
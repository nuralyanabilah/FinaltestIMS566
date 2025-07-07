<?php
include 'db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $rating = $_POST["rating"]; // This will now come from the star rating
    $category_id = $_POST["category_id"];
    $is_active = isset($_POST["is_active"]) ? 1 : 0;
    $image_path = '';

    if (empty($name) || empty($description) || empty($rating) || empty($category_id)) {
        $error = "Please fill in all fields.";
    } else {
        if ($_FILES['image']['name']) {
            $target_dir = "media/";
            if (!is_dir($target_dir)) mkdir($target_dir);
            $file_name = uniqid() . "_" . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            } else {
                $error = "Image upload failed.";
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO applications (name, description, rating, category_id, image_path, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdiss", $name, $description, $rating, $category_id, $image_path, $is_active);
            if ($stmt->execute()) {
                $success = "App review added.";
                header("Location: index.php");
                exit;
            } else {
                $error = "Database error.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        .rating input {
            display: none;
        }
        .rating label {
            cursor: pointer;
            font-size: 1.5rem;
            color: #ddd;
            transition: color 0.2s;
        }
        .rating input:checked ~ label,
        .rating input:hover ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            color: #ffc107;
        }
        .rating input:checked + label {
            color: #ffc107;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h3>Add Mobile App Review</h3>
    <a href="index.php" class="btn btn-secondary btn-sm mb-3">Back</a>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="border p-4 rounded">
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">App Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="name" required>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
                <textarea class="form-control" name="description" rows="5" required></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Rating</label>
            <div class="col-sm-10">
                <div class="rating">
                    <input type="radio" id="star5" name="rating" value="5" required>
                    <label for="star5"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1"><i class="fas fa-star"></i></label>
                </div>
                <small class="text-muted">Click stars to rate (1-5)</small>
                <input type="hidden" name="rating_value" id="rating_value">
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Category</label>
            <div class="col-sm-10">
                <select name="category_id" class="form-select" required>
                    <option value="">Select</option>
                    <?php
                    $categories = $conn->query("SELECT * FROM categories");
                    while ($cat = $categories->fetch_assoc()) {
                        echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Image</label>
            <div class="col-sm-10">
                <input type="file" class="form-control" name="image">
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Status</label>
            <div class="col-sm-10">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="is_active" checked>
                    <label class="form-check-label">Active</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-10 offset-sm-2">
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('.rating input');
    const ratingValue = document.getElementById('rating_value');
    
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            ratingValue.value = this.value;
        });
    });
});
</script>
</body>
</html>

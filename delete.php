<?php
include 'db.php';

$id = $_GET['id'];

// First get the image path to delete the file
$result = $conn->query("SELECT image_path FROM applications WHERE id=$id");
$review = $result->fetch_assoc();

if ($review['image_path'] && file_exists($review['image_path'])) {
    unlink($review['image_path']);
}

// Delete the review
$sql = "DELETE FROM applications WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    // Also delete associated comments
    $conn->query("DELETE FROM comments WHERE application_id=$id");
    header("Location: index.php");
} else {
    echo "Error: " . $conn->error;
}
?>
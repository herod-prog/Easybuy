<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$item_id = intval($_GET['id']);

// Get item
$sql = "SELECT * FROM items WHERE id='$item_id' AND user_id='$user_id'";
$result = mysqli_query($conn, $sql);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    redirect('my_listings.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $category = sanitize($_POST['category']);
    $location = sanitize($_POST['location']);
    $image = $item['image'];

    // Handle new image
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $upload_dir = 'uploads/';
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($file_extension, $allowed)) {
            $new_image = uniqid() . '.' . $file_extension;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_image)) {
                // Delete old image
                if ($item['image'] && file_exists($upload_dir . $item['image'])) {
                    unlink($upload_dir . $item['image']);
                }
                $image = $new_image;
            }
        }
    }

    $sql = "UPDATE items SET 
            title='$title', 
            description='$description', 
            price='$price', 
            category='$category', 
            location='$location',
            image=" . ($image ? "'$image'" : "NULL") . "
            WHERE id='$item_id' AND user_id='$user_id'";

    if (mysqli_query($conn, $sql)) {
        $success = "Item updated successfully!";
        // Refresh item data
        $result = mysqli_query($conn, "SELECT * FROM items WHERE id='$item_id'");
        $item = mysqli_fetch_assoc($result);
    } else {
        $error = "Failed to update item!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyBuy - Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f5f6fa; }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 0;
        }
        .navbar-brand { font-size: 24px; font-weight: 700; color: white !important; }
        .navbar-brand span { color: #ffd700; }
        .edit-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            padding: 30px;
            margin: 30px auto;
            max-width: 700px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #eee;
            padding: 10px 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: none;
        }
        .btn-update {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
        }
        .current-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">🛍️ Easy<span>Buy</span></a>
        <a href="my_listings.php" class="btn btn-outline-light ms-auto">
            <i class="fas fa-arrow-left me-2"></i>Back to My Listings
        </a>
    </div>
</nav>

<div class="container">
    <div class="edit-card">
        <h3 class="fw-bold mb-4" style="color: #667eea;">
            <i class="fas fa-edit me-2"></i>Edit Item
        </h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Item Title</label>
                    <input type="text" name="title" class="form-control"
                           value="<?= $item['title'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Price (KES)</label>
                    <input type="number" name="price" class="form-control"
                           value="<?= $item['price'] ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Category</label>
                    <select name="category" class="form-select" required>
                        <?php
                        $cats = ['Electronics', 'Furniture', 'Clothing', 'Vehicles', 
                                 'Books', 'Home Appliances', 'Sports', 'Other'];
                        foreach ($cats as $cat):
                        ?>
                            <option <?= $item['category'] == $cat ? 'selected' : '' ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Location</label>
                    <input type="text" name="location" class="form-control"
                           value="<?= $item['location'] ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="4" required><?= $item['description'] ?></textarea>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Item Photo</label>
                <?php if ($item['image']): ?>
                    <img src="uploads/<?= $item['image'] ?>" class="current-image d-block">
                    <small class="text-muted">Upload new image to replace current one</small>
                <?php endif; ?>
                <input type="file" name="image" class="form-control mt-2" accept="image/*">
            </div>
            <button type="submit" class="btn-update">
                <i class="fas fa-save me-2"></i>Update Item
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
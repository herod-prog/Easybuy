<?php
require_once 'config.php';

// Protect page
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get user's items
$sql = "SELECT * FROM items WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Handle delete
if (isset($_GET['delete'])) {
    $item_id = intval($_GET['delete']);
    
    // Get image first to delete file
    $img_query = mysqli_query($conn, "SELECT image FROM items WHERE id='$item_id' AND user_id='$user_id'");
    $img_data = mysqli_fetch_assoc($img_query);
    
    if ($img_data && $img_data['image']) {
        $img_path = 'uploads/' . $img_data['image'];
        if (file_exists($img_path)) {
            unlink($img_path);
        }
    }
    
    mysqli_query($conn, "DELETE FROM items WHERE id='$item_id' AND user_id='$user_id'");
    redirect('my_listings.php');
}

// Handle mark as sold
if (isset($_GET['sold'])) {
    $item_id = intval($_GET['sold']);
    mysqli_query($conn, "UPDATE items SET status='sold' WHERE id='$item_id' AND user_id='$user_id'");
    redirect('my_listings.php');
}

// Handle mark as available
if (isset($_GET['available'])) {
    $item_id = intval($_GET['available']);
    mysqli_query($conn, "UPDATE items SET status='available' WHERE id='$item_id' AND user_id='$user_id'");
    redirect('my_listings.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyBuy - My Listings</title>
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
        .navbar-brand {
            font-size: 24px;
            font-weight: 700;
            color: white !important;
        }
        .navbar-brand span { color: #ffd700; }
        .nav-link { color: rgba(255,255,255,0.9) !important; font-weight: 500; }
        .btn-post {
            background: #ffd700;
            color: #333;
            font-weight: 600;
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        .item-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .item-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .item-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .item-image-placeholder {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #667eea20, #764ba220);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
        }
        .item-body { padding: 15px; }
        .item-title {
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }
        .item-price {
            font-weight: 700;
            font-size: 18px;
            color: #667eea;
        }
        .badge-available {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .badge-sold {
            background: #dc3545;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .action-btns .btn {
            border-radius: 8px;
            font-size: 13px;
            padding: 6px 12px;
        }
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }
        .empty-state i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .stats-number {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
        }
        .stats-label {
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">🛍️ Easy<span>Buy</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="favorites.php"><i class="fas fa-heart me-1"></i>Favorites</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active fw-bold text-white" href="my_listings.php">
                        <i class="fas fa-box me-1"></i>My Listings
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-post" data-bs-toggle="modal" data-bs-target="#postModal">
                    <i class="fas fa-plus me-1"></i>Post Item
                </button>
                <div class="dropdown">
                    <button class="btn btn-link text-white dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i><?= $user_name ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="my_listings.php">
                            <i class="fas fa-box me-2"></i>My Listings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="php/logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2 class="fw-bold mb-0">
            <i class="fas fa-box me-2"></i>My Listings
        </h2>
        <p class="mb-0 opacity-75">Manage your posted items</p>
    </div>
</div>

<div class="container">
    <!-- Stats Row -->
    <?php
        $total = mysqli_num_rows($result);
        $available_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM items WHERE user_id='$user_id' AND status='available'");
        $available_count = mysqli_fetch_assoc($available_q)['count'];
        $sold_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM items WHERE user_id='$user_id' AND status='sold'");
        $sold_count = mysqli_fetch_assoc($sold_q)['count'];
    ?>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-number"><?= $total ?></div>
                <div class="stats-label">Total Listings</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-number text-success"><?= $available_count ?></div>
                <div class="stats-label">Available</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-number text-danger"><?= $sold_count ?></div>
                <div class="stats-label">Sold</div>
            </div>
        </div>
    </div>

    <!-- Items Grid -->
    <?php if ($total > 0): ?>
        <div class="row">
            <?php
                // Reset result pointer
                mysqli_data_seek($result, 0);
                while ($item = mysqli_fetch_assoc($result)):
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="item-card">
                        <?php if ($item['image']): ?>
                            <img src="uploads/<?= $item['image'] ?>" 
                                 class="item-image" alt="<?= $item['title'] ?>">
                        <?php else: ?>
                            <div class="item-image-placeholder">
                                <?php
                                    $icons = [
                                        'Electronics' => '📱',
                                        'Furniture' => '🛋️',
                                        'Clothing' => '👕',
                                        'Vehicles' => '🚗',
                                        'Books' => '📚',
                                        'Home Appliances' => '🏠',
                                        'Sports' => '⚽',
                                        'Other' => '📦'
                                    ];
                                    echo $icons[$item['category']] ?? '📦';
                                ?>
                            </div>
                        <?php endif; ?>
                        <div class="item-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="<?= $item['status'] == 'available' ? 'badge-available' : 'badge-sold' ?>">
                                    <?= ucfirst($item['status']) ?>
                                </span>
                                <small class="text-muted">
                                    <?= date('d M Y', strtotime($item['created_at'])) ?>
                                </small>
                            </div>
                            <div class="item-title mb-1"><?= $item['title'] ?></div>
                            <div class="item-price mb-1">
                                KES <?= number_format($item['price'], 2) ?>
                            </div>
                            <div class="text-muted mb-2" style="font-size: 12px;">
                                <i class="fas fa-map-marker-alt me-1"></i><?= $item['location'] ?>
                            </div>
                            <div class="action-btns d-flex gap-2 mt-2">
                                <?php if ($item['status'] == 'available'): ?>
                                    <a href="edit_item.php?id=<?= $item['id'] ?>" 
                                       class="btn btn-primary btn-sm flex-fill">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="my_listings.php?sold=<?= $item['id'] ?>"
                                       class="btn btn-warning btn-sm flex-fill"
                                       onclick="return confirm('Mark as sold?')">
                                        <i class="fas fa-check"></i> Sold
                                    </a>
                                <?php else: ?>
                                    <a href="my_listings.php?available=<?= $item['id'] ?>"
                                       class="btn btn-success btn-sm flex-fill">
                                        <i class="fas fa-redo"></i> Relist
                                    </a>
                                <?php endif; ?>
                                <a href="my_listings.php?delete=<?= $item['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this item?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-box-open d-block"></i>
            <h4>No listings yet</h4>
            <p>Start selling by posting your first item!</p>
            <button class="btn btn-post mt-3" 
                    data-bs-toggle="modal" data-bs-target="#postModal">
                <i class="fas fa-plus me-2"></i>Post First Item
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Post Item Modal -->
<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" 
                 style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Post New Item
                </h5>
                <button type="button" class="btn-close btn-close-white" 
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="php/post_item.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Item Title</label>
                            <input type="text" name="title" class="form-control"
                                   placeholder="e.g. iPhone 12 Pro" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Price (KES)</label>
                            <input type="number" name="price" class="form-control"
                                   placeholder="e.g. 15000" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select Category</option>
                                <option>Electronics</option>
                                <option>Furniture</option>
                                <option>Clothing</option>
                                <option>Vehicles</option>
                                <option>Books</option>
                                <option>Home Appliances</option>
                                <option>Sports</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Location</label>
                            <input type="text" name="location" class="form-control"
                                   placeholder="e.g. Nairobi, CBD" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Describe your item..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Photo (Optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-bold py-3"
                            style="background: linear-gradient(135deg, #667eea, #764ba2); 
                                   border-radius: 10px; border: none;">
                        <i class="fas fa-upload me-2"></i>Post Item
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
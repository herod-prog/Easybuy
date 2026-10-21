<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Handle remove favorite
if (isset($_GET['remove'])) {
    $item_id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM favorites WHERE user_id='$user_id' AND item_id='$item_id'");
    redirect('favorites.php');
}

// Get favorites
$sql = "SELECT items.*, users.fullname, users.phone 
        FROM favorites 
        JOIN items ON favorites.item_id = items.id 
        JOIN users ON items.user_id = users.id 
        WHERE favorites.user_id = '$user_id'
        ORDER BY favorites.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyBuy - Favorites</title>
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
        .item-title { font-weight: 600; font-size: 16px; color: #333; }
        .item-price { font-weight: 700; font-size: 18px; color: #667eea; }
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }
        .empty-state i { font-size: 80px; color: #ddd; margin-bottom: 20px; }
        .btn-whatsapp { background: #25D366; color: white; border: none; border-radius: 8px; }
        .btn-call { background: #667eea; color: white; border: none; border-radius: 8px; }
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
                    <a class="nav-link active fw-bold text-white" href="favorites.php">
                        <i class="fas fa-heart me-1"></i>Favorites
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="my_listings.php"><i class="fas fa-box me-1"></i>My Listings</a>
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
            <i class="fas fa-heart me-2"></i>My Favorites
        </h2>
        <p class="mb-0 opacity-75">Items you've saved</p>
    </div>
</div>

<div class="container">
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="row">
            <?php while ($item = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="item-card">
                        <?php if ($item['image']): ?>
                            <img src="uploads/<?= $item['image'] ?>" 
                                 class="item-image" alt="<?= $item['title'] ?>">
                        <?php else: ?>
                            <div class="item-image-placeholder">📦</div>
                        <?php endif; ?>
                        <div class="item-body">
                            <div class="item-title mb-1"><?= $item['title'] ?></div>
                            <div class="item-price mb-1">
                                KES <?= number_format($item['price'], 2) ?>
                            </div>
                            <div class="text-muted mb-2" style="font-size: 12px;">
                                <i class="fas fa-map-marker-alt me-1"></i><?= $item['location'] ?>
                            </div>
                            <div class="text-muted mb-2" style="font-size: 12px;">
                                <i class="fas fa-user me-1"></i><?= $item['fullname'] ?>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <a href="https://wa.me/254<?= substr($item['phone'], 1) ?>?text=Hi, I'm interested in <?= urlencode($item['title']) ?>"
                                   class="btn btn-whatsapp btn-sm flex-fill" target="_blank">
                                    <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                </a>
                                <a href="tel:<?= $item['phone'] ?>" 
                                   class="btn btn-call btn-sm flex-fill">
                                    <i class="fas fa-phone me-1"></i>Call
                                </a>
                                <a href="favorites.php?remove=<?= $item['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Remove from favorites?')">
                                    <i class="fas fa-heart-broken"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-heart d-block"></i>
            <h4>No favorites yet</h4>
            <p>Browse items and save your favorites!</p>
            <a href="index.php" class="btn mt-3 text-white fw-bold px-4 py-2"
               style="background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 25px;">
                <i class="fas fa-search me-2"></i>Browse Items
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
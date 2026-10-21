<?php 
require_once 'config.php';

// Protect this page - must be logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get current user
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get all available AND approved items only
$items_sql = "SELECT items.*, users.fullname, users.phone 
              FROM items 
              JOIN users ON items.user_id = users.id 
              WHERE items.status = 'available' 
              AND items.approved = 'approved'
              ORDER BY items.created_at DESC";
$items_result = mysqli_query($conn, $items_sql);

// Get categories
$categories = ['All', 'Electronics', 'Furniture', 'Clothing', 'Vehicles', 
               'Books', 'Home Appliances', 'Sports', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyBuy - Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        
        body { background: #f5f6fa; }
        
        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-size: 24px;
            font-weight: 700;
            color: white !important;
        }
        .navbar-brand span {
            color: #ffd700;
        }
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
        }
        .nav-link:hover {
            color: white !important;
        }
        .btn-post {
            background: #ffd700;
            color: #333;
            font-weight: 600;
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            transition: all 0.3s;
        }
        .btn-post:hover {
            background: #ffcc00;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,215,0,0.4);
        }

        /* Hero Search */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 0;
            color: white;
            text-align: center;
        }
        .hero-section h1 {
            font-weight: 700;
            font-size: 32px;
        }
        .search-box {
            max-width: 600px;
            margin: 20px auto 0;
        }
        .search-box .form-control {
            border-radius: 25px 0 0 25px;
            padding: 12px 20px;
            border: none;
            font-size: 16px;
        }
        .search-box .btn-search {
            border-radius: 0 25px 25px 0;
            background: #ffd700;
            color: #333;
            font-weight: 600;
            border: none;
            padding: 12px 25px;
        }

        /* Categories */
        .category-section {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .category-btn {
            border-radius: 25px;
            padding: 6px 18px;
            font-size: 14px;
            font-weight: 500;
            margin: 5px;
            border: 2px solid #667eea;
            color: #667eea;
            background: white;
            transition: all 0.3s;
            cursor: pointer;
        }
        .category-btn:hover, .category-btn.active {
            background: #667eea;
            color: white;
        }

        /* Item Cards */
        .item-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            margin-bottom: 20px;
            height: 100%;
        }
        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .item-image-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea20, #764ba220);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
        }
        .item-body {
            padding: 15px;
        }
        .item-category {
            font-size: 11px;
            font-weight: 600;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .item-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin: 5px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .item-price {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
        }
        .item-location {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        .item-seller {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .btn-view {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 13px;
            font-weight: 500;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s;
        }
        .btn-view:hover {
            opacity: 0.9;
            color: white;
        }
        .btn-favorite {
            background: white;
            color: #dc3545;
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 13px;
            font-weight: 500;
            width: 100%;
            margin-top: 6px;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .btn-favorite:hover {
            background: #dc3545;
            color: white;
        }

        /* Modal */
        .modal-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        .contact-btn {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 10px;
        }
        .btn-whatsapp {
            background: #25D366;
            color: white;
            border: none;
        }
        .btn-call {
            background: #667eea;
            color: white;
            border: none;
        }

        /* Post Item Modal */
        .post-form .form-control,
        .post-form .form-select {
            border-radius: 10px;
            border: 2px solid #eee;
            padding: 10px 15px;
        }
        .post-form .form-control:focus,
        .post-form .form-select:focus {
            border-color: #667eea;
            box-shadow: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }
        .empty-state i {
            font-size: 80px;
            margin-bottom: 20px;
            color: #ddd;
        }

        /* Stats Bar */
        .stats-bar {
            background: white;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }
        .stat-label {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            🛍️ Easy<span>Buy</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="favorites.php">
                        <i class="fas fa-heart me-1"></i>Favorites
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="my_listings.php">
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
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="my_listings.php">
                                <i class="fas fa-box me-2"></i>My Listings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="php/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <h1>🛍️ Find Amazing Second-Hand Deals</h1>
        <p class="opacity-75">Buy and sell second-hand items in your community</p>
        <div class="search-box">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput"
                       placeholder="Search for phones, furniture, clothes...">
                <button class="btn btn-search" onclick="searchItems()">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Categories -->
<div class="category-section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-center">
            <?php foreach ($categories as $category): ?>
                <button class="category-btn <?= $category == 'All' ? 'active' : '' ?>"
                        onclick="filterCategory(this, '<?= $category ?>')">
                    <?= $category ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container mt-4">
    <div class="row" id="itemsGrid">
        <?php if (mysqli_num_rows($items_result) > 0): ?>
            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 item-col" 
                     data-category="<?= $item['category'] ?>"
                     data-title="<?= strtolower($item['title']) ?>">
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
                            <div class="item-category"><?= $item['category'] ?></div>
                            <div class="item-title" title="<?= $item['title'] ?>">
                                <?= $item['title'] ?>
                            </div>
                            <div class="item-price">
                                KES <?= number_format($item['price'], 2) ?>
                            </div>
                            <div class="item-location">
                                <i class="fas fa-map-marker-alt me-1"></i><?= $item['location'] ?>
                            </div>
                            <div class="item-seller">
                                <i class="fas fa-user me-1"></i><?= $item['fullname'] ?>
                            </div>
                            <!-- View Details Button -->
                            <button class="btn btn-view" 
                                    onclick="viewItem(
                                        '<?= addslashes($item['title']) ?>',
                                        '<?= addslashes($item['description']) ?>',
                                        '<?= $item['price'] ?>',
                                        '<?= $item['category'] ?>',
                                        '<?= $item['location'] ?>',
                                        '<?= $item['fullname'] ?>',
                                        '<?= $item['phone'] ?>'
                                    )">
                                <i class="fas fa-eye me-1"></i>View Details
                            </button>
                            <!-- Save to Favorites Button -->
                            <a href="php/add_favorite.php?item_id=<?= $item['id'] ?>" 
                               class="btn-favorite">
                                <i class="fas fa-heart me-1"></i>Save to Favorites
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h4>No approved items yet</h4>
                    <p>Be the first to post and get approved!</p>
                    <button class="btn btn-post mt-3" 
                            data-bs-toggle="modal" data-bs-target="#postModal">
                        <i class="fas fa-plus me-2"></i>Post First Item
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- View Item Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <span class="badge bg-primary" id="modalCategory"></span>
                </div>
                <h4 class="text-primary fw-bold" id="modalPrice"></h4>
                <p id="modalDescription" class="text-muted"></p>
                <hr>
                <p><i class="fas fa-map-marker-alt text-danger me-2"></i>
                    <strong>Location:</strong> <span id="modalLocation"></span></p>
                <p><i class="fas fa-user text-primary me-2"></i>
                    <strong>Seller:</strong> <span id="modalSeller"></span></p>
                <hr>
                <h6 class="fw-bold mb-3">Contact Seller:</h6>
                <button class="btn btn-whatsapp contact-btn" id="whatsappBtn">
                    <i class="fab fa-whatsapp me-2"></i>Chat on WhatsApp
                </button>
                <button class="btn btn-call contact-btn" id="callBtn">
                    <i class="fas fa-phone me-2"></i>Call Seller
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Post Item Modal -->
<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Post New Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Payment Notice -->
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> After posting, you will be required to pay a 
                    <strong>5% listing fee</strong> via M-Pesa. 
                    Your item will only be visible after payment and admin approval.
                </div>
                <form class="post-form" method="POST" action="php/post_item.php" 
                      enctype="multipart/form-data">
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
                        <input type="file" name="image" class="form-control" 
                               accept="image/*">
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-bold py-3"
                            style="background: linear-gradient(135deg, #667eea, #764ba2); 
                                   border-radius: 10px; border: none;">
                        <i class="fas fa-upload me-2"></i>Post Item & Pay Fee
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // View Item Details
    function viewItem(title, description, price, category, location, seller, phone) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalDescription').textContent = description;
        document.getElementById('modalPrice').textContent = 'KES ' + parseFloat(price).toLocaleString();
        document.getElementById('modalCategory').textContent = category;
        document.getElementById('modalLocation').textContent = location;
        document.getElementById('modalSeller').textContent = seller;
        
        // WhatsApp link
        const message = `Hi, I'm interested in your item: ${title} listed on EasyBuy for KES ${price}`;
        document.getElementById('whatsappBtn').onclick = function() {
            window.open(`https://wa.me/254${phone.substring(1)}?text=${encodeURIComponent(message)}`, '_blank');
        };
        
        // Call link
        document.getElementById('callBtn').onclick = function() {
            window.open(`tel:${phone}`, '_self');
        };
        
        new bootstrap.Modal(document.getElementById('viewModal')).show();
    }

    // Filter by Category
    function filterCategory(btn, category) {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const items = document.querySelectorAll('.item-col');
        items.forEach(item => {
            if (category === 'All' || item.dataset.category === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Search Items
    function searchItems() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const items = document.querySelectorAll('.item-col');
        items.forEach(item => {
            if (item.dataset.title.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Search on Enter key
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') searchItems();
    });
</script>
</body>
</html>

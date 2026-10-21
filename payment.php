<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$item_id = intval($_GET['item_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Get item
$sql = "SELECT * FROM items WHERE id='$item_id' AND user_id='$user_id'";
$result = mysqli_query($conn, $sql);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    redirect('index.php');
}

$fee = $item['price'] * 0.05;
$success = '';
$error = '';

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transaction_code = sanitize($_POST['transaction_code']);
    
    if (empty($transaction_code)) {
        $error = "Please enter your M-Pesa transaction code!";
    } else {
        // Save payment
        $sql = "INSERT INTO payments (item_id, user_id, amount, transaction_code, status) 
                VALUES ('$item_id', '$user_id', '$fee', '$transaction_code', 'pending')";
        
        if (mysqli_query($conn, $sql)) {
            // Update item payment status to pending confirmation
            mysqli_query($conn, "UPDATE items SET payment_status='paid', 
                payment_amount='$fee', payment_date=NOW() WHERE id='$item_id'");
            $success = "Payment submitted! Admin will verify and approve your listing shortly.";
        } else {
            $error = "Failed to submit payment. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyBuy - Pay Listing Fee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 25px 30px;
            color: white;
            text-align: center;
        }
        .card-body-custom { padding: 30px; }
        .item-summary {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .fee-box {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            border: 2px solid #667eea;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .fee-amount {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
        }
        .mpesa-steps {
            background: #e8f5e9;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        .mpesa-steps h6 {
            color: #2e7d32;
            font-weight: 700;
        }
        .mpesa-steps ol li {
            font-size: 13px;
            margin-bottom: 5px;
            color: #333;
        }
        .paybill-number {
            background: #2e7d32;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 20px;
            font-weight: 700;
            display: inline-block;
            margin: 10px 0;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #eee;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: none;
        }
        .btn-pay {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
        }
        .btn-skip {
            background: #f8f9fa;
            color: #666;
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 10px;
            font-weight: 500;
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="payment-card">
                    <div class="card-header-custom">
                        <h4 class="fw-bold mb-1">
                            <i class="fas fa-money-bill-wave me-2"></i>Pay Listing Fee
                        </h4>
                        <p class="mb-0 opacity-75">Pay 5% to get your item approved & listed</p>
                    </div>
                    <div class="card-body-custom">
                        <?php if ($success): ?>
                            <div class="alert alert-success text-center">
                                <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                <strong><?= $success ?></strong>
                            </div>
                            <a href="index.php" class="btn btn-pay mt-2">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                        <?php else: ?>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>

                            <!-- Item Summary -->
                            <div class="item-summary">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Item:</span>
                                    <strong><?= $item['title'] ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="text-muted">Item Price:</span>
                                    <strong>KES <?= number_format($item['price'], 2) ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="text-muted">Listing Fee (5%):</span>
                                    <strong class="text-success">KES <?= number_format($fee, 2) ?></strong>
                                </div>
                            </div>

                            <!-- Fee Amount -->
                            <div class="fee-box">
                                <p class="text-muted mb-1">Amount to Pay</p>
                                <div class="fee-amount">KES <?= number_format($fee, 2) ?></div>
                                <small class="text-muted">5% of KES <?= number_format($item['price'], 2) ?></small>
                            </div>

                            <!-- M-Pesa Instructions -->
                            <div class="mpesa-steps">
                                <h6><i class="fas fa-mobile-alt me-2"></i>Pay via M-Pesa</h6>
                                <div class="text-center mb-2">
                                    <span>Paybill Number:</span>
                                    <div class="paybill-number">522522</div>
                                    <small class="text-muted d-block">Account: EASYBUY<?= $item_id ?></small>
                                </div>
                                <ol>
                                    <li>Go to M-Pesa on your phone</li>
                                    <li>Select <strong>Lipa na M-Pesa</strong></li>
                                    <li>Select <strong>Pay Bill</strong></li>
                                    <li>Enter Business No: <strong>522522</strong></li>
                                    <li>Account No: <strong>EASYBUY<?= $item_id ?></strong></li>
                                    <li>Enter Amount: <strong>KES <?= number_format($fee, 2) ?></strong></li>
                                    <li>Enter PIN and confirm</li>
                                </ol>
                            </div>

                            <!-- Transaction Code Form -->
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        M-Pesa Transaction Code
                                    </label>
                                    <input type="text" name="transaction_code" 
                                           class="form-control text-uppercase"
                                           placeholder="e.g. QGH7X4KL2P" required
                                           style="letter-spacing: 2px; font-weight: 600;">
                                    <small class="text-muted">
                                        Enter the code from your M-Pesa confirmation SMS
                                    </small>
                                </div>
                                <button type="submit" class="btn-pay">
                                    <i class="fas fa-check-circle me-2"></i>Confirm Payment
                                </button>
                            </form>

                            <a href="index.php" class="btn btn-skip text-center d-block">
                                <i class="fas fa-clock me-2"></i>Pay Later (Item won't be visible)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
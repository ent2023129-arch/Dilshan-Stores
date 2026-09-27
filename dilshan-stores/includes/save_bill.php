<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to save a bill.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['items']) || !is_array($input['items'])) {
    echo json_encode(['success' => false, 'message' => 'No items to save.']);
    exit;
}

$customerName = sanitize($input['customer_name'] ?? 'Walk-in Customer');
$invoiceNo    = sanitize($input['invoice_no'] ?? ('INV-' . time()));
$billDate     = !empty($input['bill_date']) ? $input['bill_date'] : date('Y-m-d');
$subtotal     = (float)($input['subtotal'] ?? 0);
$discountAmt  = (float)($input['discount_amt'] ?? 0);
$taxAmt       = (float)($input['tax_amt'] ?? 0);
$grandTotal   = (float)($input['grand_total'] ?? 0);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'INSERT INTO bills (user_id, customer_name, invoice_no, bill_date, subtotal, discount_amt, tax_amt, grand_total, created_at)
         VALUES (:user_id, :customer_name, :invoice_no, :bill_date, :subtotal, :discount_amt, :tax_amt, :grand_total, NOW())'
    );
    $stmt->execute([
        'user_id'       => $_SESSION['user_id'],
        'customer_name' => $customerName,
        'invoice_no'    => $invoiceNo,
        'bill_date'     => $billDate,
        'subtotal'      => $subtotal,
        'discount_amt'  => $discountAmt,
        'tax_amt'       => $taxAmt,
        'grand_total'   => $grandTotal,
    ]);
    $billId = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        'INSERT INTO bill_items (bill_id, item_name, price, qty, amount) VALUES (:bill_id, :item_name, :price, :qty, :amount)'
    );
    foreach ($input['items'] as $item) {
        $name  = sanitize($item['name'] ?? '');
        $price = (float)($item['price'] ?? 0);
        $qty   = (int)($item['qty'] ?? 0);
        if ($name === '' || $price <= 0 || $qty <= 0) {
            continue;
        }
        $itemStmt->execute([
            'bill_id'   => $billId,
            'item_name' => $name,
            'price'     => $price,
            'qty'       => $qty,
            'amount'    => $price * $qty,
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'invoice_no' => $invoiceNo, 'bill_id' => $billId]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

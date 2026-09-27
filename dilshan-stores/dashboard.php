<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';
require_login();

$pageTitle = 'Bill Calculator';

// Fetch this user's recent bill history
$stmt = $pdo->prepare('SELECT * FROM bills WHERE user_id = :uid ORDER BY created_at DESC LIMIT 10');
$stmt->execute(['uid' => $_SESSION['user_id']]);
$bills = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="container py-4">

  <div class="bill-card mb-5">
    <div class="bill-head">
      <h2 class="mb-1"><i class="fa-solid fa-store"></i> Dilshan Stores</h2>
      <p class="mb-0 opacity-75">Automatic Bill Calculation</p>
    </div>

    <div class="bill-meta">
      <div><input type="text" id="customerName" placeholder="Customer Name"></div>
      <div><input type="text" id="invoiceNo" placeholder="Invoice No."></div>
      <div><input type="date" id="billDate"></div>
    </div>

    <div class="add-item">
      <input type="text" id="itemName" placeholder="Item / Product name">
      <input type="number" id="itemPrice" placeholder="Price (Rs.)" min="0" step="0.01">
      <input type="number" id="itemQty" placeholder="Qty" min="1" value="1">
      <button type="button" class="btn btn-ds-primary btn-add" id="addItemBtn"><i class="fa-solid fa-plus"></i> Add</button>
    </div>

    <div class="items-wrapper">
      <table>
        <thead>
          <tr>
            <th style="text-align:left;">Item</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="itemTable">
          <tr><td colspan="5" class="empty-msg">No items added yet — add your first item above.</td></tr>
        </tbody>
      </table>
    </div>

    <div class="totals">
      <div class="adjustments">
        <div class="adjust-row">
          <label>Discount</label>
          <select id="discType">
            <option value="%">%</option>
            <option value="flat">Rs. Flat</option>
          </select>
          <input type="number" id="discountVal" value="0" min="0">
        </div>
        <div class="adjust-row">
          <label>Tax (GST)</label>
          <input type="number" id="taxVal" value="18" min="0">
          <span>%</span>
        </div>
      </div>
      <div class="summary">
        <div class="row"><span>Subtotal:</span><span id="subTotal">Rs. 0.00</span></div>
        <div class="row"><span>Discount:</span><span id="discAmt">&minus; Rs. 0.00</span></div>
        <div class="row"><span>Tax:</span><span id="taxAmt">+ Rs. 0.00</span></div>
        <div class="row grand"><span>Grand Total:</span><span id="grandTotal">Rs. 0.00</span></div>
      </div>
    </div>

    <div class="bill-actions">
      <button type="button" class="btn btn-clear" id="clearBillBtn">Clear Bill</button>
      <button type="button" class="btn btn-save" id="saveBillBtn"><i class="fa-solid fa-floppy-disk"></i> Save Bill</button>
      <button type="button" class="btn btn-print" id="printBillBtn"><i class="fa-solid fa-print"></i> Print / Save PDF</button>
    </div>
    <div class="px-4 pb-3" id="saveMsg"></div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <h4 class="ds-section-title mb-3"><i class="fa-solid fa-clock-rotate-left"></i> Your Recent Bills</h4>
      <?php if (!$bills): ?>
        <p class="text-muted mb-0">You haven't saved any bills yet. Create one above!</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-striped history-table align-middle">
            <thead>
              <tr>
                <th>Invoice No.</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Grand Total</th>
                <th>Saved On</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bills as $b): ?>
                <tr>
                  <td><?= sanitize($b['invoice_no']) ?></td>
                  <td><?= sanitize($b['customer_name']) ?></td>
                  <td><?= sanitize($b['bill_date']) ?></td>
                  <td>Rs. <?= number_format((float)$b['subtotal'], 2) ?></td>
                  <td>Rs. <?= number_format((float)$b['discount_amt'], 2) ?></td>
                  <td>Rs. <?= number_format((float)$b['tax_amt'], 2) ?></td>
                  <td class="fw-bold">Rs. <?= number_format((float)$b['grand_total'], 2) ?></td>
                  <td><small class="text-muted"><?= sanitize(date('d M Y, H:i', strtotime($b['created_at']))) ?></small></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

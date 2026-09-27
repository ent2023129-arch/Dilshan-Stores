/* ==========================================================
   Dilshan Stores - Automatic Bill Calculation System
   Client-side JavaScript
   Features implemented:
   1. Dynamic Content Updates (bill items, live totals)
   2. Interactive Image Slider
   3. Form Validation (register / login / contact)
   4. Smooth Scrolling
   5. Event Handling (hover / click)
   6. Custom Animations (fade-in on scroll)
   ========================================================== */

document.addEventListener('DOMContentLoaded', function () {
  initSmoothScroll();
  initScrollAnimations();
  initImageSlider();
  initFormValidation();
  initBillCalculator();
});

/* ---------------------------------------------------------
   3 & 5. Smooth Scrolling for in-page nav links
--------------------------------------------------------- */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId.length > 1) {
        const target = document.querySelector(targetId);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  });
}

/* ---------------------------------------------------------
   6. Custom Animations - fade elements in as they enter view
--------------------------------------------------------- */
function initScrollAnimations() {
  const items = document.querySelectorAll('.fade-in-up');
  if (!items.length) return;

  if (!('IntersectionObserver' in window)) {
    items.forEach(function (el) { el.classList.add('visible'); });
    return;
  }

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  items.forEach(function (el) { observer.observe(el); });
}

/* ---------------------------------------------------------
   2. Interactive Image Slider (auto + manual controls)
--------------------------------------------------------- */
function initImageSlider() {
  const slider = document.getElementById('dsSlider');
  if (!slider) return;

  const slides = slider.querySelectorAll('.ds-slide');
  const dots = slider.querySelectorAll('.slider-dot');
  const prevBtn = slider.querySelector('.slider-prev');
  const nextBtn = slider.querySelector('.slider-next');
  let current = 0;
  let timer;

  function show(index) {
    slides.forEach(function (s, i) { s.classList.toggle('active', i === index); });
    dots.forEach(function (d, i) { d.classList.toggle('active', i === index); });
    current = index;
  }

  function next() { show((current + 1) % slides.length); }
  function prev() { show((current - 1 + slides.length) % slides.length); }

  function startAuto() {
    timer = setInterval(next, 4000);
  }
  function stopAuto() {
    clearInterval(timer);
  }

  if (nextBtn) nextBtn.addEventListener('click', function () { next(); stopAuto(); startAuto(); });
  if (prevBtn) prevBtn.addEventListener('click', function () { prev(); stopAuto(); startAuto(); });
  dots.forEach(function (dot, i) {
    dot.addEventListener('click', function () { show(i); stopAuto(); startAuto(); });
  });

  // Pause on hover (event handling)
  slider.addEventListener('mouseenter', stopAuto);
  slider.addEventListener('mouseleave', startAuto);

  show(0);
  startAuto();
}

/* ---------------------------------------------------------
   4. Form Validation - register / login / contact
--------------------------------------------------------- */
function initFormValidation() {
  document.querySelectorAll('form[data-validate]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      let valid = true;

      form.querySelectorAll('[data-rule]').forEach(function (field) {
        const rules = field.getAttribute('data-rule').split('|');
        const feedback = field.parentElement.querySelector('.invalid-feedback-custom');
        let fieldValid = true;
        let message = '';

        rules.forEach(function (rule) {
          if (!fieldValid) return; // stop at first failed rule

          if (rule === 'required' && field.value.trim() === '') {
            fieldValid = false;
            message = 'This field is required.';
          }
          if (rule === 'email' && field.value.trim() !== '') {
            const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRe.test(field.value.trim())) {
              fieldValid = false;
              message = 'Please enter a valid email address.';
            }
          }
          if (rule === 'minlen6' && field.value.trim().length < 6) {
            fieldValid = false;
            message = 'Must be at least 6 characters.';
          }
          if (rule === 'match-password') {
            const pwField = form.querySelector('[name="password"]');
            if (pwField && field.value !== pwField.value) {
              fieldValid = false;
              message = 'Passwords do not match.';
            }
          }
        });

        field.classList.toggle('is-invalid', !fieldValid);
        if (feedback) {
          feedback.textContent = message;
          feedback.classList.toggle('show', !fieldValid);
        }
        if (!fieldValid) valid = false;
      });

      if (!valid) {
        e.preventDefault();
        form.classList.add('was-validated-custom');
      }
    });

    // live re-validation as the user types (nice UX touch)
    form.querySelectorAll('[data-rule]').forEach(function (field) {
      field.addEventListener('input', function () {
        field.classList.remove('is-invalid');
        const feedback = field.parentElement.querySelector('.invalid-feedback-custom');
        if (feedback) feedback.classList.remove('show');
      });
    });
  });
}

/* ---------------------------------------------------------
   1. Dynamic Content Updates - Automatic Bill Calculator
--------------------------------------------------------- */
function initBillCalculator() {
  const table = document.getElementById('itemTable');
  if (!table) return; // not on this page

  const items = [];
  let serial = 1;

  const itemNameEl = document.getElementById('itemName');
  const itemPriceEl = document.getElementById('itemPrice');
  const itemQtyEl = document.getElementById('itemQty');
  const addBtn = document.getElementById('addItemBtn');
  const discountValEl = document.getElementById('discountVal');
  const discTypeEl = document.getElementById('discType');
  const taxValEl = document.getElementById('taxVal');
  const saveBtn = document.getElementById('saveBillBtn');
  const clearBtn = document.getElementById('clearBillBtn');
  const printBtn = document.getElementById('printBillBtn');
  const saveMsg = document.getElementById('saveMsg');

  // Auto-fill invoice number & date on first load
  const invoiceNoEl = document.getElementById('invoiceNo');
  const billDateEl = document.getElementById('billDate');
  if (invoiceNoEl && !invoiceNoEl.value) {
    invoiceNoEl.value = 'INV-' + Date.now().toString().slice(-6);
  }
  if (billDateEl && !billDateEl.value) {
    billDateEl.value = new Date().toISOString().slice(0, 10);
  }

  function formatLKR(n) {
    return 'Rs. ' + n.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function addItem() {
    const name = itemNameEl.value.trim();
    const price = parseFloat(itemPriceEl.value);
    const qty = parseInt(itemQtyEl.value, 10);

    if (!name) { alert('Please enter an item name.'); return; }
    if (isNaN(price) || price <= 0) { alert('Please enter a valid price.'); return; }
    if (isNaN(qty) || qty <= 0) { alert('Please enter a valid quantity.'); return; }

    items.push({ id: serial++, name: name, price: price, qty: qty });
    itemNameEl.value = '';
    itemPriceEl.value = '';
    itemQtyEl.value = '1';
    itemNameEl.focus();
    render();
  }

  function updateQty(id, qty) {
    const item = items.find(function (i) { return i.id === id; });
    if (item) { item.qty = Math.max(1, parseInt(qty, 10) || 1); render(); }
  }

  function removeItem(id) {
    const idx = items.findIndex(function (i) { return i.id === id; });
    if (idx > -1) { items.splice(idx, 1); render(); }
  }

  function clearAll() {
    if (items.length && !confirm('Clear the entire bill?')) return;
    items.length = 0;
    discountValEl.value = 0;
    if (saveMsg) saveMsg.innerHTML = '';
    render();
  }

  function render() {
    table.innerHTML = '';
    if (!items.length) {
      table.innerHTML = '<tr><td colspan="5" class="empty-msg">No items added yet — add your first item above.</td></tr>';
    } else {
      items.forEach(function (item) {
        const tr = document.createElement('tr');
        tr.innerHTML =
          '<td class="item-name"></td>' +
          '<td class="price-cell"></td>' +
          '<td><input type="number" class="qty-input" min="1" value="' + item.qty + '"></td>' +
          '<td class="amount-cell"><strong></strong></td>' +
          '<td><button type="button" class="btn-del">&times;</button></td>';

        tr.querySelector('.item-name').textContent = item.name;
        tr.querySelector('.price-cell').textContent = formatLKR(item.price);
        tr.querySelector('.amount-cell strong').textContent = formatLKR(item.price * item.qty);

        tr.querySelector('.qty-input').addEventListener('change', function () {
          updateQty(item.id, this.value);
        });
        tr.querySelector('.btn-del').addEventListener('click', function () {
          removeItem(item.id);
        });

        table.appendChild(tr);
      });
    }
    calculate();
  }

  function calculate() {
    const subtotal = items.reduce(function (sum, i) { return sum + i.price * i.qty; }, 0);

    const discVal = parseFloat(discountValEl.value) || 0;
    const discType = discTypeEl.value;
    let discount = discType === '%' ? (subtotal * discVal / 100) : discVal;
    discount = Math.min(discount, subtotal);

    const taxRate = parseFloat(taxValEl.value) || 0;
    const taxable = subtotal - discount;
    const tax = taxable * taxRate / 100;
    const grand = taxable + tax;

    document.getElementById('subTotal').textContent = formatLKR(subtotal);
    document.getElementById('discAmt').textContent = '\u2212 ' + formatLKR(discount);
    document.getElementById('taxAmt').textContent = '+ ' + formatLKR(tax);
    document.getElementById('grandTotal').textContent = formatLKR(grand);

    return { subtotal: subtotal, discount: discount, tax: tax, grand: grand };
  }

  function saveBill() {
    if (!items.length) { alert('Add at least one item before saving the bill.'); return; }

    const totals = calculate();
    const payload = {
      customer_name: document.getElementById('customerName').value.trim() || 'Walk-in Customer',
      invoice_no: document.getElementById('invoiceNo').value.trim(),
      bill_date: document.getElementById('billDate').value,
      discount_type: discTypeEl.value,
      discount_val: parseFloat(discountValEl.value) || 0,
      tax_val: parseFloat(taxValEl.value) || 0,
      subtotal: totals.subtotal,
      discount_amt: totals.discount,
      tax_amt: totals.tax,
      grand_total: totals.grand,
      items: items
    };

    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';

    fetch('includes/save_bill.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Bill';
        if (data.success) {
          if (saveMsg) saveMsg.innerHTML = '<div class="alert alert-success py-2 mt-2 mb-0">Bill saved successfully (Invoice ' + data.invoice_no + ').</div>';
          setTimeout(function () { window.location.reload(); }, 1200);
        } else {
          if (saveMsg) saveMsg.innerHTML = '<div class="alert alert-danger py-2 mt-2 mb-0">' + (data.message || 'Could not save the bill.') + '</div>';
        }
      })
      .catch(function () {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Bill';
        if (saveMsg) saveMsg.innerHTML = '<div class="alert alert-danger py-2 mt-2 mb-0">Network error — please try again.</div>';
      });
  }

  addBtn.addEventListener('click', addItem);
  [itemNameEl, itemPriceEl, itemQtyEl].forEach(function (el) {
    el.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); addItem(); } });
  });
  discountValEl.addEventListener('input', calculate);
  discTypeEl.addEventListener('change', calculate);
  taxValEl.addEventListener('input', calculate);
  clearBtn.addEventListener('click', clearAll);
  if (printBtn) printBtn.addEventListener('click', function () { window.print(); });
  if (saveBtn) saveBtn.addEventListener('click', saveBill);

  render();
}

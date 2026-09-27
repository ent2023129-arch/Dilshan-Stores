# Dilshan Stores — Automatic Bill Calculation System

An interactive web application built for the **ICT 2209 Web Technologies Mini Project**.
It lets a shop ("Dilshan Stores") build a customer bill in the browser (add items,
apply discount and tax, see the grand total update live), print/save the invoice,
and — after logging in — save the bill to a MySQL database for future reference.

## Tech Stack
- **Frontend:** HTML5, CSS3, Bootstrap 5, vanilla JavaScript
- **Backend:** PHP (PDO/MySQLi via PDO)
- **Database:** MySQL (via XAMPP / WAMP)

## Features Implemented

### Frontend / UX
- 5 pages: Home, Features, Contact, Login, Register (+ protected Dashboard)
- Fully responsive layout (mobile, tablet, desktop) using Bootstrap grid + custom CSS
- JavaScript features (6 implemented, only 3 required by the brief):
  1. **Dynamic Content Updates** — add/edit/remove bill items with live recalculation
  2. **Interactive Image Slider** — auto-rotating + manual prev/next/dot controls on the Home page
  3. **Form Validation** — client-side validation for Register, Login and Contact forms (mirrored server-side in PHP)
  4. **Smooth Scrolling** — in-page nav links (e.g. "Learn More" on Home)
  5. **Event Handling** — hover states, click handlers on buttons, pause-on-hover slider
  6. **Custom Animations** — fade-in-on-scroll for sections/cards

### Backend (PHP + MySQL)
- **`database.sql`** — creates the `dilshan_stores` database with `users`, `bills`, `bill_items`, `messages` tables
- **User Authentication**
  - `auth/register.php` — sign-up form, passwords stored with `password_hash()`
  - `auth/login.php` — validates credentials with `password_verify()`, starts a session
  - `auth/logout.php` — destroys the session
- **Contact Form:** `contact.php` — validates and stores submissions in the `messages` table
- **Bill Calculator backend:** `dashboard.php` (protected page) + `includes/save_bill.php`
  (AJAX endpoint) save each bill and its line items to `bills` / `bill_items`, tied to the
  logged-in user, and the dashboard lists the user's recent saved bills.

## Folder Structure
```
dilshan-stores/
├── css/
│   └── style.css
├── js/
│   └── script.js
├── images/                (place any custom images here)
├── includes/
│   ├── db.php             # database connection (PDO)
│   ├── functions.php      # helpers: sanitize(), require_login(), etc.
│   ├── header.php          # shared nav/header partial
│   ├── footer.php          # shared footer partial
│   └── save_bill.php       # AJAX endpoint: saves a bill + items
├── auth/
│   ├── register.php
│   ├── login.php
│   └── logout.php
├── index.html              # Home page (static HTML)
├── features.php            # Features page
├── contact.php             # Contact page + form handler
├── dashboard.php           # Protected: bill calculator + bill history
├── database.sql            # MySQL schema (import this)
└── README.md
```

## Setup Instructions (XAMPP / WAMP)

1. **Install XAMPP or WAMP** if you don't already have it, and start the **Apache** and
   **MySQL** services from the control panel.

2. **Copy the project folder** into your server's web root:
   - XAMPP (Windows): `C:\xampp\htdocs\dilshan-stores`
   - XAMPP (macOS/Linux): `/opt/lampp/htdocs/dilshan-stores`
   - WAMP: `C:\wamp64\www\dilshan-stores`

3. **Import the database:**
   - Open `http://localhost/phpmyadmin`
   - Click **Import** → choose `database.sql` → click **Go**
   - This creates the `dilshan_stores` database and all required tables.
   - (Alternatively, from a terminal: `mysql -u root -p < database.sql`)

4. **Check the DB credentials** in `includes/db.php` match your local MySQL setup.
   The defaults (`root` / no password) work for a fresh XAMPP/WAMP install.

5. **Open the app** in your browser:
   ```
   http://localhost/dilshan-stores/index.html
   ```

   > **Note:** `index.html` is static plain HTML, so its navigation bar always shows
   > "Login" / "Sign Up" (it can't check your session). Once you log in from there,
   > every other page (`features.php`, `contact.php`, `dashboard.php`, etc.) correctly
   > shows "Bill Calculator" / "Logout" and your username, since those remain PHP pages.

6. **Try it out:**
   - Click **Sign Up** to create an account (stored in the `users` table, password hashed).
   - **Log in**, then open **Bill Calculator** from the nav bar.
   - Add a few items, set a discount and tax rate, click **Save Bill** — it's written to
     the `bills` and `bill_items` tables and appears in "Your Recent Bills" below.
   - Use **Print / Save PDF** to get a clean printable invoice.
   - Try the **Contact** page — messages are stored in the `messages` table.

## Notes
- All form inputs are sanitised with `htmlspecialchars()`/`strip_tags()` and queries use
  PDO prepared statements to guard against SQL injection.
- Passwords are never stored in plain text (`password_hash()` / `password_verify()`).
- Sessions gate access to `dashboard.php` via `require_login()`.

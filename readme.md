# 🍃 The Serene Finder

A fully dynamic, two-sided marketplace web application connecting customers with trusted local service professionals (like HVAC technicians, electricians, and cleaners). 

Built from scratch using **HTML, CSS, Vanilla JavaScript, PHP, and MySQL**, this project demonstrates a complete closed-loop booking system, role-based dashboards, and real-time data filtering.

## ✨ Key Features

**For Customers:**
* **Smart Search & Filter:** Instantly filter professionals by category, keyword, or maximum hourly rate without reloading the page.
* **Dynamic Profiles:** View a professional's bio, location, hourly rate, and real client reviews before booking.
* **Booking System:** Request a service and track its status (Pending, Confirmed, Cancelled, Completed) directly from the dashboard.
* **Review & Rating Engine:** After a job is completed, leave a 1-5 star review that automatically recalculates the professional's overall rating.
* **Historical Archive:** A dedicated ledger of all past and present service requests.

**For Service Providers:**
* **Role-Based Dashboard:** A customized view showing incoming job offers, complete with the customer's name and address.
* **Job Management:** Accept or decline job offers with a single click, instantly updating the customer's dashboard.
* **Profile Management:** A dedicated settings page to manage public-facing details like specialty, category, bio, and hourly rate.

## 🛠️ Tech Stack

* **Frontend:** HTML5, CSS3, Vanilla JavaScript (ES6+), Fetch API
* **Backend:** PHP 8+ (RESTful API architecture)
* **Database:** MySQL (Relational database using PDO for secure queries)
* **State Management:** Session-based authentication & Local/BFCache invalidation

## 🔄 How It Works (The Core Loop)

1. **Discover:** A customer uses the Explore page to find a professional using the dynamic search engine.
2. **Book:** The customer clicks "Book Now" on a profile, creating a `pending` job offer.
3. **Acceptance:** The professional logs in, sees the offer on their dashboard, and clicks "Accept" (status changes to `confirmed`).
4. **Completion:** After the work is done, the customer clicks "Mark Completed & Review", leaving a star rating.
5. **Update:** The database mathematically calculates the professional's new average rating and updates their public profile instantly.

## 🚀 How to Run Locally

Because this project uses PHP and MySQL, you will need a local server environment like **XAMPP, MAMP, or WAMP** to run it.

### Step 1: Clone the Repository
```bash
git clone https://github.com/ShakibHossain124/The-Serene-Finder.git
```
*(Move the cloned folder into your local server's web directory, e.g., the `htdocs` folder in XAMPP).*

### Step 2: Set Up the Database
1. Open your database manager (e.g., phpMyAdmin at `http://localhost/phpmyadmin`).
2. Create a new database named `serene_finder`.
3. Import the provided `.sql` file to automatically generate the tables.

### Step 3: Configure the Database Connection
1. Open the `db.php` file in the root directory.
2. Update the credentials to match your local database:
```php
$host = 'localhost';
$dbname = 'serene_finder';
$user = 'root';
$pass = ''; // Leave blank for default XAMPP
```

### Step 4: Launch the App
Open your web browser and navigate to: `http://localhost/the-serene-finder/index.html`

## 🗄️ Database Architecture

The application relies on four core relational tables mapped to handle the marketplace logic:

### 1. `users` Table
Handles global authentication and basic identity for all accounts.
* `id` (INT, Primary Key)
* `full_name` (VARCHAR)
* `email` (VARCHAR, Unique)
* `password` (VARCHAR, Hashed)
* `role` (ENUM: 'customer', 'provider')
* `created_at` (TIMESTAMP)

### 2. `provider_profiles` Table
Stores public-facing directory data. Links to the `users` table via Foreign Key.
* `id` (INT, Primary Key)
* `user_id` (INT, Foreign Key -> users.id)
* `specialty` (VARCHAR) - *e.g., "Master Plumber"*
* `category` (VARCHAR) - *e.g., "HVAC"*
* `hourly_rate` (DECIMAL)
* `rating` (DECIMAL) - *Dynamically updated by the review engine*
* `reviews_count` (INT)
* `bio` (TEXT)
* `location` (VARCHAR)

### 3. `bookings` Table
The transactional ledger connecting a customer to a provider.
* `id` (INT, Primary Key)
* `customer_id` (INT, Foreign Key -> users.id)
* `provider_id` (INT, Foreign Key -> users.id)
* `issue_description` (TEXT)
* `scheduled_date` (DATE)
* `address` (VARCHAR)
* `status` (ENUM: 'pending', 'confirmed', 'cancelled', 'completed')
* `created_at` (TIMESTAMP)

### 4. `reviews` Table
Stores individual feedback and star ratings linked to completed bookings.
* `id` (INT, Primary Key)
* `booking_id` (INT, Foreign Key -> bookings.id)
* `provider_id` (INT, Foreign Key -> users.id)
* `customer_id` (INT, Foreign Key -> users.id)
* `rating` (INT, 1-5)
* `comment` (TEXT)
* `created_at` (TIMESTAMP)
# The Serene Finder

A two-sided service marketplace connecting customers with local professionals (electricians, plumbers, cleaners, and more).

Built with HTML, CSS, vanilla JavaScript, PHP, and MySQL.

## Key Features

For Customers:
- Search and filter professionals by keyword, category, location, and max hourly rate
- Open provider profiles with bio, rating, location, and recent reviews
- Create bookings with estimated duration and see computed estimated cost
- Track booking status: pending, confirmed, completed, cancelled
- Submit post-service review and rating

For Providers:
- Receive and manage incoming job offers
- Accept or decline pending offers
- View booking duration and payment estimate before deciding
- Manage profile details from settings

## Recent Behavior Updates

- Booking flow now validates required fields and estimated time on the server
- Booking price is computed server-side: estimated_time * hourly_rate + 25
- Review submission is protected with ownership/status checks
- Duplicate reviews for the same booking/customer are blocked
- Provider status updates are restricted to valid transitions (pending -> confirmed/cancelled)
- Archive and Reviews pages now perform real session logout via API
- Dashboard "next appointment" uses upcoming appointments (not completed/cancelled history)

## Tech Stack

- Frontend: HTML5, CSS3, vanilla JavaScript (ES6+), Fetch API
- Backend: PHP 8+ (API endpoints)
- Database: MySQL/MariaDB with PDO
- Auth: Session-based authentication

## Local Setup

1. Clone repository:

```bash
git clone https://github.com/ShakibHossain124/The-Serene-Finder.git
```

2. Move project folder to your server web root (for example, XAMPP htdocs).

3. Create database serene_finder in phpMyAdmin.

4. Import SQL schema/data from api/serene_finder.sql.

5. Configure DB credentials in db.php.

6. Open app in browser:

```text
http://localhost/the-serene-finder/index.html
```

## API Overview

- api/register.php: account registration (customer/provider)
- api/login.php / api/logout.php / api/check_auth.php: session auth
- api/search_providers.php: explore filtering
- api/get_profile.php: provider profile + recent reviews
- api/submit_booking.php: booking creation + server-side cost calculation
- api/get_dashboard_data.php: bookings/job offers for dashboard cards
- api/update_booking_status.php: provider accept/decline for pending offers
- api/submit_review.php: review submission + rating recalculation
- api/get_my_reviews.php: role-based review history
- api/get_settings.php / api/save_settings.php: role-aware account/profile settings

## Current Database Schema (as used by code)

users
- id (PK)
- full_name
- email
- password_hash
- role (customer/provider)
- created_at

provider_profiles
- user_id (PK, FK -> users.id)
- specialty
- category
- hourly_rate
- rating
- reviews_count
- bio
- location

bookings
- id (PK)
- customer_id (FK -> users.id)
- provider_id (FK -> users.id)
- issue_description
- scheduled_date (DATETIME)
- address
- estimated_time (DECIMAL)
- total_price (DECIMAL)
- status (pending/confirmed/completed/cancelled)

reviews
- id (PK)
- booking_id (FK -> bookings.id)
- provider_id (FK -> users.id)
- customer_id (FK -> users.id)
- rating (1..5)
- comment
- created_at
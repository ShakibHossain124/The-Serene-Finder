# The Serene Finder

The Serene Finder is a full-stack marketplace platform that connects customers with local home-service professionals through a complete booking lifecycle.

This project was built to demonstrate practical web engineering skills across frontend UX, backend APIs, authentication, relational data modeling, and workflow-driven product logic.

## Portfolio Snapshot

### What This Project Solves
- Customers need a trusted way to discover local experts and book services with clear pricing context.
- Providers need a practical dashboard to evaluate and respond to job offers quickly.
- Both sides need transparent status tracking and post-service accountability through reviews.

### What Makes This Build Strong
- End-to-end product flow from search to booking to review completion
- Role-aware dashboards and settings for customers and providers
- Server-side validation for key booking, status, and review operations
- Price and duration visibility across booking cards and history views
- Clean API-driven architecture using PHP + MySQL with prepared statements

## Core Product Experience

### Customer Journey
1. Discover professionals by keyword, category, location, and hourly rate.
2. Open profile pages with live rating and review context.
3. Submit booking request with date, time window, issue, address, and estimated duration.
4. See estimated service cost before confirmation.
5. Track status changes in dashboard and archive views.
6. Submit service review after confirmed work is completed.

### Provider Journey
1. Receive incoming job offers in dashboard cards.
2. Review customer, location, duration, and payment estimate.
3. Accept or decline pending offers with one click.
4. Maintain public profile and account settings.
5. Monitor rating/reputation updates as reviews arrive.

## Technical Highlights

### Frontend
- HTML5, CSS3, Vanilla JavaScript (ES6+)
- Fetch API integration for all dynamic data flows
- Role-aware rendering and conditional content
- Dynamic booking/review cards with status visualization

### Backend
- PHP 8+ endpoint architecture
- PDO prepared statements for query safety
- Session-based authentication/authorization checks
- Validation-backed state transitions (bookings, offers, reviews)

### Data Layer
- MySQL/MariaDB relational schema
- Linked user/provider/booking/review entities
- Live rating aggregation from review records

## End-to-End Workflow

1. Customer creates booking (status pending).
2. Provider reviews incoming offer and either:
- confirms (status confirmed), or
- declines (status cancelled).
3. Customer completes review flow for confirmed work.
4. Booking is marked completed and provider reputation metrics are recalculated.

## Pricing and Booking Logic

- Customer enters estimated service time in hours.
- Server computes total estimate:

```text
total_price = (estimated_time * provider_hourly_rate) + 25
```

- Estimated duration and amount are surfaced in:
- dashboard cards
- job offer cards
- booking archive
- review history context

## Validation and Integrity Rules

### Booking Creation
- Prevents invalid provider selection and self-booking
- Requires date, time window, address, city, and zip
- Requires estimated time greater than zero
- Converts UI time-window labels to valid DATETIME values

### Job Offer Status Updates
- Only provider owner can update offer
- Only pending offers are eligible for transitions
- Only confirmed or cancelled are accepted statuses

### Review Submission
- Allowed only for confirmed bookings owned by logged-in customer
- Booking/provider/customer relationship is validated
- Duplicate reviews for the same booking/customer are blocked
- Successful review updates booking status to completed
- Provider rating and review count are recalculated from source reviews

### Appointment Prioritization
- Next-appointment widgets prioritize upcoming work
- Completed/cancelled history is excluded from next-appointment selection

## Project Structure

```text
.
├── index.html
├── professionals.php
├── profile.html
├── booking.html
├── dashboard.html
├── archive.html
├── my_reviews.html
├── settings.html
├── login.html
├── signup.html
├── style.css
├── db.php
└── api/
    ├── check_auth.php
    ├── get_dashboard_data.php
    ├── get_my_reviews.php
    ├── get_profile.php
    ├── get_providers.php
    ├── get_settings.php
    ├── login.php
    ├── logout.php
    ├── register.php
    ├── save_settings.php
    ├── search_providers.php
    ├── serene_finder.sql
    ├── submit_booking.php
    ├── submit_review.php
    └── update_booking_status.php
```

## API Surface

### Authentication
- api/register.php: account creation for customer/provider
- api/login.php: session start
- api/logout.php: session termination
- api/check_auth.php: auth-state and role check

### Discovery and Profiles
- api/search_providers.php: filter-based provider listing
- api/get_profile.php: provider profile + recent reviews
- api/get_providers.php: additional provider listing endpoint

### Booking Lifecycle
- api/submit_booking.php: create booking + compute total price
- api/get_dashboard_data.php: role-aware bookings/offers payload
- api/update_booking_status.php: provider accept/decline action
- api/submit_review.php: secure review submission + rating update
- api/get_my_reviews.php: customer/provider review history

### Settings
- api/get_settings.php: role-aware settings fetch
- api/save_settings.php: role-aware settings save

## Database Schema (Current)

Source: api/serene_finder.sql

### users
- id (INT, PK)
- full_name (VARCHAR)
- email (VARCHAR, unique)
- password_hash (VARCHAR)
- role (ENUM: customer, provider)
- created_at (TIMESTAMP)

### provider_profiles
- user_id (INT, PK, FK -> users.id)
- specialty (VARCHAR)
- category (VARCHAR)
- hourly_rate (DECIMAL)
- rating (DECIMAL)
- reviews_count (INT)
- bio (TEXT)
- location (VARCHAR)

### bookings
- id (INT, PK)
- customer_id (INT, FK -> users.id)
- provider_id (INT, FK -> users.id)
- issue_description (TEXT)
- scheduled_date (DATETIME)
- address (VARCHAR)
- estimated_time (DECIMAL)
- total_price (DECIMAL)
- status (ENUM: pending, confirmed, completed, cancelled)

### reviews
- id (INT, PK)
- booking_id (INT, FK -> bookings.id)
- provider_id (INT, FK -> users.id)
- customer_id (INT, FK -> users.id)
- rating (INT, constrained 1-5)
- comment (TEXT)
- created_at (TIMESTAMP)

## Run Locally

### Prerequisites
- PHP 8+
- MySQL/MariaDB
- Local server stack (XAMPP, WAMP, or MAMP)

### Setup Steps
1. Clone repository:

```bash
git clone https://github.com/ShakibHossain124/The-Serene-Finder.git
```

2. Move project into web root (example: htdocs for XAMPP).

3. Create database serene_finder.

4. Import api/serene_finder.sql.

5. Configure db.php credentials:

```php
$host = 'localhost';
$dbname = 'serene_finder';
$user = 'root';
$pass = '';
```

6. Start Apache and MySQL, then open:

```text
http://localhost/the-serene-finder/index.html
```

## Frontend Pages

- index.html: landing page and category entry points
- professionals.php: discovery and filtering interface
- profile.html: provider detail and booking entry
- booking.html: service request form and cost summary
- dashboard.html: role-aware operational hub
- archive.html: full booking history ledger
- my_reviews.html: review history and context
- settings.html: account/profile settings by role
- login.html and signup.html: authentication pages

## Engineering Notes

- Keep UI category names aligned with backend category matching behavior.
- If booking time-window labels are changed, update mapping in api/submit_booking.php.
- If fee policy changes, adjust formula in api/submit_booking.php.
- Keep status transition constraints in sync between frontend buttons and API validation.

## Future Enhancements

- Add CSRF protection on state-changing endpoints
- Add rate limiting and lockout rules for auth flows
- Add automated API tests for booking/review transitions
- Refactor inline styles/scripts into modular assets

## License

Educational and portfolio use unless repository owner specifies otherwise.
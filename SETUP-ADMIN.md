# Mini Admin Backend Setup (PHP + MySQL)

## 1) Upload files
Upload all project files to your hosting (public_html or equivalent), keeping folders exactly as they are.

## 2) Create table
Run this SQL in phpMyAdmin (database: `u979944047_bioint`):

- `database/schema.sql`

## 3) Verify DB config
File:

- `admin/includes/config.php`

It already contains your provided DB credentials:

- Host: `localhost`
- DB: `u979944047_bioint`
- User: `u979944047_bioint`
- Password: `Juliocesar1234$`

## 4) Admin login credentials
Default admin login is configured in:

- `admin/includes/config.php`

Current defaults:

- Username: `admin`
- Email login: `admin@biomaussan.com`
- Password: `Admin123!Change`

> IMPORTANT: Change the password hash before production.

Generate a new hash locally:

```bash
php -r "echo password_hash('YOUR_NEW_PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"
```

Then replace `ADMIN_PASSWORD_HASH` in `admin/includes/config.php`.

## 5) Access admin
Open:

- `https://your-domain.com/admin/login.php`

## 6) Form integration
The public website form now submits to:

- `form-handler.php`

Data is validated and stored in MySQL table `submissions`.

## 7) Features included
- Session-based admin authentication
- Protected admin pages
- Dashboard with newest submissions first
- Search + pagination
- CSV export (all or filtered results)
- Single delete + bulk delete with confirmation
- Basic CSRF protection for delete actions
- Safe output escaping to reduce XSS risk
- Prepared statements to reduce SQL injection risk

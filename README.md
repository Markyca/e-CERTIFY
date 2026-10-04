# e-CERTIFY

A certificate issuance system for barangays. Staff look up a resident, pick a certificate, and print it with an automatic control number, while every action is recorded in an audit log.

**Current version:** 1.0.3 &nbsp;·&nbsp; Built with Laravel 12 &nbsp;·&nbsp; Developed by Mark Indayon

---

## Features

**Certificates**
- Barangay Clearance, Certificate of Residency, Certificate of Indigency
- First Time Job Seeker (certification and Oath of Undertaking, printed as a two-page set with a witness)
- Automatic control numbers, issuance history, and printable history reports
- Editable certificate bodies with placeholders such as `{name}`, `{age}`, `{purok}` and `{barangay}`, a live preview, and a "restore default" option
- One global header and footer, edited once in Settings, applied to every certificate

**Residents**
- Resident master file with search and filters for Purok, Gender and Civil Status
- Archive and restore, so a resident's document history is never lost

**Administration**
- Role-based access (Admin, Secretary, Staff)
- User management and a full audit log
- Barangay page that sets the barangay, municipality and province used across the whole system, so any barangay can use it

**Interface**
- White and blue theme, collapsible sidebar, dashboard with charts, expandable quick search
- Show/hide password buttons and a Remember Me option on the login page

## Who can do what

| Area | Staff | Secretary | Admin |
|---|:---:|:---:|:---:|
| Dashboard, resident list, add residents | ✅ | ✅ | ✅ |
| Issue certificates and view history | ✅ | ✅ | ✅ |
| Edit or archive residents, restore archive | | ✅ | ✅ |
| Certificate layouts, body editor, Settings & Assets | | ✅ | ✅ |
| System logs, Users, Barangay page | | | ✅ |

## Requirements

- PHP 8.2 or newer
- Composer
- MySQL or MariaDB (the Users page uses a MySQL function, so SQLite is not recommended)
- A web server or `php artisan serve` for local use

## Installation

```bash
git clone https://github.com/Markyca/e-CERTIFY.git
cd e-CERTIFY

composer install
cp .env.example .env
php artisan key:generate
```

Open `.env` and set your database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_certify
DB_USERNAME=root
DB_PASSWORD=
```

Create an empty database with that name, then:

```bash
php artisan migrate
php artisan storage:link
php artisan serve
```

Open http://127.0.0.1:8000 in your browser. The `storage:link` step is needed so uploaded logos, the QR code and the signature show up.

### Create the first Admin account

Accounts are created by an Admin inside the app, so the very first one has to be made by hand:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Your Name',
    'email' => 'you@example.com',
    'password' => 'choose-a-strong-password',
    'role' => 'Admin',
]);
```

The role must be written exactly as `Admin`, `Secretary` or `Staff`. After signing in, add everyone else from the **Users** page.

> The existing `UserSeeder` uses different role names and a sample password, so don't use it for a real installation.

## Setting up for your barangay

After the first login, as Admin:

1. Open **Barangay** and enter your barangay, municipality and province. This updates the login page, sidebar, certificate texts, header, footer address and printed reports.
2. Open **Certificates → Settings & Assets** and set the Punong Barangay name and title, the header lines, footer, and the LGU logo, barangay logo, QR code and signature images.
3. Open each certificate layout and press **Edit** to adjust the wording. On the First Time Job Seeker page you can also set the default witness.

## Updating

```bash
git pull
composer install
php artisan migrate
```

To change the version number shown in the app, edit `'version'` in `config/app.php`.

## Project layout

| Path | What it holds |
|---|---|
| `app/Http/Controllers` | Residents, certificates, templates, settings, users, logs, barangay |
| `app/Models` | `BarangaySetting` (identity, header/footer, witness) and `CertificateTemplate` (bodies and placeholders) |
| `resources/views` | Blade pages; `layouts/app.blade.php` holds the sidebar |
| `public/css/theme.css` | The blue and white theme |
| `routes/web.php` | All routes, grouped by role |

## Notes

- Never commit your `.env` file. It is already ignored by Git.
- Change any sample or default passwords before real use.
- Bootstrap, Bootstrap Icons, Chart.js and the Inter font load from a CDN, so the pages need an internet connection.

## Credits

System developed by **Mark Indayon**. Built on the [Laravel](https://laravel.com) framework.

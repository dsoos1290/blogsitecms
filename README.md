# BlogSite CMS

Lightweight blog and site CMS built on VC Framework for creating simple content-focused websites.

BlogSite CMS provides a minimal administration interface for managing posts and site settings while keeping the application small, easy to understand and easy to deploy.

It is built on the lightweight **V**iew-**C**ontroller architecture of [VC Framework](https://github.com/dsoos1290/vcframework) and uses MySQLi for database access. It does not require Composer or an external PHP framework and is compatible with PHP 5.3 and newer.

## Features

* Lightweight blog and site CMS
* Built on VC Framework
* Simple Controller-View architecture
* MySQLi database access
* No Composer required
* No external PHP framework dependencies
* PHP 5.3+ compatibility
* Simple administration interface
* Administrator login
* Administrator password change
* Create, edit and delete posts
* Enable or disable individual posts
* Optional post visibility in the main post list
* Optional post visibility in `sitemap.xml`
* Automatic created and modified timestamps
* Post ordering by creation or modification date
* Configurable number of posts per page
* Compact pagination
* Clean pagination URLs
* Configurable pagination slug
* Clean post URLs
* Optional configurable post URL slug
* Configurable post list style
* Optional post date and Continue/Back buttons
* Configurable Continue and Back button text
* Configurable PHP date format
* Configurable post display timezone
* Automatic daylight saving time handling with IANA timezones
* Configurable site title and description
* Configurable disclaimer and copyright text
* Configurable HTML language
* LTR and RTL text direction support
* Automatic XML sitemap
* Custom code injection before `</head>`
* Custom code injection before `</body>`
* Favicon upload support
* Favicon deletion
* Default favicon restoration
* Responsive public layout
* Responsive administration interface
* Development and production environments
* Optional absolute application URL support
* Apache URL rewriting
* Subdirectory installation support

## Usage

1. Download the latest version: https://github.com/dsoos1290/blogsitecms/releases/latest
2. Rename `private_html/app/config/db-sample.php` to `db.php`.
3. Open `db.php` and configure your database connection.
4. Import `install.sql` into the configured database.
5. Open `/admin` to access the administration interface.

## Configuration

Application-level configuration is available in:

`private_html/app/config/app.php`

Database configuration is stored in:

`private_html/app/config/db.php`

Most blog-specific settings can be changed directly from the administration interface, including the site title, description, language, text direction, pagination, URL slugs, date format, timezone, favicon and custom HTML code.

## Contact form

Existing installations must import `migrations/001_contact.sql` before using the
contact form or the admin Messages page. Apply your `DB_PREFIX` to table names in
the SQL if configured. New installations include the table in `install.sql`.

In Admin > Settings, configure the contact title, unique slug, optional description
below the form, sitemap visibility, footer link and sender receipt confirmation.
The form is disabled by default. Its title is also used in the footer and page heading.
Admin > Messages lists saved messages, sender addresses and email submission status.
Every message stores an empty `extra` TEXT field for future use.

Configure `APP_MAIL_API_KEY`, `APP_MAIL_FROM`, `APP_MAIL_FROM_NAME` and
`APP_MAIL_BASE_URL` in `private_html/app/config/app.php`. The base URL includes the
installation subdirectory and is used in emails. Set `APP_MAIL_TO` to receive admin
notifications; these include the sender as Reply-To. Sender confirmations are
controlled independently by the checkbox in Settings. A verified Resend sender
and the PHP cURL extension are required for sending emails. No SDK is needed.
Messages are saved before email is attempted; a failed email does not lose the message.
Email status `sent` means Resend accepted the request, not confirmed inbox delivery.

For optional invisible reCAPTCHA v2, configure both
`APP_GOOGLE_HIDDEN_RECAPTCHA_SITE_KEY` and
`APP_GOOGLE_HIDDEN_RECAPTCHA_SECRET_KEY` for your domain. Leave both empty to run
without CAPTCHA. A partial configuration rejects submissions. Verification uses
cURL and Google's official browser script, without a PHP library.
Submissions require a session CSRF token and are limited to one per minute per session.

API references: [Resend Send Email](https://resend.com/docs/api-reference/emails/send-email),
[Google invisible reCAPTCHA](https://developers.google.com/recaptcha/docs/invisible)
and [server verification](https://developers.google.com/recaptcha/docs/verify).

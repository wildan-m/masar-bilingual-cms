# Masar Engineering Consultants — website and CMS

The bilingual (English and Arabic) website of **Masar Engineering Consultants**, an engineering consultancy based in Abu Dhabi, together with the content management system the marketing team uses to run it and the intake desk the proposals team uses to handle incoming requests for proposal (RFPs).

Built with Laravel 13, Filament 5, Blade, Tailwind CSS 4 and Vite.

## Walkthrough

<!-- walkthrough -->

## Features

**Public website** (`/en/...` and `/ar/...`)

- Home, disciplines (index and detail), projects portfolio with a sector filter, news with categories, CMS pages (about, privacy, ...) and a contact page with the request-for-proposal form.
- Full right-to-left layout for Arabic, built with CSS logical properties and Tailwind `rtl:` variants, set in IBM Plex Sans Arabic (self-hosted).
- Language switcher that keeps the visitor on the same page, using each language's own URL slug.
- SEO: per-language meta title and description, canonical URLs, `hreflang` alternates (including `x-default`), `sitemap.xml` with alternates, Open Graph tags, generated `robots.txt` and an Organization JSON-LD block.

**CMS** (`/admin`)

- Pages, disciplines, projects, sectors, news articles, news categories and site settings, all edited in English and Arabic side by side.
- Translation status on every list, a filter for incomplete translations and a dashboard widget showing Arabic coverage.
- Requests for proposal: status workflow, assignee, internal notes, attachment download, CSV export.
- Users with three roles (administrator, content editor, RFP intake), enforced by policies.
- Read-only activity log of every change.

## How bilingual content works

### Storage

Each translatable attribute is a JSON column holding one value per language:

```json
{"en": "Structural engineering", "ar": "الهندسة الإنشائية"}
```

A model opts in with the `HasLocalizedAttributes` trait and lists its translatable attributes. The required list drives the completeness checks; SEO fields are optional because they fall back to the title and summary.

```php
class Project extends Model
{
    use HasLocalizedAttributes, HasLocalizedSlug;

    protected array $localized = ['title', 'slug', 'location', 'summary', 'body', 'meta_title', 'meta_description'];

    protected array $requiredTranslations = ['title', 'slug', 'location', 'summary', 'body'];
}

$project->title;                      // LocalizedText, prints the current language
$project->translate('title', 'ar');   // string, with fallback
$project->title->in('ar');            // exact Arabic value or null
$project->translationCompleteness('ar')->missing;   // ['summary', 'body']
Project::query()->missingTranslation('ar')->get();
```

The `AsLocalizedText` cast (in `app/Casts`) stores translations in a fixed key order and compares decoded values, so databases that normalise JSON (MySQL, PostgreSQL) never make an unchanged record look modified. No third-party translation package is involved.

### Fallback rules

| Situation | What visitors see | What editors see |
| --- | --- | --- |
| Arabic translation present | Arabic | Complete badge |
| Arabic missing, English present | The English text, wrapped in `lang="en" dir="ltr"`; rich text also gets a short notice in Arabic | Warning badge on the record ("3 of 5"), a callout on the edit form, a count on the Arabic tab and a hint on each untranslated field |
| Arabic slug missing | The page is served at `/ar/projects/{english-slug}` | Same as above |
| A slug from the other language in the URL | Permanent redirect to the canonical URL of the current language | — |

Text is never rendered blank while any translation exists: missing values fall back to English first and then to any other available language.

### Editing

`App\Filament\Support\LocaleTabs` renders one tab per language for any form. The caller describes the fields of one language and the helper:

- sets `dir="ltr" lang="en"` or `dir="rtl" lang="ar"` on every input, textarea and rich editor;
- marks untranslated fields and shows the number of missing translations on the Arabic tab;
- keeps English required and lets Arabic follow later.

```php
LocaleTabs::make(fn (Locale $locale): array => [
    LocalizedFields::title($locale),                 // fills the slug of the same language
    LocalizedFields::slug($locale, 'projects/'),     // unique per language, Arabic script allowed
    LocalizedFields::textarea($locale, 'summary', 'Summary')->required($locale->isFallback()),
    LocalizedFields::richText($locale)->required($locale->isFallback()),
]);
```

`TranslationStatusColumn`, `MissingTranslationFilter`, `LocalizedTextColumn` and `TranslationCallout` complete the toolkit and are reused by every content resource.

### URLs and language selection

- Every public URL starts with the language: `/en/projects/corniche-residences-tower` and `/ar/projects/برج-كورنيش-ريزيدنسز`.
- `/` redirects to the language the visitor chose before (cookie), otherwise to the best match of the browser's `Accept-Language` header, otherwise to English. The redirect varies on both headers.
- The `SetLocale` middleware applies the language, sets `URL::defaults()` and is prioritised before rate limiting, so even "too many requests" pages are localised.
- Arabic slugs keep the Arabic script; Laravel percent-encodes them in links and browsers display them decoded.
- Phone numbers typed with Arabic-Indic digits (`٠٥٠`) are normalised before validation.

## Roles and permissions

Roles are an enum on the user (`App\Enums\Role`) that maps to fine-grained permissions (`App\Enums\Permission`). Policies check permissions, never role names, so changing what a role may do happens in one place. The Filament panel runs in strict authorization mode: a resource whose policy lacks a method fails loudly instead of silently allowing access.

| Capability | Administrator | Content editor | RFP intake |
| --- | :---: | :---: | :---: |
| Edit pages, disciplines, projects, sectors and news | ✓ | ✓ | |
| Edit site settings | ✓ | ✓ | |
| View requests for proposal | ✓ | | ✓ |
| Assign, annotate and move RFPs through the workflow | ✓ | | ✓ |
| Download RFP attachments | ✓ | | ✓ |
| Export RFPs to CSV | ✓ | | ✓ |
| Delete RFPs | ✓ | | |
| Manage users and roles | ✓ | | |
| View the activity log | ✓ | | |

Screens a role may not use are hidden from its navigation and return 403 when opened directly. Deactivated users cannot sign in, administrators cannot delete their own account, and system pages (about, contact, privacy) can be unpublished but not deleted.

## Requests for proposal

1. **Form.** The contact page carries the RFP form: name, company, email, phone, sector, project location, project description and an optional attachment (PDF, Word `.docx` or Excel `.xlsx`, up to 10 MB). Validation messages are shown in the visitor's language.
2. **Spam protection.** A hidden honeypot field silently discards bot submissions (they receive the normal success redirect), and a rate limiter allows five submissions per minute per IP address.
3. **Storage.** The attachment is written to the private `private` disk (`storage/app/private/uploads`) under a random 40-character name; the original file name is kept for display only. If saving the record fails, the uploaded file is removed.
4. **Notification.** Active intake staff (or administrators, if there are none) receive a queued email with the reference number and a signed, expiring link to the record. The link still requires signing in and the `view` permission; the email does not include the message or the attachment.
5. **Workflow.** New → In review → Proposal sent → Closed. A sent proposal can go back to review and a closed request can be reopened. Transitions are enforced by the model (`ProposalRequest::transitionTo()`), shown as actions only when allowed, and logged.
6. **Handling.** Staff assign requests (or take them with "Assign to me"), keep internal notes and filter by status, sector, assignee and attachment. List tabs show the count per status, the navigation badge shows the number of new requests and the dashboard flags open requests nobody has picked up.
7. **Export.** CSV export of the filtered list or of selected rows. The file starts with a UTF-8 byte order mark so spreadsheets display Arabic correctly, and cells starting with `=`, `+`, `-` or `@` are neutralised against formula injection. Every export is logged.
8. **Attachments.** Files are only ever streamed by `ProposalRequestAttachmentController` after the `downloadAttachment` policy check, with `Content-Disposition: attachment`, `nosniff` and `no-store` headers. Every download is logged, and deleting a request deletes its file. There is no public URL for an attachment. In production, point `RFP_ATTACHMENT_DISK` at a private S3 bucket.

## Activity log

The `LogsActivity` trait records `created`, `updated` and `deleted` events for every CMS model, with the user, the record and the changed attributes (old and new values; translations are stored per language). RFPs add `status_changed`, `attachment_downloaded` and `exported` events.

Personal and secret fields (passwords, client contact details, messages, internal notes, file paths) are never stored: the entry only notes that they changed. Bookkeeping fields such as timestamps are ignored, and saving a record without changes writes nothing. Entries cannot be modified, are browsable by administrators only and are pruned after 24 months by the scheduled `model:prune` command.

## Architecture notes

```
app/
  Actions/                      SubmitProposalRequest (store upload, create record, notify)
  Casts/AsLocalizedText.php     JSON translations <-> LocalizedText
  Enums/                        Role, Permission, ProposalRequestStatus, ...
  Exports/                      Streaming CSV export
  Filament/
    Support/                    LocaleTabs, LocalizedFields, translation column, filter and callout
    Resources/                  One folder per resource (schemas, tables, pages)
    Pages/ManageSiteSettings    Site settings form
    Widgets/                    RFP figures and translation coverage
  Http/
    Controllers/Site/           Public pages, one controller per section
    Controllers/Admin/          Attachment download and signed email link
    Middleware/                 SetLocale, BlockHoneypotSubmissions, SecurityHeaders
  Models/Concerns/              HasLocalizedAttributes, HasLocalizedSlug, HasSeoFields, LogsActivity, ...
  Policies/                     ContentPolicy base class plus one policy per model
  Support/
    Localization/               Locale enum, LocalizedText, slugs, alternate URLs, dates
    Seo/                        SeoData for the <head>, SitemapBuilder
    Settings/SiteSettings.php   Cached key-value settings with typed access
lang/{en,ar}/                   Interface strings and Arabic validation messages
resources/views/                Blade layout, components and pages of the public site
```

- **Rich text** written in the CMS is sanitised with Symfony's HTML sanitizer before it is rendered on the public site.
- **Fonts** (IBM Plex Sans and IBM Plex Sans Arabic) are downloaded from fonts.bunny.net at build time by the Laravel Vite plugin and served from the site itself. English pages load the Latin face only; Arabic pages add the Arabic face.
- **Site settings** live in a key-value table, are cached as a whole and are invalidated on save.
- **Security headers** (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`) are added to every web response.
- **Error pages** (403, 404, 419, 429) use the site layout in the visitor's language; 500 and 503 use a standalone page that renders without the database.
- **Polymorphic types** use a morph map (`project`, `proposal_request`, ...), so stored activity entries do not depend on class names.
- **Time zones:** timestamps are stored in UTC and displayed in Asia/Dubai (`APP_DISPLAY_TIMEZONE`).

## Local development

Requirements: PHP 8.3 or newer with the `gd`, `intl`, `pdo_sqlite`, `zip` and `fileinfo` extensions, Composer 2, and Node.js 20.19 or newer with npm.

```bash
composer run setup
```

The setup script installs PHP and JavaScript dependencies, creates `.env` and the application key, creates the SQLite database, runs the migrations, seeds the content, links the public storage folder and builds the front-end assets. Running it again keeps existing content.

Start the application:

```bash
php artisan serve --port=8099
```

Then open http://127.0.0.1:8099 for the website and http://127.0.0.1:8099/admin for the CMS. Run `php artisan queue:work` in a second terminal to deliver the RFP notification emails, or use `composer run dev` to start the server, queue worker, log viewer and Vite together. Emails are written to `storage/logs/laravel.log` (`MAIL_MAILER=log`).

Local accounts created by the seeder (for local development only, never in production):

| Role | Email | Password |
| --- | --- | --- |
| Administrator | admin@masar.test | `Admin#Local2026` |
| Content editor | editor@masar.test | `Editor#Local2026` |
| RFP intake | intake@masar.test | `Intake#Local2026` |

The seeded content includes six disciplines, nine projects, six news articles, three pages, site settings and six requests for proposal at different workflow stages (two with attachments). One project and one news article are deliberately not fully translated, so the fallback behaviour and the translation flags are visible straight away.

To start again from a clean database: `php artisan migrate:fresh --seed`.

To accept 10 MB attachments locally, PHP's own limits must allow them: set `upload_max_filesize = 12M` and `post_max_size = 16M` in the `php.ini` reported by `php --ini`.

## Tests

```bash
php artisan test
```

The feature tests run against an in-memory SQLite database and cover the locale redirect and `<html lang dir>`, the language switcher and canonical redirects, the fallback behaviour of localised attributes, the bilingual Filament forms (RTL inputs, required English, unique slugs per language, completeness badges), RFP validation in both languages, private attachment storage, the honeypot, rate limiting, the signed email link, the status workflow, the CSV export, the policy matrix for all three roles, the activity log and the sitemap's `hreflang` alternates.

## Production notes

- Set `APP_ENV=production`, `APP_DEBUG=false`, a real mail transport and a database such as MySQL or PostgreSQL.
- Run a queue worker for notification emails and `php artisan schedule:run` every minute (activity log pruning).
- Point `RFP_ATTACHMENT_DISK` at a private bucket and keep `upload_max_filesize` / `post_max_size` above 10 MB.
- Deploy with `composer install --no-dev`, `npm ci && npm run build`, `php artisan migrate --force` and `php artisan optimize`.

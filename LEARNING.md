# Laravel Learning Journal: KSG Innovation Portal

A companion learning guide for developers transitioning from C#/.NET to PHP Laravel.

---

## Step 0: Environment Assessment & Project Blueprint

### Concepts Introduced

| Laravel / PHP Concept | .NET / C# Equivalent | Purpose & Notes |
| :--- | :--- | :--- |
| **Composer** | **NuGet** + `dotnet CLI` | Dependency manager for PHP. Manages packages in `composer.json` and locks versions in `composer.lock` (similar to `.csproj` package references). |
| **Artisan CLI** | `dotnet CLI` (e.g., `dotnet ef`, `dotnet new`) | The command-line interface included with Laravel providing helpful commands for code generation, migrations, caching, and serving. |
| **Blade** | **Razor (`.cshtml`)** | Laravel's templating engine. Uses lightweight directives like `@if`, `@foreach`, `@auth`, and component tags like `<x-layout>`. |
| **Eloquent ORM** | **Entity Framework Core (EF Core)** | Active Record ORM implementation where a Model class directly represents a database table record and encapsulates query logic and relations. |
| **Migrations** | **EF Core Migrations** | Version control for your database schema. Allows defining schema changes in PHP code that can be run or rolled back programmatically. |
| **Form Requests** | **FluentValidation / Action Filters / DTO validation** | Dedicated request classes (`app/Http/Requests`) that encapsulate validation rules and authorization checks before a controller action executes. |
| **Policies** | **Custom Authorization Handlers / Resource-based Policies** | Classes that organize authorization logic around a specific model or resource (e.g., `InnovationPolicy` determining who can update or submit an innovation). |
| **Service Layer / Actions** | **Application Services / MediatR Handlers** | Domain classes to keep controllers lean. Controllers only orchestrate HTTP requests and responses, while business rules live in services/actions. |

---

### Key Takeaways from Today
- **Clean Architecture Principle**: Controllers in Laravel should remain ultra-thin (HTTP input $\rightarrow$ Form Request validation $\rightarrow$ Service invocation $\rightarrow$ Return Blade View / Redirect).
- **Security by Default**: CSRF protection, SQL injection prevention (via PDO parameter binding), and XSS prevention (via Blade `{{ }}`) are built into the framework out of the box.

---

## Step 1: Environment Provisioning (Completed)

### Installed & Configured Stack
- **PHP**: 8.3.37 (CLI with `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd`, `zip` extensions enabled)
- **Composer**: 2.11 (PHP dependency manager, configured in PATH)
- **Node.js & npm**: Node v26.13.1, npm 11.20.0 (Vite asset compilation)
- **Database**: MariaDB / MySQL running on port 3306 (database `ksg_innovation_portal` created)

### What We Did & Why
1. **Configured PHP Extensions**: In .NET, libraries are managed as NuGet assemblies referenced in your project. In PHP, native core capabilities like database drivers (`pdo_mysql`) and cryptographic hashing (`openssl`) are compiled C extensions that must be enabled in `php.ini`.
2. **Composer**: Configured as the global dependency manager (comparable to NuGet). It reads `composer.json` to resolve and download vendor packages into `vendor/`.
3. **Database Setup**: Initialized and verified our MySQL/MariaDB database `ksg_innovation_portal` with UTF-8 (`utf8mb4_unicode_ci`) character encoding to ensure smooth multi-language text and emoji support.

---

## Step 2: Application Scaffolding, Auth & Role Foundations (Completed)

### What Was Built
1. **Scaffolded Laravel 11 Project**: Created application skeleton with standard PSR-4 directory structure.
2. **Configured Database Environment**: Switched `.env` from SQLite to MySQL on port 3306 pointing to `ksg_innovation_portal`.
3. **Installed Laravel Breeze (Blade Stack)**: Complete user authentication system (login, registration, password reset, profile management) with Tailwind CSS styling and Vite asset compilation.
4. **Installed Spatie Laravel-Permission**: Created permission schema (`roles`, `permissions`, `model_has_roles`, etc.) and attached `HasRoles` trait to `App\Models\User`.
5. **Database Migration for KSG User Profile**: Added `department` and `phone` columns to `users` table via migration.
6. **Role & Permission Seeder (`RoleAndPermissionSeeder`)**:
   - Roles created: `innovator`, `reviewer`, `admin`.
   - Seeded demo accounts:
     - Admin: `admin@ksg.ac.ke` (password: `password`)
     - Reviewer: `reviewer@ksg.ac.ke` (password: `password`)
     - Innovator: `innovator@ksg.ac.ke` (password: `password`)
7. **Verified Test Suite**: All 25 default auth tests ran and passed (61 assertions).

### Laravel Concepts vs. .NET Equivalents

| Laravel Concept | .NET Equivalent | Practical Explanation |
| :--- | :--- | :--- |
| **Laravel Breeze** | **ASP.NET Core Identity UI** (scaffolded Identity) | Scaffolds authentication controllers, Blade templates, and routes directly into your codebase so you have full ownership and customization control over them. |
| **`php artisan migrate`** | **`dotnet ef database update`** | Applies outstanding migration files in sequential chronological order to the configured database. |
| **`database/seeders`** | **`ModelBuilder.HasData()` / Custom DbInitializer** | Classes that populate initial lookup records, admin users, or sample test data. Run via `php artisan db:seed`. |
| **Spatie `HasRoles` Trait** | **C# Extension Methods / Mixin / ASP.NET Identity `UserStore<T>`** | PHP Traits allow horizontal reuse of methods in multiple classes without multiple inheritance. Using `use HasRoles;` in `User.php` immediately equips the model with `$user->hasRole('admin')`, `$user->can('review innovations')`, and `$user->assignRole('reviewer')`. |
| **Environment Configuration (`.env`)** | **`appsettings.json` + `appsettings.Development.json`** | Stores machine-specific environment variables. Accessed in Laravel via `env('KEY')` inside `config/*.php` files, and consumed in application code via `config('app.name')`. |

---

## Step 3: Domain Entity Architecture & Relational Migrations (Completed)

### What Was Built
1. **`InnovationStatus` PHP Enum (`app/Enums/InnovationStatus.php`)**:
   - Represents the complete state machine: `draft`, `submitted`, `under_review`, `approved`, `rejected`, `revision_requested`, `resubmitted`.
   - Includes UI presentation helpers (`badgeClass()`, `label()`) and policy checks (`isEditable()`, `isReviewable()`).
2. **Database Migrations & Eloquent Models**:
   - `innovations`: Main idea records with author foreign key, slug, description, enum status, and SoftDeletes.
   - `innovation_attachments`: File metadata (path, size, mime type, uploader).
   - `innovation_user`: Pivot table managing co-innovator collaborator permissions.
   - `reviews`: Evaluation records storing rating (1–5), formal decision, and feedback.
   - `reviewer_comments`: Real-time inter-reviewer discussion trail.
   - `innovation_status_histories`: Non-destructive state machine audit logs.
3. **Eloquent Relationships**:
   - Model mappings wired across `Innovation`, `User`, `InnovationAttachment`, `Review`, `ReviewerComment`, and `InnovationStatusHistory`.
4. **Lifecycle Hooks & Auto-slugging**:
   - Registered `creating` model event on `Innovation` to automatically generate unique, URL-safe slugs from titles.
5. **Testing & Seed Data**:
   - Built `InnovationFactory` for test generation.
   - Verified functionality with `InnovationModelTest` (all 27 application tests passing).
   - Seeded sample KSG innovations with `InnovationSeeder`.

### Laravel Concepts vs. .NET Equivalents

| Laravel Concept | .NET / EF Core Equivalent | Practical Explanation |
| :--- | :--- | :--- |
| **Backed Enums (`enum Status: string`) + `$casts`** | **C# `enum` + `HasConversion<string>()`** | PHP 8.1+ backed enums are strongly typed. By adding `'status' => InnovationStatus::class` to `$casts`, Eloquent automatically turns database strings into enum instances when reading and serializes them when saving. |
| **Eloquent Relationships (`hasMany`, `belongsTo`, `belongsToMany`)** | **EF Core Navigation Properties (`ICollection<T>`, `virtual Parent`)** | In EF Core you declare explicit property getters/setters and configure fluent API in `OnModelCreating`. In Eloquent, you define methods that return relationship objects. `$innovation->author` returns the model, while `$innovation->author()` returns the query builder. |
| **Pivot Tables (`belongsToMany` with `withPivot()`)** | **Many-to-Many Join Entity (`InnovationUser` with composite key)** | In Laravel, `belongsToMany` provides methods like `$innovation->collaborators()->attach($userId, ['role' => 'Lead'])`, `$innovation->collaborators()->detach($userId)`, and `$innovation->collaborators()->sync([...])` without writing manual join queries. |
| **Model Lifecycle Events (`booted()` / `creating`)** | **EF Core `SaveChangesInterceptor` / Domain Events** | Allows hooking into entity persistence events. In `Innovation::booted()`, the `creating` callback automatically inspects and generates a unique slug before SQL `INSERT` is executed. |
| **Soft Deletes (`use SoftDeletes;`)** | **EF Core Global Query Filters (`IsDeleted == false`)** | When enabled, `$innovation->delete()` sets `deleted_at` timestamp rather than removing the row. Queries automatically exclude soft-deleted rows unless explicitly requested via `Innovation::withTrashed()->get()`. |
| **Storage Symlink (`php artisan storage:link`)** | **`app.UseStaticFiles()` pointing to physical path** | In Laravel, uploaded files are safely stored in private `storage/app/public/`. Running `storage:link` creates a Windows symbolic link from `public/storage` to `storage/app/public`, making files accessible to web browsers. |

---

## Step 4: Innovator Workflow, Policies & Lean Architecture (Completed)

### What Was Built
1. **Authorization Policy (`app/Policies/InnovationPolicy.php`)**:
   - `create`: requires permission or `innovator`/`admin` role.
   - `view`: author, collaborator, reviewer, or admin.
   - `update`: author or collaborator, and only when status allows editing (`draft` or `revision_requested`).
   - `delete`: author only, and only when still in `draft`.
   - `submit`: author or collaborator when status is `draft` or `revision_requested`.
   - `deleteAttachment`: author or collaborator while editable.
2. **Dedicated Service Layer (`app/Services/InnovationService.php`)**:
   - `create()`: atomizes innovation insertion, file attachment uploads to `storage/app/public/innovations/{id}`, collaborator associations, and audit trail in `InnovationStatusHistory` using `DB::transaction()`.
   - `update()`: updates fields, adds new attachments, and synchronizes collaborators.
   - `submit()`: moves status from `draft` $\rightarrow$ `submitted` or `revision_requested` $\rightarrow$ `resubmitted`, sets `submitted_at`, and appends an audit record.
   - `deleteAttachment()`: deletes the physical file from disk and removes the database record.
3. **Form Request Classes**:
   - `StoreInnovationRequest`: Validates title (min 5, max 255), description (min 20), multi-attachments (allowed MIME types, max 10MB per file), and collaborator roles.
   - `UpdateInnovationRequest`: Enforces the same rules and checks policy authorization during editing.
4. **Lean Controller (`app/Http/Controllers/InnovationController.php`)**:
   - Zero business logic in controller actions. Pure HTTP orchestration using dependency injection and `Gate::authorize()`.
5. **Tailwind Blade Views**:
   - `resources/views/innovations/index.blade.php`: Portal dashboard with status filter pills, search, count summaries, and quick action buttons.
   - `resources/views/innovations/create.blade.php`: Proposal creation form with multi-file drag/drop zone and dynamic Alpine.js collaborator picker.
   - `resources/views/innovations/edit.blade.php`: Edit form with existing attachment management (delete file buttons) and new file upload.
   - `resources/views/innovations/show.blade.php`: Comprehensive detail page displaying proposal, attachments with download links, collaborator list, reviewer evaluation feedback cards, and lifecycle audit timeline.
6. **Feature Test Suite (`tests/Feature/InnovationTest.php`)**:
   - 6 automated tests covering guest redirect, dashboard view, draft creation, file storage assertions, validation rule failures, submission transitions, and unauthorized user 403 Forbidden checks.
   - Total test suite: **33 tests, 92 assertions passing**.

### Laravel Concepts vs. .NET Equivalents

| Laravel Concept | .NET Equivalent | Practical Explanation |
| :--- | :--- | :--- |
| **Form Requests (`StoreInnovationRequest`)** | **`IValidator<T>` (FluentValidation) + Action Filter** | Form Requests run *before* the controller action is even invoked. If validation fails, Laravel automatically catches it and redirects back to the form with `$errors` and `old()` input data, keeping your controller clean and free of validation checks. |
| **Route Model Binding (`show(Innovation $innovation)`)** | **ASP.NET Core Model Binding with `[FromRoute]` + `DbContext.FindAsync(id)`** | When a controller action type-hints an Eloquent model and the route parameter name matches (`{innovation}`), Laravel automatically queries the database by primary key or slug. If not found, it automatically throws a 404 HTTP exception without any manual queries. |
| **Policies & `Gate::authorize()`** | **`IAuthorizationService.AuthorizeAsync()` / Resource-based Authorization** | Policies organize authorization around a specific model. Calling `Gate::authorize('update', $innovation)` evaluates `InnovationPolicy::update()`. If false, Laravel immediately aborts with a 403 Forbidden response. |
| **`DB::transaction()`** | **`using var transaction = _context.Database.BeginTransaction()`** | Wraps multiple database calls into a single atomic database transaction. If an exception occurs inside the closure, changes are automatically rolled back. |
| **`Storage::disk('public')->assertExists(...)`** | **Mocking `IFileStorageService` with Moq** | Laravel includes a built-in virtual file system test harness (`Storage::fake('public')`). In feature tests, you can test realistic file uploads without actually writing files to physical disk or polluting your hard drive. |

---

## Step 5: Reviewer Dashboard, Rating System & Decision State Machine (Completed)

### What Was Built
1. **Dedicated Review Service (`app/Services/ReviewService.php`)**:
   - `startReview()`: Seamlessly claims/advances an incoming submission from `submitted` $\rightarrow$ `under_review` upon first evaluation open, writing an audit log.
   - `postComment()`: Enables inter-reviewer dialogue on an innovation.
   - `submitReview()`: Atomically commits the reviewer's rating (1–5 scale), formal decision (`approved`, `rejected`, `revision_requested`), feedback returned to the innovator, and confidential internal notes, triggering the state machine transition in an atomic `DB::transaction()`.
2. **Review Form Requests**:
   - `StoreReviewRequest`: Enforces numeric score between 1 and 5, allowed decision enum strings, and minimum feedback length (10 characters).
   - `StoreReviewerCommentRequest`: Validates internal discussion comments.
3. **Middleware Registration in `bootstrap/app.php`**:
   - Registered Spatie's `role`, `permission`, and `role_or_permission` aliases into the application middleware pipeline.
4. **Reviewer Controller (`app/Http/Controllers/ReviewerDashboardController.php`)**:
   - Thin controller handling queue metrics, tabbed filtering (`queue`, `in_review`, `decided`, `all`), proposal evaluation, discussion notes, and decision submission.
5. **Reviewer Blade Templates**:
   - `resources/views/reviewer/index.blade.php`: Reviewer portal with queue metric cards, submission metadata, and direct evaluate links.
   - `resources/views/reviewer/evaluate.blade.php`: Side-by-side workspace with full proposal details and attachments on the left, and inter-reviewer discussion board plus decision/rating form on the right.
6. **Navigation Integration**:
   - Reviewer Dashboard link conditionally displayed in top navigation bar for users with `reviewer` or `admin` roles.
7. **Automated Feature Test Suite (`tests/Feature/ReviewerWorkflowTest.php`)**:
   - 6 test cases verifying role authorization gates, automatic `under_review` state transition, comment posting, review scoring, confidential notes isolation, and the complete revise-and-resubmit lifecycle.
   - Application total: **39 tests, 113 assertions passing**.

### Laravel Concepts vs. .NET Equivalents

| Laravel Concept | .NET Equivalent | Practical Explanation |
| :--- | :--- | :--- |
| **Spatie Middleware (`role_or_permission`)** | **`[Authorize(Roles = "...")]` / Custom Policy** | In ASP.NET you decorate controllers/actions with `[Authorize]`. In Laravel, you can apply route middleware like `middleware('role_or_permission:reviewer|admin')` directly in `routes/web.php` to protect entire route groups. |
| **Middleware Aliases in `bootstrap/app.php`** | **`builder.Services.AddAuthorization()` in `Program.cs`** | Laravel 11 uses a single `bootstrap/app.php` configuration function where middleware aliases are mapped, replacing the older `Kernel.php` file from previous Laravel versions. |
| **State Machine Pattern** | **Workflow State Machine / Aggregate Root Events** | By pairing backed PHP Enums (`InnovationStatus`) with `InnovationStatusHistory` logs, each status change is non-destructive, audited, and strictly validated. |
| **Data Privacy & Scope Isolation** | **DTO Projection / AutoMapper mapping** | In `innovations.show`, internal reviewer notes are excluded from the author's view, while in `reviewer.evaluate` they are rendered for the evaluation committee. |

---

## Step 6: Email Notifications & One-Time Passcode (OTP) System (Completed)

### What Was Built
1. **SMTP Transport Configuration**:
   - Configured `.env` and `config/mail.php` with Gmail SMTP credentials on port 587 using TLS (`MAIL_ENCRYPTION=tls`).
   - Verified live end-to-end transport delivery.
2. **Multi-Channel Notification Architecture**:
   - **`InnovationSubmittedNotification`** (`app/Notifications/InnovationSubmittedNotification.php`):
     - Dispatched automatically upon innovation submission/resubmission to all reviewers and administrators.
     - Includes proposal title, lead author, department, attachment count, and a direct CTA link to the Reviewer Evaluation workspace.
   - **`InnovationReviewedNotification`** (`app/Notifications/InnovationReviewedNotification.php`):
     - Dispatched automatically upon decision submission to the primary author and all co-innovators.
     - Dynamically formats subject line and email body based on decision (`Approved`, `Revision Requested`, `Rejected`).
     - Includes committee score (1–5 stars) and reviewer feedback.
   - **`SendOtpNotification`** (`app/Notifications/SendOtpNotification.php`):
     - Dispatches a 6-digit numeric passcode with 10-minute expiry for account security and two-factor verification.
3. **Domain Service Integration (Clean Architecture)**:
   - `InnovationService::submit()`: Dispatches `InnovationSubmittedNotification` to evaluation committee members.
   - `ReviewService::submitReview()`: Dispatches `InnovationReviewedNotification` to innovator and collaborators.
4. **One-Time Passcode (OTP) Feature**:
   - Migration: Added `otp_code` and `otp_expires_at` to `users` table.
   - `OtpService` (`app/Services/OtpService.php`): Generates cryptographically secure 6-digit passcodes (`random_int`) and handles safe constant-time comparison (`hash_equals`).
   - `OtpController` (`app/Http/Controllers/OtpController.php`): Form validation and resend endpoints.
   - `resources/views/auth/otp-verify.blade.php`: Passcode verification interface.
   - Profile Integration: Added OTP testing card inside User Profile (`resources/views/profile/edit.blade.php`).
5. **Automated Feature Test Suite (`tests/Feature/NotificationAndOtpTest.php`)**:
   - 5 automated tests verifying notification dispatching using `Notification::fake()`, reviewer alerts, author/collaborator review outcome alerts, and OTP generation/verification cycles.
   - Complete application test suite: **44 tests, 129 assertions passing**.

### Laravel Concepts vs. .NET Equivalents

| Laravel Concept | .NET Equivalent | Practical Explanation |
| :--- | :--- | :--- |
| **Notifications (`Illuminate\Notifications\Notification`)** | **`IEmailSender` + MediatR Notification Handlers** | Unlike basic email classes, Laravel Notifications are multi-channel. Calling `$user->notify(new InnovationReviewedNotification($innovation, $review))` sends an email via `'mail'` today, and can add SMS (`'vonage'`) or database alerts (`'database'`) simply by adding them to `via()`. |
| **Fluent `MailMessage` Builder** | **`BodyBuilder` (MailKit / MimeKit)** | Provides a responsive, standardized HTML template automatically: `->greeting()`, `->line()`, and `->action('Button Label', $url)`. You get clean, branded transactional emails without having to write raw HTML table layouts. |
| **`Notification::fake()`** | **Mocking `IEmailSender` with Moq** | A built-in test double. When called in a test, Laravel suppresses actual outgoing SMTP network calls and records all dispatched notifications in memory, allowing assertions like `Notification::assertSentTo($reviewer, InnovationSubmittedNotification::class)`. |
| **Timing-Attack Resistance (`hash_equals`)** | **`CryptographicOperations.FixedTimeEquals()`** | In `OtpService::verify`, user-provided passcodes are compared using `hash_equals` rather than `===` to prevent side-channel timing attacks when validating security codes. |

---

## Step 7: Hero Slider, Staggered Text Flow & Institutional Homepage (Completed)

### What Was Built
1. **Dynamic Hero Slider Component** ([public/css/hero-slider.css](file:///c:/Users/Admin/Documents/Innovation%20portal/public/css/hero-slider.css) & [public/js/hero-slider.js](file:///c:/Users/Admin/Documents/Innovation%20portal/public/js/hero-slider.js)):
   - Multi-slide cross-fading container (`clamp(520px, 80vh, 760px)`) with dark contrast gradient overlay.
   - **Staggered Text Flow**: Using CSS custom properties (`--i: 0`, `--i: 1`, `--i: 2`, `--i: 3`) and cubic-bezier easing to orchestrate animated entrances for the Eyebrow kicker, Headline, Description, and CTA buttons on slide transition.
   - Interactive features: Circular next/prev arrows, animated indicator dots, autoplay with pause-on-hover / focus, touch swipe support, and keyboard arrow controls.
2. **Homepage Layout Structure** ([resources/views/welcome.blade.php](file:///c:/Users/Admin/Documents/Innovation%20portal/resources/views/welcome.blade.php)):
   - **Top Navigation Bar**: KSG monogram logo, responsive nav links, and dynamic Auth state buttons (Sign In / Register for guests; Dashboard & Reviewer shortcuts for logged-in users).
   - **Key Impact Metrics Bar**: Real-time counts (Total Innovations, In Active Evaluation, Approved for Adoption, Registered Public Officers) query-bound from MySQL.
   - **How The Pipeline Works**: 4-stage sequential cards (1. Draft & Attach $\rightarrow$ 2. Add Collaborators $\rightarrow$ 3. Committee Review $\rightarrow$ 4. Approval & Adoption).
   - **Live Innovations Showcase**: Cards rendering real submissions pulled from the database with status badges and details.
   - **Officer & Faculty Call to Action Banner**: Highlighted CTA inviting public officers across Kenya to propose solutions.
   - **Institutional Footer**: KSG campuses (Lower Kabete, Mombasa, Embu, Matuga, Baringo), governance links, and contact information.
3. **Asset Organization**:
   - Ready-to-swap image directory at `public/images/slider/` (`slide1.svg`, `slide2.svg`, `slide3.svg`).
4. **Automated Verification**:
   - Full test suite passing (**44 tests, 129 assertions**).

### Laravel Concepts vs. .NET Equivalents

| Laravel Concept | .NET Equivalent | Practical Explanation |
| :--- | :--- | :--- |
| **`asset('path')` Helper** | **`Url.Content("~/path")` / Static Web Assets** | Generates a fully qualified URL for assets in the `public/` directory, taking into account HTTPS and subdirectories automatically. |
| **Blade Route Injection (`route('name')`)** | **`Url.Action("Action", "Controller")` / Tag Helpers (`asp-action`)** | Named routes decouple your HTML templates from physical URL patterns. If you change `/innovations/create` to `/submit-idea`, you only update the route definition, and all Blade `route(...)` links update automatically. |
| **View Data Binding (`compact('stats', 'recentInnovations')`)** | **`View(viewModel)` / Controller ViewModel** | In ASP.NET you pass a strongly-typed ViewModel class. In Laravel, you can pass associative arrays or use PHP's `compact('var1', 'var2')` which automatically maps variables into view scope. |
| **Vite Asset Compilation & JIT Purging** | **ASP.NET Core WebOptimizer / Bundler & Minifier** | Tailwind CSS operates with Just-In-Time (JIT) compilation. Only utility classes detected in your scanned Blade templates during `npm run build` or `npm run dev` are included in the output CSS. Adding custom inline fallback gradients prevents washed-out rendering if styles are purged. |

---

## Step 9: Institutional Scope Enforcement (KSG Staff-Only) & Local Dev Workflow (Completed)

### What Was Built
1. **Scope Refinement Across Homepage Content**:
   - Updated all copy across the landing page, hero slides, metrics bar, and call-to-action sections to strictly reflect that the portal is exclusive to **KSG staff, faculty, researchers, and administrative personnel** across its 5 campuses (Lower Kabete, Mombasa, Embu, Matuga, Baringo).
   - Metrics bar updated to display "Registered KSG Staff & Faculty".
   - Step 2 collaboration step changed to "fellow KSG faculty, campus colleagues, or department officers".
   - Step 4 adoption step changed to "pilot deployment across KSG directorates and training programs".
   - Recent showcase header updated to "Latest KSG Staff Innovations".
   - Call to action banner refined to "Institutional Innovation Call" targeting KSG faculty and staff.
2. **Local Development Workflow & Working Directory**:
   - Documented the required working directory command `cd "c:\Users\Admin\Documents\Innovation portal"` before running `php artisan serve` to avoid `Could not open input file: artisan` error when in `C:\Windows\System32`.
3. **Recompiled Assets & Tested**:
   - Re-compiled Tailwind bundle via `npm run build`.
   - Verified test suite passes (**44 passed, 129 assertions**).

### Laravel Concepts vs. .NET Equivalents

| Laravel Concept | .NET Equivalent | Practical Explanation |
| :--- | :--- | :--- |
| **`php artisan serve` vs. Working Directory** | **`dotnet run` in `.csproj` directory** | Just as `dotnet run` must be executed in the directory containing the `.csproj` file (or pointing to `--project`), `php artisan` commands require the terminal working directory to be the application root where the `artisan` PHP bootstrapper resides. |
| **Strict Authorization Scope in Views** | **View-level Authorization (`@inject IAuthorizationService`)** | Ensuring UI messaging, copy, and forms reflect specific audience scopes prevents unauthorized submissions early at the UX level before backend Policies (`InnovationPolicy`) enforce access controls. |

---

## Step 10: Official KSG Logo Integration Across Layouts & Components (Completed)

### What Was Built
1. **Logo Asset Handling**:
   - Detected `ksg logo.png` (866×288 high-res with Kenya Coat of Arms, national flag accents, and KSG emblem).
   - Created clean URL-safe alias `ksg-logo.png` in `public/images/`.
2. **Global Blade Component (`x-application-logo`)**:
   - Replaced default Breeze SVG in [resources/views/components/application-logo.blade.php](file:///c:/Users/Admin/Documents/Innovation%20portal/resources/views/components/application-logo.blade.php) with dynamic `<img>` tag binding to the asset.
3. **Internal & Authenticated Layouts**:
   - [resources/views/layouts/navigation.blade.php](file:///c:/Users/Admin/Documents/Innovation%20portal/resources/views/layouts/navigation.blade.php): Integrated the logo into the top navbar across all authenticated pages (Innovations, Reviewer Dashboard, Evaluation Workspace, User Profile).
   - [resources/views/layouts/guest.blade.php](file:///c:/Users/Admin/Documents/Innovation%20portal/resources/views/layouts/guest.blade.php): Branded the guest portal (Login, Staff Registration, Forgot Password, OTP verification) with responsive logo sizing and subtitle.
4. **Homepage & Footer Integration**:
   - [resources/views/welcome.blade.php](file:///c:/Users/Admin/Documents/Innovation%20portal/resources/views/welcome.blade.php): Replaced header monogram with official logo + "Staff Innovation Portal" divider tag. Added white framed badge in the dark footer to ensure high contrast.
5. **Browser Favicon**:
   - Added `<link rel="icon" type="image/png" href="{{ asset('images/ksg-logo.png') }}">` across all layout heads.

### Laravel Concepts vs. .NET Equivalents

| Laravel Concept | .NET Equivalent | Practical Explanation |
| :--- | :--- | :--- |
| **Blade Components (`<x-application-logo />`)** | **ASP.NET Core Tag Helpers / ViewComponents / Partial Views (`_LogoPartial.cshtml`)** | Reusable UI widgets in `resources/views/components/`. Any change to `application-logo.blade.php` immediately cascades across every view that references `<x-application-logo>`, avoiding repetitive HTML edits. |
| **`$attributes->merge(...)`** | **Tag Helper `[HtmlTargetElement]` / HTML attributes dictionary** | In Blade components, attributes passed when invoking (`<x-application-logo class="h-10 w-auto" />`) are merged with default component classes so callers can customize sizing and styling per layout context. |

---

## Step 11: Gated Reviewer Registration, Zero-Access Security Guard & Admin Approval Workflow (Completed)

### What Was Built
1. **Database Schema Expansion**:
   - Added `reviewer_status` (`string`, indexed, values: `pending`, `approved`, `rejected`) and `reviewer_approved_at` (`timestamp`, nullable) to `users` table via migration `2026_11_21_160946_add_reviewer_approval_fields_to_users_table`.
2. **Registration Role Selection** ([resources/views/auth/register.blade.php](file:///c:/Users/Admin/Documents/Innovation%20portal/resources/views/auth/register.blade.php)):
   - Interactive role selection cards allowing applicants to select **Staff Innovator** (immediate access) vs. **Reviewer / Evaluation Committee** (requires administrator approval).
   - Added fields for Department/Campus and Phone number.
3. **Zero-Access Registration Enforcement** ([app/Http/Controllers/Auth/RegisteredUserController.php](file:///c:/Users/Admin/Documents/Innovation%20portal/app/Http/Controllers/Auth/RegisteredUserController.php)):
   - When a user selects `role = 'reviewer'`, their account is created with `reviewer_status = 'pending'`.
   - The user is **NOT** logged in (zero portal access).
   - Automatically dispatched `ReviewerAccessRequestedNotification` via Gmail SMTP to all portal administrators (`admin@ksg.ac.ke`).
   - Redirects to `/login` with an informational status banner.
4. **Login Guard Interceptor** ([app/Http/Requests/Auth/LoginRequest.php](file:///c:/Users/Admin/Documents/Innovation%20portal/app/Http/Requests/Auth/LoginRequest.php)):
   - Even if a pending reviewer attempts to sign in with their password, `LoginRequest::authenticate()` checks `reviewer_status`. If `pending`, the session is immediately invalidated, and a validation error is thrown: *"Your Reviewer account is awaiting administrator approval. You will be notified via email once approved."*
5. **Admin Approval Console & Actions**:
   - [app/Http/Controllers/Admin/ReviewerApprovalController.php](file:///c:/Users/Admin/Documents/Innovation%20portal/app/Http/Controllers/Admin/ReviewerApprovalController.php):
     - `index`: Paginated list of pending applicant requests and historical approval logs.
     - `approve`: Sets `reviewer_status = 'approved'`, `reviewer_approved_at = now()`, assigns the Spatie `reviewer` role, and dispatches `ReviewerApprovedNotification` to the user with the requested message: *"Your Reviewer access has been approved. You may now evaluate proposals in the Reviewer Workspace."*
     - `reject`: Flags status as `'rejected'`.
   - Protected behind `role:admin` middleware.
   - [resources/views/admin/reviewers/index.blade.php](file:///c:/Users/Admin/Documents/Innovation%20portal/resources/views/admin/reviewers/index.blade.php): Dedicated management interface with 1-click **Approve Reviewer** and **Decline** buttons.
   - [resources/views/layouts/navigation.blade.php](file:///c:/Users/Admin/Documents/Innovation%20portal/resources/views/layouts/navigation.blade.php): Admin top navbar link with an unread badge counter showing pending applicant count.
6. **Automated Feature Verification** ([tests/Feature/ReviewerApprovalWorkflowTest.php](file:///c:/Users/Admin/Documents/Innovation%20portal/tests/Feature/ReviewerApprovalWorkflowTest.php)):
   - 7 automated tests covering registration, zero-access blocking, admin notifications, authorization gates, and post-approval login access.
   - Entire application test suite: **51 tests, 162 assertions passing**.

### Laravel Concepts vs. .NET Equivalents

| Laravel Concept | .NET Equivalent | Practical Explanation |
| :--- | :--- | :--- |
| **Gated Authentication in Form Request (`LoginRequest`)** | **`SignInManager<T>.CanSignInAsync(user)` / Custom `IUserClaimsPrincipalFactory`** | Rather than allowing authentication and relying solely on route middleware, checking approval status inside `LoginRequest::authenticate()` prevents unauthorized session creation at the credential verification boundary. |
| **Admin Authorization Middleware (`role:admin`)** | **`[Authorize(Roles = "Admin")]`** | Restricts administrative management routes to users with verified administrative roles. |
| **`Notification::send($admins, ...)`** | **`IEmailSender.SendEmailAsync` to Admin distribution list** | Dispatches transactional email notifications to a collection of recipient models in a single clean call. |

---

## Step 12: Role-Based UI Hardening with Blade `@can` Directives & Contextual Navigation (Completed)

### What Was Built
1. **Separation of Concerns in UI (Reviewer vs. Innovator)**:
   - Evaluated user roles and permissions to strictly enforce that reviewers cannot create innovations (`create innovations` permission belongs strictly to `innovator` and `admin`).
   - Hardened `resources/views/layouts/navigation.blade.php`: Wrapped both desktop and responsive `+ New Innovation` links in `@can('create', App\Models\Innovation::class)`.
   - Hardened `resources/views/innovations/index.blade.php`: Wrapped the top header `Submit Innovation` button and the empty state `+ Create New Innovation` button in `@can('create', App\Models\Innovation::class)`.
2. **Context-Aware Dashboard Landing (`/dashboard`)**:
   - In `routes/web.php`, updated the `/dashboard` redirect route:
     - Pure Reviewers (`reviewer` role without `innovator`) are redirected straight to their working queue at `route('reviewer.dashboard')`.
     - Staff Innovators are redirected to `route('innovations.index')`.
3. **Automated Verification**:
   - Added 3 new tests in `tests/Feature/ReviewerWorkflowTest.php` verifying that reviewers do not see creation buttons, cannot navigate to `/innovations/create` (403 Forbidden), and are routed directly to the Reviewer Dashboard upon login.
   - Complete application test suite: **54 tests, 171 assertions passing**.

### Laravel Concepts vs. .NET Equivalents

| Laravel Concept | .NET Equivalent | Practical Explanation |
| :--- | :--- | :--- |
| **Blade `@can` Directive** | **`@if ((await AuthorizationService.AuthorizeAsync(User, "CanCreateInnovation")).Succeeded)`** | Directly tests policy gates within Blade templates. If `InnovationPolicy::create` returns false for the authenticated user, the enclosed HTML is omitted from server rendering entirely. |
| **Defense in Depth (UI + Policy)** | **View hiding + `[Authorize(Policy = "...")]` on Action** | Hiding UI buttons improves user experience, but backend authorization enforcement via `Gate::authorize('create', Innovation::class)` in `InnovationController` ensures unauthorized direct HTTP calls are still rejected with `403 Forbidden`. |









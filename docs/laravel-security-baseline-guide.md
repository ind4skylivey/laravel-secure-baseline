# Laravel Security Baseline Guide with Secure Baseline

## Why your Laravel app needs a baseline
Misconfigurations—like exposed `/telescope` panels or `APP_DEBUG=true`—turn into production incidents. This guide shows how **Laravel Secure Baseline** turns that manual checklist into an automated workflow.

## Step 1: Install the package
```bash
composer require ind4skylivey/laravel-secure-baseline --dev
php artisan vendor:publish --tag=secure-baseline-config
```

## Step 2: Run the secure scan
```bash
php artisan secure:scan
php artisan secure:scan --format=schema > storage/logs/secure-baseline/schema.json
php artisan secure:scan --format=sarif > storage/logs/secure-baseline/scan.sarif
```
The command inspects configuration, sessions, cookies, CORS, headers, debug routes, and dependency age without modifying your files. Use the schema output for custom dashboards and the SARIF output for GitHub/ADO code scanning integrations.

## Step 3: Review the findings
Statuses:
- ✅ pass – nothing to fix
- ⚠️ warning – action recommended
- ❌ fail – needs immediate remediation

## Step 4: Generate a shareable report
```bash
php artisan secure:report --format=md --output=storage/logs/secure-baseline/report.md
php artisan secure:report --format=html --output=storage/app/reports/secure-baseline.html
```
Use Markdown for auditors and HTML for stakeholders who prefer formatted tables. Reports include summary tables, per-category findings, and remediation guidance.

## Step 5: Tune configuration & prioritize fixes
Publish the config (`php artisan vendor:publish --tag=secure-baseline-config`) and toggle scanners, add custom scanners, change report destinations, or adjust CLI defaults (`secure-baseline.cli.fail_on`, `secure-baseline.cli.error_exit_code`). Then prioritize fixes:
1. Remove or protect `/telescope`, `/horizon`, and `/phpinfo`.
2. Force HTTPS cookies with `SESSION_SECURE_COOKIE=true`.
3. Replace wildcard CORS rules with explicit origins per environment.
4. Upgrade Laravel when the scan reports an outdated release.

Automating this checklist keeps every deployment aligned with a hardened baseline.

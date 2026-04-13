# Laravel Security Checklist with Secure Baseline Automation

## Problem → solution overview
Manual checklists get skipped when deadlines loom. **Secure Baseline** automates the checklist so you can enforce it on every commit.

## Checklist categories
1. **Environment sanity** – `APP_ENV`, `APP_DEBUG`, `APP_URL`, `APP_KEY`, `LOG_LEVEL`
2. **Session & cookie rules** – Secure, HttpOnly, SameSite, session driver
3. **CORS controls** – Allowed origins, headers, methods
4. **Security headers** – `X-Frame-Options`, `X-Content-Type-Options`, `Strict-Transport-Security`, `Referrer-Policy`, CSP hints
5. **Routes & tooling exposure** – `/telescope`, `/horizon`, `/phpinfo`, `/debugbar`
6. **Version drift** – Compare Laravel release against latest security patch

## Using the checklist in practice
```bash
php artisan secure:scan --format=cli
php artisan secure:scan --format=schema > storage/logs/secure-baseline/scan.json
php artisan secure:scan --format=sarif > storage/logs/secure-baseline/scan.sarif
```
- ✅ All checks pass → promote build confidently
- ⚠️ Warnings → address before final QA
- ❌ Failures → block deploys until fixed (pair with `--fail-on=warning` or `--fail-on=fail` + `--error-exit-code=<int>` to control CI behavior)

## Export actionable reports
```bash
php artisan secure:report --format=md --output=storage/app/reports/security-baseline.md
php artisan secure:report --format=html --output=storage/app/reports/security-baseline.html
```
Share Markdown/HTML reports with stakeholders, attach them to change records, and keep an audit trail for compliance.

## Extend the checklist
Scanners are class-based, so you can add organization-specific checks (for example, “API rate limiting configured”) and register them via the config file.

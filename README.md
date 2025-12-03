<div align="center">

```ascii
╔═══════════════════════════════════════════════════════════╗
║                                                           ║
║      🛡️  LARAVEL SECURE BASELINE                         ║
║      Automated Security Audit for Laravel Apps           ║
║                                                           ║
╚═══════════════════════════════════════════════════════════╝
```

# Laravel Secure Baseline 🛡️ — Automated Laravel security scanner
### **Automated Laravel Security Scanner — Catch Misconfigurations Before Production**

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white&style=for-the-badge)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10%20%7C%2011-FF2D20?logo=laravel&logoColor=white&style=for-the-badge)](https://laravel.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-passing-brightgreen.svg?style=for-the-badge)](https://github.com/ind4skylivey/laravel-secure-baseline)
[![Packagist Version](https://img.shields.io/packagist/v/ind4skylivey/laravel-secure-baseline?label=Packagist&logo=packagist&style=for-the-badge)](https://packagist.org/packages/ind4skylivey/laravel-secure-baseline)
[![Packagist Downloads](https://img.shields.io/packagist/dt/ind4skylivey/laravel-secure-baseline?logo=packagist&style=for-the-badge&label=Downloads&color=blue&cacheSeconds=3600)](https://packagist.org/packages/ind4skylivey/laravel-secure-baseline/stats)

**Run one Artisan command. Get instant Laravel security audit. Deploy with confidence.**

```bash
php artisan secure:scan
```

[🚀 Quick Start](#-quick-start-secure-your-laravel-app-in-60-seconds) • [📋 Features](#-features) • [🔍 What It Checks](#-laravel-security-checks-covered-what-laravel-secure-baseline-checks) • [⚙️ Configuration](#-configuration) • [🔄 CI/CD Integration](#-laravel-cicd-integration) • [📚 Docs](#-documentation)

---

⭐ **Star this repo** if you believe in secure-by-default Laravel applications!

</div>

---

## 🚨 The Problem: Laravel Security Audit Gaps in Production

Laravel applications in production often suffer from **critical security misconfigurations** that expose sensitive data, leak credentials, and create attack vectors. These vulnerabilities aren't code bugs—they're configuration oversights that slip through manual reviews.

Common Laravel security issues include `APP_DEBUG=true` leaking stack traces with database credentials, missing or weak `APP_KEY` values compromising session encryption, overly permissive CORS policies (`allowed_origins = *`), debug tools like Laravel Telescope and Horizon left publicly accessible, and security headers (`X-Frame-Options`, `Strict-Transport-Security`) completely absent. According to security research, **78% of breaches involve misconfiguration**, not code vulnerabilities.

Manual Laravel security checklists are time-consuming, error-prone, and often skipped under deployment pressure. Teams need **automated Laravel security baseline checks** that run in seconds and integrate seamlessly into CI/CD pipelines.

## ✨ The Solution: Automated Laravel Security Baseline Scanner

**Laravel Secure Baseline** is a zero-configuration Laravel security audit tool that scans your application in seconds and produces actionable security findings. Run one Artisan command to validate environment configuration, session security, CORS policies, security headers, debug route exposure, and dependency versions against Laravel security best practices.

No complex setup. No security expertise required. Just install the package, run `php artisan secure:scan`, and get a color-coded Laravel security report with **pass (✅)**, **warning (⚠️)**, and **fail (❌)** statuses. Export results as JSON, Markdown, HTML, or SARIF for GitHub Security integration. Perfect for local development, staging validation, and production deployment gates.

## 🙌 Why Laravel Developers Use This Scanner

- Built for CI: Add security checks to every pull request so misconfigurations never reach production.
- Secure deploys: Enforce a repeatable security baseline before each release.
- Actionable findings: Provide remediation steps tuned for Laravel 10/11.
- Fits real pipelines: Works with GitHub Actions, GitLab CI, Jenkins, and self-hosted runners.
- Production-ready: Flags debug routes, weak cookies, missing HTTPS headers, and outdated dependencies.

---

## 📋 Features

<table>
<tr>
<td width="50%">

### 🔍 Comprehensive Laravel Security Checks
- ✅ **Configuration Audit** — `APP_DEBUG`, `APP_ENV`, `APP_KEY`, `APP_URL`, `LOG_LEVEL`
- ✅ **Session & Cookie Security** — `SESSION_SECURE_COOKIE`, `SESSION_HTTP_ONLY`, `SESSION_SAME_SITE`
- ✅ **CORS Policy Validation** — Detect wildcard origins, dangerous methods
- ✅ **Security Headers** — `X-Frame-Options`, `HSTS`, `X-Content-Type-Options`, CSP
- ✅ **Route Exposure Detection** — `/telescope`, `/horizon`, `/phpinfo`, debug endpoints
- ✅ **Dependency Version Checks** — Laravel version, security patch warnings

</td>
<td width="50%">

### ⚡ Developer-Friendly Integration
- 🚀 **Zero Configuration** — Works out of the box
- 📊 **Multiple Export Formats** — JSON, Markdown, HTML, SARIF
- 🔄 **CI/CD Ready** — GitHub Actions, GitLab CI, Jenkins
- 🎯 **Flexible Fail Conditions** — `--fail-on=warning` or `--fail-on=fail`
- 🧩 **Extensible Architecture** — Add custom security scanners
- ⚡ **Lightning Fast** — Complete scan in < 5 seconds

</td>
</tr>
</table>

## 🆚 Laravel Security Scanner Comparison

| Capability | Laravel Secure Baseline | Enlightn (free) | No scanner |
|------------|-------------------------|-----------------|------------|
| Setup time | 60 seconds (`composer require` + `php artisan secure:scan`) | Requires config + account | N/A |
| CI enforcement | Fails pipeline via `--fail-on` | Limited in free tier | None |
| Focus | Env/config hardening for production | Code insights/performance | Hope-and-pray |
| Config checks (APP_DEBUG/APP_KEY/headers) | ✔️ | Partial | ❌ |
| Telemetry | None (runs in your CI) | SaaS telemetry | N/A |
| Output formats | CLI, JSON, MD, HTML, SARIF | Dashboard + CLI | None |

---

## 🚀 Quick Start: Secure Your Laravel App in 60 Seconds

### Installation

Install via Composer in your Laravel project:

```bash
# Install the package (dev dependency)
composer require ind4skylivey/laravel-secure-baseline --dev

# Optional: Publish configuration
php artisan vendor:publish --tag=secure-baseline-config
```

### Run Your First Laravel Security Scan

```bash
# Run a complete security audit
php artisan secure:scan

# Generate detailed report
php artisan secure:report --format=html --output=security-report.html

# Fail CI builds on warnings (strict mode)
php artisan secure:scan --fail-on=warning --error-exit-code=1
```

**That's it!** No configuration files to edit. No learning curve. Just instant Laravel security insights.

---

## 📊 Example Output: Laravel Security Scan Results

### Console Output (CLI)

```bash
$ php artisan secure:scan

🛡️  Laravel Secure Baseline — Security Audit Report
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

⚙️  Configuration Security
   ✅ APP_KEY is set (32 characters)
   ✅ APP_DEBUG is disabled (production mode)
   ✅ APP_ENV set to 'production'
   ⚠️  LOG_LEVEL is 'debug' (recommended: 'error' or 'warning' in production)

🍪 Session & Cookie Security
   ✅ SESSION_DRIVER is 'redis' (secure)
   ⚠️  SESSION_SECURE_COOKIE is false (set to true for HTTPS-only cookies)
   ⚠️  SESSION_SAME_SITE not set (recommended: 'lax' or 'strict')
   ✅ SESSION_HTTP_ONLY is true

🌐 CORS Configuration Audit
   ❌ CORS allowed_origins contains wildcard "*" (allows any origin)
   ⚠️  CORS allows all HTTP methods (PUT, DELETE exposed)
   ❌ CORS supports_credentials is true with wildcard origins (security risk)

🛡️  Security Headers
   ✅ X-Frame-Options: SAMEORIGIN
   ✅ X-Content-Type-Options: nosniff
   ❌ Strict-Transport-Security header missing (HSTS required for HTTPS)
   ⚠️  Content-Security-Policy not configured

🚪 Debug Routes & Endpoint Exposure
   ❌ /telescope route is publicly accessible (GET, POST)
      → No authentication middleware detected
      → Recommendation: Add Gate authorization or disable in production
   ✅ /horizon route protected by authentication middleware
   ✅ No /phpinfo routes detected

📦 Laravel Framework & Dependencies
   ✅ Laravel 11.31.0 detected (up to date)
   ⚠️  Running PHP 8.1.12 (PHP 8.2+ recommended for security patches)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📊 Summary: 9 passed • 7 warnings • 4 critical failures

💡 Recommendation: Fix critical failures before deploying to production.
   Run: php artisan secure:report --format=md for detailed remediation steps.
```

### JSON Output (For Automation)

```bash
$ php artisan secure:scan --format=json | jq '.totals'

{
  "pass": 9,
  "warning": 7,
  "fail": 4,
  "total": 20
}
```

---

## 📄 Example Markdown Report

Running `php artisan secure:report --format=md` generates a detailed report:

```markdown
# Laravel Security Baseline Report

**Generated:** 2025-11-11 14:32 UTC  
**Application:** production  
**Laravel Version:** 11.31.0

## 🎯 Executive Summary

- ✅ **9 checks passed** — Good security baseline
- ⚠️ **7 warnings** — Recommended improvements
- ❌ **4 critical failures** — Require immediate action

**Overall Risk Level:** HIGH — Production deployment not recommended

---

## ❌ Critical Failures (Must Fix)

### 1. CORS Wildcard Origins
**Severity:** CRITICAL  
**Category:** CORS Configuration

**Finding:** CORS `allowed_origins` is set to `["*"]`, allowing any website to make authenticated requests.

**Risk:** Attackers can exfiltrate user data via malicious websites.

**Remediation:**
```php
// config/cors.php
'allowed_origins' => [
    'https://yourdomain.com',
    'https://app.yourdomain.com',
],
'supports_credentials' => true,
```

### 2. Laravel Telescope Publicly Accessible
**Severity:** CRITICAL  
**Category:** Debug Route Exposure

**Finding:** `/telescope` route accessible without authentication in production.

**Risk:** Exposes database queries, Redis commands, exceptions, and user sessions.

**Remediation:**
```php
// app/Providers/TelescopeServiceProvider.php
protected function gate()
{
    Gate::define('viewTelescope', fn ($user) => 
        in_array($user->email, ['admin@yourdomain.com'])
    );
}
```

---

## ⚠️ Warnings (Recommended)

### Session Secure Cookie Flag
Set `SESSION_SECURE_COOKIE=true` in `.env` to prevent cookie transmission over HTTP.

### Missing HSTS Header
Add `Strict-Transport-Security: max-age=31536000; includeSubDomains` header via middleware.

---

## ✅ Passed Checks

- APP_KEY properly configured
- APP_DEBUG disabled in production
- X-Frame-Options header set
- No /phpinfo routes detected
- Laravel framework up to date

---

**Next Steps:**  
1. Fix 4 critical failures  
2. Review 7 warnings  
3. Re-run scan: `php artisan secure:scan`  
4. Deploy with confidence
```

---

## ✅ Production Deployment Checklist for Secure Laravel Deployments

- Run `php artisan secure:scan --fail-on=fail` before every release to enforce the Laravel security baseline.
- Review CORS, session, and header findings to harden HTTPS and cookie handling for production.
- Export SARIF or HTML reports for audit trails and attach them to deployment artifacts.
- Enable Laravel CI security checks in your pipeline (GitHub Actions example below) to block risky builds automatically.
- Confirm `APP_DEBUG=false`, strong `APP_KEY`, and restrictive `SESSION_SECURE_COOKIE` prior to tag creation.
- Rerun after infrastructure changes (load balancers, CDN) to validate security headers and HTTPS redirects.

## ⚙️ Configuration

### Publishing Configuration

```bash
php artisan vendor:publish --tag=secure-baseline-config
```

This creates `config/secure-baseline.php` where you can customize scanner behavior.

### Key Configuration Options

```php
<?php

return [
    // Enable/disable specific security scanners
    'scanners' => [
        'config'       => true,  // ⚙️  Environment configuration checks
        'session'      => true,  // 🍪 Session & cookie security
        'cors'         => true,  // 🌐 CORS policy validation
        'headers'      => true,  // 🛡️  Security headers (HSTS, CSP, etc.)
        'routes'       => true,  // 🚪 Debug route exposure (Telescope, Horizon)
        'dependencies' => true,  // 📦 Laravel version & dependency checks
    ],

    // Register custom security scanners
    'custom_scanners' => [
        // 'company-policy' => \App\Security\Scanners\CompanyPolicyScanner::class,
    ],

    // Report generation settings
    'report' => [
        'default_format'      => 'cli',      // cli, json, md, html, sarif
        'default_output_path' => storage_path('logs/security'),
        'include_timestamp'   => true,
        'include_metadata'    => true,
    ],

    // CI/CD behavior
    'ci' => [
        'fail_on'         => 'fail',  // none, warning, fail
        'error_exit_code' => 1,       // Exit code when checks fail
    ],
];
```

### Disabling Specific Scanners

```php
// Disable CORS checks for internal APIs
'scanners' => [
    'cors' => false,
],
```

### Adding Custom Scanners

```php
// app/Security/Scanners/CompanyPolicyScanner.php
namespace App\Security\Scanners;

use Ind4skylivey\LaravelSecureBaseline\Contracts\Scanner;
use Ind4skylivey\LaravelSecureBaseline\Data\ScanResult;
use Ind4skylivey\LaravelSecureBaseline\Enums\FindingStatus;

class CompanyPolicyScanner implements Scanner
{
    public function scan(): ScanResult
    {
        $result = new ScanResult('Company Security Policy');
        
        if (config('app.name') === 'Laravel') {
            $result->addFinding(
                'Application name not customized',
                FindingStatus::WARNING,
                'Set APP_NAME in .env for audit trails'
            );
        }
        
        return $result;
    }
}
```

Then register in `config/secure-baseline.php`:

```php
'custom_scanners' => [
    'company-policy' => \App\Security\Scanners\CompanyPolicyScanner::class,
],
```


## 🔄 Laravel CI/CD Integration

Integrate Laravel Secure Baseline into your deployment pipeline to **block insecure releases automatically**. The workflow below adds Laravel CI security checks for continuous Laravel vulnerability detection and secure Laravel deployments.

### GitHub Actions: Secure Laravel deployments (Recommended)

Add to `.github/workflows/security-scan.yml`:

```yaml
name: Laravel Security Baseline

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  security-scan:
    runs-on: ubuntu-latest
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, openssl, pdo_mysql
          tools: composer:v2

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress --no-interaction

      - name: Run Laravel security audit
        run: php artisan secure:scan --format=json --fail-on=warning | tee scan-results.json

      - name: Check for failures
        run: |
          FAILURES=$(jq '.totals.fail' scan-results.json)
          WARNINGS=$(jq '.totals.warning' scan-results.json)
          
          if [ "$FAILURES" -gt 0 ]; then
            echo "❌ Security scan found $FAILURES critical issues"
            exit 1
          fi
          
          if [ "$WARNINGS" -gt 0 ]; then
            echo "⚠️  Security scan found $WARNINGS warnings"
            exit 1
          fi

      - name: Generate HTML report
        if: always()
        run: php artisan secure:report --format=html --output=security-report.html

      - name: Upload security report
        if: always()
        uses: actions/upload-artifact@v4
        with:
          name: laravel-security-report
          path: |
            scan-results.json
            security-report.html

      - name: Upload SARIF to GitHub Security
        if: always()
        uses: github/codeql-action/upload-sarif@v3
        with:
          sarif_file: secure-baseline.sarif
```

#### Minimal GitHub Actions snippet (just fail on issues)

```yaml
name: security
on: [push, pull_request]

jobs:
  scan:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, openssl
      - run: composer install --prefer-dist --no-progress --no-interaction
      - run: php artisan secure:scan --fail-on=fail
```

### GitLab CI

Add to `.gitlab-ci.yml`:

```yaml
laravel-security-scan:
  stage: test
  image: php:8.2-cli
  
  before_script:
    - apt-get update && apt-get install -y git unzip
    - curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    - composer install --prefer-dist --no-progress --no-interaction
  
  script:
    - php artisan secure:scan --format=json --fail-on=fail > scan-results.json
    - php artisan secure:report --format=html --output=security-report.html
    
    # Check results
    - |
      FAILURES=$(jq '.totals.fail' scan-results.json)
      if [ "$FAILURES" -gt 0 ]; then
        echo "❌ Laravel security baseline found $FAILURES critical issues"
        exit 1
      fi
  
  artifacts:
    when: always
    paths:
      - scan-results.json
      - security-report.html
    reports:
      junit: scan-results.json
  
  only:
    - main
    - develop
    - merge_requests
```

### Generic Shell Script (Any CI)

Create `scripts/security-check.sh`:

```bash
#!/bin/bash
set -e

echo "🛡️  Running Laravel Security Baseline Scan..."

# Run scan and save results
php artisan secure:scan --format=json --fail-on=fail > scan-results.json

# Parse results
PASS=$(jq '.totals.pass' scan-results.json)
WARN=$(jq '.totals.warning' scan-results.json)
FAIL=$(jq '.totals.fail' scan-results.json)

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 Laravel Security Scan Results"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Passed:   $PASS"
echo "⚠️  Warnings: $WARN"
echo "❌ Failed:   $FAIL"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Generate HTML report
php artisan secure:report --format=html --output=reports/security-baseline.html

# Fail if critical issues found
if [ "$FAIL" -gt 0 ]; then
  echo "❌ Deployment blocked: Fix $FAIL critical security issues"
  echo "📄 View report: reports/security-baseline.html"
  exit 1
fi

if [ "$WARN" -gt 5 ]; then
  echo "⚠️  Warning: $WARN security warnings detected"
  echo "📄 Review report: reports/security-baseline.html"
  exit 1
fi

echo "✅ Laravel security baseline checks passed"
exit 0
```

Make executable and use in any CI:

```bash
chmod +x scripts/security-check.sh
./scripts/security-check.sh
```

---

## 🔍 Laravel Security Checks Covered (What Laravel Secure Baseline Checks)

### ⚙️ Configuration Security

**Purpose:** Validate environment configuration follows Laravel security best practices.

| Check | Description | Risk if Failed |
|-------|-------------|----------------|
| `APP_DEBUG` | Must be `false` in production | Stack traces leak database credentials, API keys, file paths |
| `APP_ENV` | Should be `production` | Non-production environments may bypass security features |
| `APP_KEY` | Must be 32+ characters | Weak keys compromise session encryption, cookie signing |
| `APP_URL` | Must match production domain | CSRF token validation failures, asset loading issues |
| `LOG_LEVEL` | Recommended `error` or `warning` | Verbose logging exposes sensitive data in logs |

### 🍪 Session & Cookie Security

**Purpose:** Ensure session management resists hijacking and XSS attacks.

| Check | Description | Risk if Failed |
|-------|-------------|----------------|
| `SESSION_DRIVER` | Validate secure drivers (redis, database) | File-based sessions vulnerable on shared hosting |
| `SESSION_SECURE_COOKIE` | Must be `true` for HTTPS sites | Session cookies transmitted over HTTP (MITM attacks) |
| `SESSION_HTTP_ONLY` | Must be `true` | JavaScript can access session cookies (XSS exploitation) |
| `SESSION_SAME_SITE` | Should be `lax` or `strict` | CSRF attacks from malicious websites |
| `SESSION_LIFETIME` | Validate reasonable timeout | Long-lived sessions increase hijacking window |

### 🌐 CORS Configuration Audit

**Purpose:** Prevent unauthorized cross-origin requests from malicious websites.

| Check | Description | Risk if Failed |
|-------|-------------|----------------|
| Wildcard origins (`*`) | Detect `allowed_origins = *` | Any website can make authenticated requests |
| Credential + wildcard | `supports_credentials` + `*` origins | Browser security violation, credential leakage |
| Dangerous methods | Excessive `allowed_methods` | Unintended PUT/DELETE operations from external sites |
| Header exposure | `exposed_headers` validation | Sensitive headers leaked to untrusted origins |

### 🛡️ Security Headers

**Purpose:** Browser-level security controls to mitigate common attacks.

| Header | Purpose | Risk if Missing |
|--------|---------|-----------------|
| `X-Frame-Options` | Prevent clickjacking | App can be embedded in malicious iframes |
| `X-Content-Type-Options` | Disable MIME sniffing | Browser executes malicious scripts |
| `Referrer-Policy` | Control referrer leakage | Sensitive URLs leaked to third parties |
| `Strict-Transport-Security` | Enforce HTTPS | MITM attacks, protocol downgrade |
| `Content-Security-Policy` | Control resource loading | XSS attacks, data exfiltration |
| `Permissions-Policy` | Disable unnecessary features | Microphone/camera abuse, geolocation tracking |

### 🚪 Debug Route & Endpoint Exposure

**Purpose:** Detect debug tools accidentally left accessible in production.

| Route/Tool | Check | Risk if Exposed |
|------------|-------|-----------------|
| `/telescope` | Laravel Telescope | Full database queries, Redis commands, exceptions visible |
| `/horizon` | Laravel Horizon | Queue jobs, worker metrics, failed job details exposed |
| `/phpinfo` | PHP configuration | Server paths, environment variables, extensions revealed |
| `/_ignition` | Ignition error pages | Stack traces with credentials in production |
| Debug routes | Routes with `debug` in name | Application internals exposed to attackers |

**Middleware Validation:** Checks for authentication/authorization middleware on sensitive routes.

### 📦 Laravel Framework & Dependencies

**Purpose:** Ensure Laravel and dependencies are patched against known vulnerabilities.

| Check | Description | Risk if Failed |
|-------|-------------|----------------|
| Laravel version | Detect outdated major/minor versions | Missing security patches, known CVEs exploitable |
| PHP version | Validate PHP 8.1+ | End-of-life PHP versions lack security updates |
| Composer dependencies | Warn about outdated packages | Known vulnerabilities in dependencies |

---

## 🗺️ Roadmap & Future Enhancements

We're actively developing new Laravel security audit features:

- 🔌 **Plugin System** — Third-party scanner marketplace
- 📊 **Baseline Diffing** — Track security improvements over time (`--compare-with=previous.json`)
- 🔔 **Notification Integrations** — Slack, Discord, PagerDuty alerts for failed scans
- 🎨 **Custom Report Templates** — Branded HTML reports with company logos
- 🔄 **Auto-Fix Mode** — `php artisan secure:fix` to automatically remediate common issues
- 🌍 **Multi-Language Support** — Localized security recommendations
- 🔐 **Secret Scanning** — Detect hardcoded API keys, passwords in config files
- 📈 **Security Score** — Numeric security rating (0-100) for dashboards
- 🧪 **Penetration Testing Mode** — Automated exploit validation for findings

**Have a feature idea?** [Open an issue](https://github.com/ind4skylivey/laravel-secure-baseline/issues/new) or discussion!

---

## 🤝 Contributing

We welcome contributions from the Laravel security community!

### How to Contribute

1. 🐛 **Report Security Issues** — See [Security Policy](#-security-vulnerabilities)
2. 💡 **Suggest Features** — [Open a feature request](https://github.com/ind4skylivey/laravel-secure-baseline/issues/new?labels=enhancement)
3. 🔧 **Submit Pull Requests** — Check [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines
4. 📖 **Improve Documentation** — Fix typos, add examples, clarify instructions
5. ⭐ **Star the Repository** — Help others discover this tool

### Development Setup

```bash
# Clone the repository
git clone https://github.com/ind4skylivey/laravel-secure-baseline.git
cd laravel-secure-baseline

# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer analyze

# Check code style
composer format
```

### Coding Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style
- Write tests for new scanners (Pest framework)
- Update documentation for new features
- Keep backward compatibility

**Before submitting PR:**
- ✅ All tests pass (`composer test`)
- ✅ Code follows PSR-12 (`composer format`)
- ✅ Static analysis passes (`composer analyze`)
- ✅ Documentation updated

---

## 🔐 Security Vulnerabilities

**Found a security vulnerability in Laravel Secure Baseline itself?**

Please **DO NOT** open a public GitHub issue. Responsible disclosure protects all users.

### Reporting Process

**Email:** livey_sh13lds1b33@keemail.me (PGP key available on request)

**Include:**
- Description of the vulnerability
- Steps to reproduce
- Potential impact assessment
- Suggested fix (if available)

### What to Expect

- **24-hour response time** — Initial acknowledgment
- **7-day patch timeline** — Critical vulnerabilities
- **Coordinated disclosure** — We'll work with you on timing
- **Security advisory** — Published after patch release
- **Credit** — Public acknowledgment in release notes (unless you prefer anonymity)

### Security Best Practices for Users

- Keep Laravel Secure Baseline updated (`composer update ind4skylivey/laravel-secure-baseline`)
- Run scans regularly, not just at deployment time
- Review scan results before dismissing warnings
- Use `--fail-on=warning` in CI to enforce strict security standards

---

## 📜 License

Laravel Secure Baseline is open-source software licensed under the **[MIT License](LICENSE)**.

```text
MIT License

Copyright (c) 2025 ind4skylivey

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

[Full license text in LICENSE file]
```

**Why MIT?** Maximum adoption, zero legal friction. Use freely in commercial projects.

---

## 👤 Credits & Acknowledgments

### Author

**ind4skylivey**  
Security Researcher & Laravel Developer

- 🐙 GitHub: [@ind4skylivey](https://github.com/ind4skylivey)

### Inspiration & Resources

- [Laravel Security Documentation](https://laravel.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Checklist by Sqreen](https://www.sqreen.com/checklists/laravel-security-checklist)
- [Mozilla Observatory](https://observatory.mozilla.org/)

### Special Thanks

- Laravel community for security best practices
- Contributors who submitted scanners and bug fixes
- Security researchers who responsibly disclosed vulnerabilities

---

## 📚 Documentation

- 📖 [Laravel Security Baseline Guide](docs/laravel-security-baseline-guide.md) — Detailed explanations of each check
- ✅ [Laravel Security Checklist](docs/laravel-security-checklist-with-secure-baseline.md) — Manual review checklist
- 🔄 [CI/CD Integration Guide](docs/integrating-secure-baseline-into-ci-cd.md) — Advanced pipeline configurations
- 📦 [Publishing to Packagist](docs/publishing-to-packagist.md) — For maintainers
- 🔌 [Creating Custom Scanners](docs/custom-scanners.md) — Extend with your own checks
- 🗓️ [Changelog](CHANGELOG.md) — Release history

---

<div align="center">

## 🛡️ Secure Your Laravel Application Today

**Stop shipping vulnerable Laravel apps. Start scanning.**

```bash
composer require ind4skylivey/laravel-secure-baseline --dev
php artisan secure:scan
```

<br>

[![Star on GitHub](https://img.shields.io/github/stars/ind4skylivey/laravel-secure-baseline?style=social)](https://github.com/ind4skylivey/laravel-secure-baseline)
<br>

**Made with ❤️ for the Laravel security community**

[⭐ Star on GitHub](https://github.com/ind4skylivey/laravel-secure-baseline) • [📦 View on Packagist](https://packagist.org/packages/ind4skylivey/laravel-secure-baseline) • [🐛 Report Issues](https://github.com/ind4skylivey/laravel-secure-baseline/issues) • [💬 Discussions](https://github.com/ind4skylivey/laravel-secure-baseline/discussions)

---

**Keywords:** Laravel security, Laravel security audit, Laravel security scanner, Laravel security baseline, secure Laravel production, Laravel security checklist, automated Laravel security, Laravel config audit, Laravel security best practices, Laravel vulnerability scanner, Laravel security testing, Laravel CORS security, Laravel session security, Laravel security headers

</div>

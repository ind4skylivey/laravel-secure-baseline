# Integrating Laravel Secure Baseline Into CI/CD Pipelines

## Why automate the security baseline?
CI/CD pipelines already run tests and linters. Adding **Laravel Secure Baseline** ensures each deployment passes a repeatable security audit before hitting production.

## Step 1: Install dependencies
Add to your pipeline image or `composer install` step:
```bash
composer require ind4skylivey/laravel-secure-baseline --dev
```
Publish the config once in the repo so the pipeline shares the same toggles.

## Step 2: Run the scan job
```bash
php artisan secure:scan --format=json > storage/logs/secure-baseline/scan.json
FAILURES=$(jq '.totals.fail' storage/logs/secure-baseline/scan.json)
if [ "$FAILURES" -gt 0 ]; then
  echo "Secure Baseline detected $FAILURES failures"; exit 1;
fi
```
Parse the JSON to detect ⚠️ and ❌ statuses and decide whether to block the deploy. Use the totals to fail fast and surface the offending categories.

## Step 3: Generate artifacts
```bash
php artisan secure:report --format=md --output=storage/logs/secure-baseline/report.md
php artisan secure:report --format=html --output=storage/logs/secure-baseline/report.html
```
Upload both Markdown (diff-friendly) and HTML (executive-ready) artifacts for auditors or change-management boards.

## Step 4: Fail fast on critical findings
- Treat ❌ failures (exposed debug routes, missing APP_KEY, wildcard CORS) as build breakers.
- Allow ⚠️ warnings to pass with a Jira ticket if desired.

## Step 5: Observe over time
Store reports per release tag to show compliance trends and prove due diligence during audits.

## GitHub Actions quickstart
```yaml
name: secure-baseline

on:
  workflow_dispatch:
  pull_request:
    branches: [ main ]

jobs:
  secure-baseline:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring
          tools: composer
      - run: composer install --prefer-dist --no-progress
      - run: php artisan secure:scan --format=json > secure-scan.json
      - run: |
          FAILS=$(jq '.totals.fail' secure-scan.json)
          if [ "$FAILS" -gt 0 ]; then
            echo "Secure Baseline detected $FAILS failures" && exit 1;
          fi
      - run: php artisan secure:report --format=md --output=secure-baseline.md
      - uses: actions/upload-artifact@v4
        with:
          name: secure-baseline
          path: |
            secure-scan.json
            secure-baseline.md
```

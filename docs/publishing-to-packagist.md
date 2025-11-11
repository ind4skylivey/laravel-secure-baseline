# Publishing Laravel Secure Baseline to Packagist

## 1. Prepare the repository
- Ensure `main` (or release branch) is clean: `composer validate` and `composer test` should pass.
- Update `CHANGELOG.md` (if present) and bump the version via annotated Git tags (Packagist reads tags, not the JSON file).

## 2. Tag a release
```bash
VERSION=0.1.0
git tag -a v$VERSION -m "Release v$VERSION"
git push origin v$VERSION
```

## 3. Submit/refresh on Packagist
1. Visit [https://packagist.org/packages/submit](https://packagist.org/packages/submit) the first time.
2. Provide the public Git URL (for example `https://github.com/ind4skylivey/laravel-secure-baseline`).
3. Enable the GitHub Webhook so Packagist auto-updates when you push new tags.

## 4. Automate releases (optional)
- `.github/workflows/release.yml` (included in this repo) runs on `v*` tags, installs dependencies, runs tests, generates SARIF/Markdown/HTML artifacts, uploads SARIF to GitHub Security, notifies Packagist, and publishes a GitHub Release. Configure `PACKAGIST_USERNAME` and `PACKAGIST_API_TOKEN` secrets before tagging.
- If you fork, update the repository URL inside the workflow before pushing.

## 5. Post-release checklist
- Announce the release internally or via social channels.
- Monitor Packagist download stats and GitHub issues.
- Capture feedback for the next milestone (screenshots/docs, additional scanners, etc.).

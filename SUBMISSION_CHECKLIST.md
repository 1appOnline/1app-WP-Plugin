# OneApp Universal Payment Handler Submission Checklist

Version target: 3.0.0

## Store Readiness Pass

- Confirm the plugin activates cleanly on a fresh WordPress install.
- Confirm the settings page loads and saves the public key correctly.
- Confirm the frontend shortcode renders and initiates checkout correctly.
- Confirm the admin direct payment form works and logs transactions.
- Confirm the dynamic form builder saves fields correctly with nonce protection.
- Confirm the file upload flow accepts only intended file types.
- Confirm transaction posts are created with the expected metadata.
- Confirm no PHP notices, warnings, or fatal errors appear in debug logs.
- Confirm the plugin works with the current WordPress version you plan to support.

## Release Package Checklist

- Ship `oneapp-custom-checkout.php` as the main plugin file.
- Include `oneapp-form-builder.php` as the form builder support file.
- Include `readme.txt` with stable tag and external service disclosure.
- Include `uninstall.php` for cleanup.
- Remove stray development artifacts before packaging.
- Zip the plugin root folder so the main plugin file sits at the top level.

## Submission Notes

- WordPress.org review is usually strictest on security, readme quality, and privacy disclosure.
- Keep the plugin header version, `readme.txt` stable tag, and `README.md` release note aligned.
- Since this plugin loads a third-party checkout script, keep that dependency documented in the readme.

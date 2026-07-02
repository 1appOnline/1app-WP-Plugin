=== OneApp Universal Payment Handler ===
Contributors: alexbamidele, adedayoadejumo
Tags: payment, oneapp, forms, wpforms, gravity forms, contact form 7
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 3.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A universal OneApp payment handler for WordPress forms, custom forms, and admin payment workflows.

== Description ==

OneApp Universal Payment Handler provides a payment form, transaction logging, and a form builder for custom payment forms.

Features:

* Universal payment integration for custom forms and form plugins
* Direct payment form in the WordPress admin
* Transaction logging via a custom post type
* Dynamic form builder for reusable payment forms
* Shortcode-based frontend payment form
* Secure AJAX logging with nonce verification

External services used:

* `https://js.oneappgo.com/v1/checkout.js`

The plugin sends payment details such as first name, last name, email, phone, amount, and reference to the OneApp checkout script when a payment is initiated.

== Installation ==

1. Upload the plugin folder to `wp-content/plugins/`.
2. Activate the plugin in WordPress Admin.
3. Open the 1app Payment Settings menu.
4. Add your OneApp public key.

== Frequently Asked Questions ==

= Which form plugins are supported? =

The plugin is designed to work with custom forms and can be extended to support WPForms, Gravity Forms, Contact Form 7, and similar systems.

= How are transactions stored? =

Transactions are stored as a custom post type called `oneapp_transaction`.

== Changelog ==

= 3.0.0 =
* Security hardening for form save operations.
* Release metadata aligned for submission readiness.

= 2.0.0 =
* Initial public release.

# OneApp Universal Payment Handler

**Contributors:** Alexander Bamidele  
**Tags:** payment, oneapp, forms, wpforms, gravity forms, contact form 7, universal  
**Requires at least:** 5.0  
**Tested up to:** 6.5  
**Stable tag:** 2.0.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

A universal OneApp payment handler for any WordPress form (WPForms, Gravity Forms, Contact Form 7, custom forms, and more). Log all transactions, accept payments via OneApp, and manage payment forms from your dashboard.

---

## Features

- **Universal Payment Integration:** Works with any WordPress form plugin or custom form.
- **Admin Direct Payment Form:** Accept payments directly from the WordPress admin.
- **Dynamic Form Builder:** Build custom payment forms and embed them anywhere with a shortcode.
- **Transaction Logging:** All payments are logged as custom post types for easy review.
- **Customizable Payment Form:** Change form title, description, and button text from the admin.
- **Secure AJAX Logging:** Uses WordPress nonces for secure transaction logging.
- **No Coding Required:** Easy setup and integration.

---

## Installation

1. Upload the plugin files to the `/wp-content/plugins/1app-WP-Plugin` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **1app Payment Settings** in your WordPress admin menu.
4. Enter your **OneApp Public Key** in the settings page and save.

---

## Usage

### 1. **Frontend Payment Form**

To display a simple payment form on any page or post, use the shortcode:

```
[oneapp_payment_form]
```

### 2. **Dynamic Form Builder**

- Go to **1app Form Builder** under the 1app Payment Settings menu.
- Create a new form and configure your fields.
- After saving, copy the generated shortcode (e.g. `[oneapp_payment_form id="123"]`) and paste it into any page or post.

### 3. **Direct Admin Payment**

- Go to **Direct Payment Form** under the 1app Payment Settings menu.
- Fill out the form and submit payments directly from the admin dashboard.

### 4. **Transaction Log**

- All transactions are logged under **1app Transactions** (submenu of 1app Payment Settings).
- The log is view-only; to add a payment, use the Direct Payment Form.

---

## Screenshots

1. **Settings Page:** Enter your OneApp public key.
2. **Direct Payment Form:** Accept payments from the admin.
3. **Dynamic Form Builder:** Build and manage custom payment forms.
4. **Transaction Log:** View all payment transactions.

---

## Frequently Asked Questions

**Q: Does this work with WPForms, Gravity Forms, or Contact Form 7?**  
A: Yes! You can use the universal handler with any form plugin or your own custom forms.

**Q: How do I view payment logs?**  
A: Go to **1app Transactions** under the 1app Payment Settings menu.

**Q: Can I customize the payment form?**  
A: Yes, you can change the title, description, and button text from the admin.

**Q: How are transactions logged?**  
A: Each payment is saved as a custom post type (`oneapp_transaction`) with all relevant details.

---

## Changelog

### 2.0.0
- Initial public release.
- Universal payment handler for all forms.
- Admin direct payment and transaction log.
- Dynamic form builder.

---

## License

This plugin is licensed under the GPLv2 or later.

---

## Support

For support, please contact the plugin author or open an issue on GitHub.

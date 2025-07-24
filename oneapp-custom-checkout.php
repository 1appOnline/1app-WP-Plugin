<?php
require_once plugin_dir_path(__FILE__) . 'oneapp-form-builder.php';
/**
 * Plugin Name: OneApp Universal Payment Handler
 * Description: Universal Oneapp payment handler for any WordPress form (WPForms, Gravity Forms, Contact Form 7, etc).
 * Version: 2.0.0
 * Author: Alexander Bamidele
 * Contributor: Adedayo Adejumo
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// --- Settings Page for Public Key ---
function oneapp_custom_add_settings_menu() {
    add_menu_page(
        '1app Payment Settings',
        '1app Payment Settings',
        'manage_options',
        'oneapp-settings',
        'oneapp_custom_settings_page'
    );
}
// Ensure the settings page is always visible
add_action('admin_menu', function() {
    add_submenu_page(
        'oneapp-settings',
        '1app Payment Settings',
        __('Settings', 'oneapp'),
        'manage_options',
        'oneapp-settings',
        'oneapp_custom_settings_page'
    );
}, 11);
add_action('admin_menu', 'oneapp_custom_add_settings_menu');

function oneapp_custom_settings_page() {
    ?>
    <div class="wrap">
        <h2><?php _e('1app Settings', 'oneapp'); ?></h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('oneapp_settings_group');
            do_settings_sections('oneapp-settings');
            submit_button();
            ?>
        </form>
        <hr>
        <h3><?php _e('Frontend Payment Form Shortcode', 'oneapp'); ?></h3>
        <p>
            <?php _e('To display the payment form on any page or post, use this shortcode:', 'oneapp'); ?><br>
            <code>[oneapp_payment_form]</code>
        </p>
    </div>
    <?php
}

function oneapp_custom_register_settings() {
    register_setting('oneapp_settings_group', 'oneapp_public_key');
    add_settings_section('oneapp_main_section', __('Main Settings', 'oneapp'), null, 'oneapp-settings');
    add_settings_field('oneapp_public_key', __('Public Key', 'oneapp'), 'oneapp_custom_public_key_callback', 'oneapp-settings', 'oneapp_main_section');
}
add_action('admin_init', 'oneapp_custom_register_settings');

function oneapp_custom_public_key_callback() {
    $public_key = get_option('oneapp_public_key', '');
    echo '<input type="text" name="oneapp_public_key" value="' . esc_attr($public_key) . '" class="regular-text">';
}

// --- Admin Customizable Payment Form Page ---
function oneapp_custom_add_payment_form_menu() {
    add_submenu_page(
        'oneapp-settings',
        'Direct Payment Form',
        __('Direct Payment Form', 'oneapp'),
        'manage_options',
        'oneapp-direct-payment',
        'oneapp_custom_direct_payment_form_page'
    );
}
add_action('admin_menu', 'oneapp_custom_add_payment_form_menu');

// Register settings for form customization
function oneapp_custom_register_form_settings() {
    register_setting('oneapp_form_settings_group', 'oneapp_form_title');
    register_setting('oneapp_form_settings_group', 'oneapp_form_desc');
    register_setting('oneapp_form_settings_group', 'oneapp_form_btn');
    add_settings_section('oneapp_form_section', __('Form Customization', 'oneapp'), null, 'oneapp-direct-payment');
    add_settings_field('oneapp_form_title', __('Form Title', 'oneapp'), 'oneapp_form_title_cb', 'oneapp-direct-payment', 'oneapp_form_section');
    add_settings_field('oneapp_form_desc', __('Form Description', 'oneapp'), 'oneapp_form_desc_cb', 'oneapp-direct-payment', 'oneapp_form_section');
    add_settings_field('oneapp_form_btn', __('Button Text', 'oneapp'), 'oneapp_form_btn_cb', 'oneapp-direct-payment', 'oneapp_form_section');
}
add_action('admin_init', 'oneapp_custom_register_form_settings');

function oneapp_form_title_cb() {
    echo '<input type="text" name="oneapp_form_title" value="' . esc_attr(get_option('oneapp_form_title', __('Make a Payment', 'oneapp'))) . '" class="regular-text">';
}
function oneapp_form_desc_cb() {
    echo '<textarea name="oneapp_form_desc" class="large-text" rows="2">' . esc_textarea(get_option('oneapp_form_desc', __('Fill the form below to make a payment.', 'oneapp'))) . '</textarea>';
}
function oneapp_form_btn_cb() {
    echo '<input type="text" name="oneapp_form_btn" value="' . esc_attr(get_option('oneapp_form_btn', __('Pay Now', 'oneapp'))) . '" class="regular-text">';
}

// Render the admin payment form page
function oneapp_custom_direct_payment_form_page() {
    ?>
    <div class="wrap">
        <h2><?php _e('Direct Payment Form', 'oneapp'); ?></h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('oneapp_form_settings_group');
            do_settings_sections('oneapp-direct-payment');
            submit_button(__('Save Form Settings', 'oneapp'));
            ?>
        </form>
        <hr>
        <h3><?php echo esc_html(get_option('oneapp_form_title', __('Make a Payment', 'oneapp'))); ?></h3>
        <p><?php echo esc_html(get_option('oneapp_form_desc', __('Fill the form below to make a payment.', 'oneapp'))); ?></p>
        <form id="oneapp-direct-payment-form" onsubmit="return oneappDirectPaymentSubmit(event)">
            <input type="text" name="fname" placeholder="<?php esc_attr_e('First Name', 'oneapp'); ?>" required class="regular-text"><br><br>
            <input type="text" name="lname" placeholder="<?php esc_attr_e('Last Name', 'oneapp'); ?>" required class="regular-text"><br><br>
            <input type="email" name="email" placeholder="<?php esc_attr_e('Email', 'oneapp'); ?>" required class="regular-text"><br><br>
            <input type="text" name="phone" placeholder="<?php esc_attr_e('Phone (optional)', 'oneapp'); ?>" class="regular-text"><br><br>
            <input type="number" name="amount" placeholder="<?php esc_attr_e('Amount (NGN)', 'oneapp'); ?>" required class="regular-text"><br><br>
            <button type="submit" class="button button-primary"><?php echo esc_html(get_option('oneapp_form_btn', __('Pay Now', 'oneapp'))); ?></button>
        </form>
        <div id="oneapp-payment-result"></div>
        <script>
function oneappDirectPaymentSubmit(e) {
    e.preventDefault();
    var form = document.getElementById('oneapp-direct-payment-form');
    var data = {
        fname: form.fname.value,
        lname: form.lname.value,
        email: form.email.value,
        phone: form.phone.value,
        amount: form.amount.value,
        reference: 'DASHBOARD_' + Date.now(),
        onSuccess: function(resp) {
            // Log transaction
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    action: 'oneapp_log_transaction',
                    reference: resp.reference || data.reference,
                    amount: data.amount,
                    email: data.email,
                    fname: data.fname,
                    lname: data.lname,
                    status: resp.status || 'success',
                    _ajax_nonce: (typeof oneappLogTxn !== 'undefined') ? oneappLogTxn.nonce : ''
                })
            })
            .then(response => response.json())
            .then(function(log) {
                if (!log.success) {
                    document.getElementById('oneapp-payment-result').innerHTML = '<div style="color:red;"><?php echo esc_js(__('Could not log transaction. Please contact support.', 'oneapp')); ?></div>';
                } else {
                    document.getElementById('oneapp-payment-result').innerHTML = '<div style="color:green;"><?php echo esc_js(__('Payment successful!', 'oneapp')); ?></div>';
                }
            })
            .catch(function(error) {
                document.getElementById('oneapp-payment-result').innerHTML = '<div style="color:red;"><?php echo esc_js(__('Could not log transaction. Please contact support.', 'oneapp')); ?></div>';
            });
        },
        onFail: function(resp) {
            document.getElementById('oneapp-payment-result').innerHTML = '<div style="color:red;"><?php echo esc_js(__('Payment failed. Please try again.', 'oneapp')); ?></div>';
        }
    };
    window.oneappMakePayment(data);
    return false;
}
</script>
        <?php echo do_shortcode('[oneapp_payment_js]'); ?>
    </div>
    <?php
}

// --- Register Custom Post Type for Transactions ---
add_action('init', function() {
    register_post_type('oneapp_transaction', [
        'labels' => [
            'name' => __('1app Transactions', 'oneapp'),
            'singular_name' => __('Transaction', 'oneapp'),
            'menu_name' => __('1app Transactions', 'oneapp'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'oneapp-settings',
        'supports' => ['title'],
        'menu_icon' => 'dashicons-list-view'
    ]);
});

// --- Enqueue Nonce for AJAX Logging ---
add_action('wp_enqueue_scripts', function() {
    if (is_singular() || is_page()) {
        wp_localize_script('jquery', 'oneappLogTxn', [
            'nonce' => wp_create_nonce('oneapp_log_transaction')
        ]);
    }
});

// --- AJAX Handler to Log Transactions ---
add_action('wp_ajax_oneapp_log_transaction', 'oneapp_log_transaction');
add_action('wp_ajax_nopriv_oneapp_log_transaction', 'oneapp_log_transaction');
function oneapp_log_transaction() {
    if ( ! isset($_POST['_ajax_nonce']) || ! wp_verify_nonce($_POST['_ajax_nonce'], 'oneapp_log_transaction') ) {
        wp_send_json(['success' => false, 'error' => __('Security check failed.', 'oneapp')]);
    }
    $data = $_POST;
    $title = 'Txn: ' . sanitize_text_field($data['reference'] ?? uniqid());
    $post_id = wp_insert_post([
        'post_type' => 'oneapp_transaction',
        'post_title' => $title,
        'post_status' => 'publish'
    ]);
    if ($post_id) {
        foreach ($data as $key => $value) {
            if ($key === 'amount') {
                update_post_meta($post_id, $key, floatval($value));
            } elseif ($key === 'email') {
                update_post_meta($post_id, $key, sanitize_email($value));
            } else {
                update_post_meta($post_id, $key, sanitize_text_field($value));
            }
        }
        wp_send_json(['success' => true]);
    }
    wp_send_json(['success' => false]);
}

// --- Frontend Payment Form Shortcode ---
function oneapp_payment_form_shortcode() {
    ob_start();
    ?>
    <form id="oneapp-frontend-payment-form" onsubmit="return oneappFrontendPaymentSubmit(event)">
        <input type="text" name="fname" placeholder="<?php esc_attr_e('First Name', 'oneapp'); ?>" required>
        <input type="text" name="lname" placeholder="<?php esc_attr_e('Last Name', 'oneapp'); ?>" required>
        <input type="email" name="email" placeholder="<?php esc_attr_e('Email', 'oneapp'); ?>" required>
        <input type="text" name="phone" placeholder="<?php esc_attr_e('Phone (optional)', 'oneapp'); ?>">
        <input type="number" name="amount" placeholder="<?php esc_attr_e('Amount (NGN)', 'oneapp'); ?>" required>
        <button type="submit"><?php esc_html_e('Pay Now', 'oneapp'); ?></button>
    </form>
    <div id="oneapp-frontend-payment-result"></div>
    <script>
    function oneappFrontendPaymentSubmit(e) {
        e.preventDefault();
        var form = document.getElementById('oneapp-frontend-payment-form');
        var data = {
            fname: form.fname.value,
            lname: form.lname.value,
            email: form.email.value,
            phone: form.phone.value,
            amount: form.amount.value,
            reference: 'FRONTEND_' + Date.now(),
            onSuccess: function(resp) {
                document.getElementById('oneapp-frontend-payment-result').innerHTML = '<div style="color:green;"><?php echo esc_js(__('Payment successful!', 'oneapp')); ?></div>';
            },
            onFail: function(resp) {
                document.getElementById('oneapp-frontend-payment-result').innerHTML = '<div style="color:red;"><?php echo esc_js(__('Payment failed. Please try again.', 'oneapp')); ?></div>';
            }
        };
        window.oneappMakePayment(data);
        return false;
    }
    </script>
    <?php echo do_shortcode('[oneapp_payment_js]'); ?>
    <?php
    return ob_get_clean();
}
add_shortcode('oneapp_payment_form', 'oneapp_payment_form_shortcode');

// --- Shortcode to Inject JS Handler ---
function oneapp_payment_js_shortcode() {
    $publickey = esc_js(get_option('oneapp_public_key', ''));
    ob_start();
    ?>
    <script src="https://js.oneappgo.com/v1/checkout.js"></script>
    <script>
    window.oneappMakePayment = function(opts) {
        // opts: {fname, lname, email, phone (optional), amount, reference, onSuccess, onFail}
        var intipay = new OneAppCheckout({
            publickey: '<?php echo $publickey; ?>',
            amount: parseInt(opts.amount),
            fname: opts.fname || '',
            lname: opts.lname || '',
            customer_email: opts.email || '',
            phone: opts.phone || '',
            reference: opts.reference || ('WPFORM_' + Date.now()),
            currency: 'NGN',
            onComplete: function(response) {
                if (
                    response.status &&
                    (
                        response.responsecode == '01' ||
                        (response.message && response.message.toLowerCase().includes('transaction completed'))
                    )
                ) {
                    if (typeof opts.onSuccess === 'function') opts.onSuccess(response);
                } else {
                    if (typeof opts.onFail === 'function') opts.onFail(response);
                }
            }
        });
        intipay.makePayment();
    }
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('oneapp_payment_js', 'oneapp_payment_js_shortcode');
add_action('admin_head', function() {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'oneapp_transaction') {
        // Hide the "Add New" button
        echo '<style>.post-type-oneapp_transaction .page-title-action { display:none !important; }</style>';
    }
});
add_action('load-post-new.php', function() {
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'oneapp_transaction') {
        wp_redirect(admin_url('admin.php?page=oneapp-direct-payment'));
        exit;
    }
});
add_action('admin_enqueue_scripts', function() {
    wp_localize_script('jquery', 'oneappLogTxn', [
        'nonce' => wp_create_nonce('oneapp_log_transaction')
    ]);
});
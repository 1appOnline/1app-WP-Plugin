<?php
/**
 * Plugin Name: OneApp Custom Checkout Integration
 * Description: Integrates 1app's inline popup checkout with custom HTML form.
 * Version: 1.0
 * Author: Your Name
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Create a custom menu page for the plugin
function oneapp_custom_checkout_menu() {
    add_menu_page(
        '1app Custom Checkout',
        '1app Custom Checkout',
        'manage_options',
        'oneapp-custom-checkout',
        'oneapp_custom_checkout_page',
        'dashicons-cart', // Icon
        6
    );
}
add_action( 'admin_menu', 'oneapp_custom_checkout_menu' );

function oneapp_enqueue_bootstrap() {
  
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');

   
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'oneapp_enqueue_bootstrap');

// The HTML page with custom form for testing
function oneapp_custom_checkout_page() {
    ?>
    <div class="wrap">
        <h2>1app Custom Checkout Test</h2>
        <p>Test the 1app payment gateway with custom input fields.</p>

        <form id="custom-checkout-form">
            <label for="fname">First Name</label>
            <input type="text" id="fname" name="fname" required><br>

            <label for="lname">Last Name</label>
            <input type="text" id="lname" name="lname" required><br>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required><br>

            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" required><br>

            <label for="amount">Amount (e.g. 20000)</label>
            <input type="number" id="amount" name="amount" required><br>

            <button type="button" onclick="makePayment()">Make Payment</button>
        </form>
    </div>

    <script src="https://js.oneappgo.com/v1/checkout.js"></script>
    <script type="text/javascript">
    function makePayment() {
    
        const fname = document.getElementById('fname').value;
        const lname = document.getElementById('lname').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const amount = parseInt(document.getElementById('amount').value);

        const publickey = '<?php echo esc_js(get_option('oneapp_public_key')); ?>';

        const intipay = new OneAppCheckout({
            publickey: publickey,  
            amount: amount,
            fname: fname,
            lname: lname,
            customer_email: email,
            phone: phone,
            reference: 'TEST_' + Date.now(),
            currency: 'NGN',
            onComplete: function(response) {
                if (response.status && response.responsecode == '01') {
                    alert('✅ Payment Successful! Reference: ' + response.reference);
                } else {
                    alert('❌ Payment Failed: ' + response.message);
                }
            }
        });

        intipay.makePayment();
    }
    </script>
    <?php
}

// Register a shortcode to display the custom checkout form
function oneapp_custom_checkout_form_shortcode() {
    ob_start();  

    ?>

    <div class="">
    <div class="row" style="box-shadow:0 0 50px 1px rgba(46, 45, 45, 0.5);">
    <div class="hero mb-12 col-12 mt-4" style="font-size:1.5rem;">Fill the details below</div>
        <div class="col-6 col-md-4 mt-4 container">
        <div class="card shadow-sm" style="box-shadow:0 0 15px rgba(0,0,0,0.5);height:7rem;">
        <center>
        <img src="<?php echo plugins_url('images/1app.png', __FILE__); ?>" 
            style="width:6rem;"
            class="img-fluid mt-4">
        </center>
        </div>
    </div>
        <div class="col-12 col-sm-6 col-md-8 mb-4">
        <div class="card" style="border:0;">
        <br>
        <form id="custom-checkout-form" class="container">
        
        <div class="form-row">
        <div class="form-group col-md-6 mb-4">    
            <input type="text" id="fname" name="fname" required class="form-control" 
            style="border-radius:0.5rem;height:3rem;" placeholder="First Name">
        </div>
        <div class="form-group col-md-6 mb-4"> 
            <input type="text" id="lname" name="lname" required class="form-control" style="border-radius:0.5rem;height:3rem;" placeholder="Last Name">
        </div>
        <div class="form-group col-md-6 mb-4 mt-2"> 
            <input type="email" id="email" name="email" required class="form-control" style="border-radius:0.5rem;height:3rem;" placeholder="Email">
        </div>
        <div class="form-group col-md-6 mb-4 mt-2"> 
            <input type="text" id="phone" name="phone" required class="form-control" style="border-radius:0.5rem;height:3rem;" placeholder="Phone">
        </div>
        <div class="form-group col-md-12 mb-4 mt-2">
            <input type="number" id="amount" name="amount" required class="form-control" style="border-radius:0.5rem;height:3rem;" placeholder="Amount (e.g. 20000)">
        </div>
        <div class="form-group col-md-12 mt-4">
        <button type="button" onclick="makePayment()" style="border-radius:0.5rem;height:3rem;background-color: #ab005e;border:none;" class="btn btn-primary btn-block">Make Payment</button>
        </div>
        
        </div>
        </form>
        <br>
        </div>
    </div>
    </div>

    </div>
    <script src="https://js.oneappgo.com/v1/checkout.js"></script>
    <script type="text/javascript">
    function makePayment() {
    
        const fname = document.getElementById('fname').value;
        const lname = document.getElementById('lname').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const amount = parseInt(document.getElementById('amount').value);
        const publickey = '<?php echo esc_js(get_option('oneapp_public_key')); ?>';

        const intipay = new OneAppCheckout({
            publickey: publickey,  // Public key
            amount: amount,
            fname: fname,
            lname: lname,
            customer_email: email,
            phone: phone,
            reference: 'TEST_' + Date.now(),
            currency: 'NGN',
            onComplete: function(response) {
                if (response.status && response.responsecode == '01') {
                    alert('✅ Payment Successful! Reference: ' + response.reference);
                } else {
                    alert('❌ Payment Failed: ' + response.message);
                }
            }
        });

        intipay.makePayment();
    }
    </script>
    <?php
    return ob_get_clean(); 
}

// Register the shortcode
add_shortcode('oneapp_checkout_form', 'oneapp_custom_checkout_form_shortcode');

// Add settings page for 1app public key
function oneapp_custom_add_settings_menu() {
    add_menu_page(
        '1app Custom Checkout Settings',
        '1app Custom Checkout Settings',
        'manage_options',
        'oneapp-settings',
        'oneapp_custom_settings_page'
    );
}
add_action('admin_menu', 'oneapp_custom_add_settings_menu');

function oneapp_custom_settings_page() {
    ?>
    <div class="wrap">
        <h2>1app Payment Gateway Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('oneapp_settings_group');
            do_settings_sections('oneapp-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function oneapp_custom_register_settings() {
    register_setting('oneapp_settings_group', 'oneapp_public_key');
    add_settings_section('oneapp_main_section', 'Main Settings', null, 'oneapp-settings');
    add_settings_field('oneapp_public_key', 'Public Key', 'oneapp_custom_public_key_callback', 'oneapp-settings', 'oneapp_main_section');
}
add_action('admin_init', 'oneapp_custom_register_settings');

function oneapp_custom_public_key_callback() {
    $public_key = get_option('oneapp_public_key', '');
    echo '<input type="text" name="oneapp_public_key" value="' . esc_attr($public_key) . '" class="regular-text">';
}

add_action('rest_api_init', function () {
    register_rest_route('oneapp/v1', '/public-key', array(
        'methods' => 'GET',
        'callback' => function () {
            return ['publickey' => get_option('oneapp_public_key')];
        },
        'permission_callback' => '__return_true'
    ));
});

?>

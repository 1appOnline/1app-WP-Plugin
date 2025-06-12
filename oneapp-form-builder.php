<?php
/**
 * Plugin Name: 1app Form Builder
 * Description: Custom form builder for 1app payment forms.
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// --- Register Custom Post Type for Forms ---
add_action('init', function() {
    register_post_type('oneapp_form', [
        'labels' => [
            'name'               => '1app Forms',
            'singular_name'      => 'Form',
            'add_new'            => 'Add Form',
            'add_new_item'       => 'Add New Form',
            'edit_item'          => 'Edit Form',
            'new_item'           => 'New Form',
            'view_item'          => 'View Form',
            'search_items'       => 'Search Forms',
            'not_found'          => 'No forms found',
            'not_found_in_trash' => 'No forms found in Trash',
            'all_items'          => 'All Forms',
            'menu_name'          => '1app Forms',
            'name_admin_bar'     => 'Form',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'supports' => ['title'],
        'menu_icon' => 'dashicons-feedback'
    ]);
});

// --- Add Form Builder to Admin Menu ---
add_action('admin_menu', function() {
    add_submenu_page(
        'oneapp-settings',
        '1app Form Builder',
        '1app Form Builder',
        'manage_options',
        'edit.php?post_type=oneapp_form'
    );
});

// --- Add Meta Box for Form Fields ---
add_action('add_meta_boxes', function() {
    add_meta_box('oneapp_form_fields', 'Form Fields', 'oneapp_form_fields_metabox_cb', 'oneapp_form', 'normal', 'high');
});
function oneapp_form_fields_metabox_cb($post) {
    // Show shortcode and instructions
    ?>
    <div style="background:#f8f8f8;padding:10px;border:1px solid #e1e1e1;margin-bottom:15px;">
        <strong>To display this form on any page or post, use this shortcode:</strong><br>
        <code>[oneapp_payment_form id="<?php echo esc_attr($post->ID); ?>"]</code>
        <br>
        <small>
            Copy and paste the shortcode above into any page, post, or widget where you want this form to appear.<br>
            <b>Validation:</b> For payment to work, your form must include at least <b>Amount</b> (type: number), <b>Email</b> (type: email), <b>First Name</b> (type: text, label: fname), and <b>Last Name</b> (type: text, label: lname).<br>
            The form will use your theme's default styles.
        </small>
    </div>
    <?php
    $fields = get_post_meta($post->ID, '_oneapp_form_fields', true);
    if (!is_array($fields)) $fields = [];
    ?>
    <style>
        .oneapp-field-row { margin-bottom: 10px; padding: 8px; background: #f9f9f9; border: 1px solid #eee; }
        .oneapp-field-row input, .oneapp-field-row select { margin-right: 8px; }
    </style>
    <div id="oneapp-fields-list">
        <?php foreach ($fields as $i => $field): ?>
            <div class="oneapp-field-row">
                <select name="oneapp_fields[<?php echo $i; ?>][type]">
                    <option value="text" <?php selected($field['type'], 'text'); ?>>Text</option>
                    <option value="email" <?php selected($field['type'], 'email'); ?>>Email</option>
                    <option value="number" <?php selected($field['type'], 'number'); ?>>Number</option>
                    <option value="phone" <?php selected($field['type'], 'phone'); ?>>Phone</option>
                    <option value="url" <?php selected($field['type'], 'url'); ?>>URL</option>
                    <option value="checkbox" <?php selected($field['type'], 'checkbox'); ?>>Checkbox</option>
                    <option value="radio" <?php selected($field['type'], 'radio'); ?>>Radio</option>
                    <option value="dropdown" <?php selected($field['type'], 'dropdown'); ?>>Dropdown</option>
                    <option value="file" <?php selected($field['type'], 'file'); ?>>File</option>
                    <option value="label" <?php selected($field['type'], 'label'); ?>>Label</option>
                    <option value="address" <?php selected($field['type'], 'address'); ?>>Address</option>
                </select>
                <input type="text" name="oneapp_fields[<?php echo $i; ?>][label]" value="<?php echo esc_attr($field['label']); ?>" placeholder="Label/Title">
                <input type="text" name="oneapp_fields[<?php echo $i; ?>][options]" value="<?php echo esc_attr($field['options'] ?? ''); ?>" placeholder="Options (comma separated)">
                <label><input type="checkbox" name="oneapp_fields[<?php echo $i; ?>][required]" value="1" <?php checked(!empty($field['required'])); ?>> Required</label>
                <button type="button" onclick="this.parentNode.remove();">Remove</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button" onclick="oneappAddField();">Add Field</button>
    <script>
    function oneappAddField() {
        var i = document.querySelectorAll('#oneapp-fields-list > .oneapp-field-row').length;
        var html = `<div class="oneapp-field-row">
            <select name="oneapp_fields[`+i+`][type]">
                <option value="text">Text</option>
                <option value="email">Email</option>
                <option value="number">Number</option>
                <option value="phone">Phone</option>
                <option value="url">URL</option>
                <option value="checkbox">Checkbox</option>
                <option value="radio">Radio</option>
                <option value="dropdown">Dropdown</option>
                <option value="file">File</option>
                <option value="label">Label</option>
                <option value="address">Address</option>
            </select>
            <input type="text" name="oneapp_fields[`+i+`][label]" placeholder="Label/Title">
            <input type="text" name="oneapp_fields[`+i+`][options]" placeholder="Options (comma separated)">
            <label><input type="checkbox" name="oneapp_fields[`+i+`][required]" value="1"> Required</label>
            <button type="button" onclick="this.parentNode.remove();">Remove</button>
        </div>`;
        document.getElementById('oneapp-fields-list').insertAdjacentHTML('beforeend', html);
    }
    </script>
    <p style="margin-top:10px;color:#666;">Field types like <b>radio</b> and <b>dropdown</b> use the "Options" box (comma separated). "Label" is just a heading/description. "Address" is a textarea.</p>
    <?php
}
add_action('save_post_oneapp_form', function($post_id) {
    if (isset($_POST['oneapp_fields'])) {
        update_post_meta($post_id, '_oneapp_form_fields', $_POST['oneapp_fields']);
    }
});

// --- Show Shortcode After Save ---
add_filter('post_updated_messages', function($messages) {
    global $post;
    if ($post && $post->post_type == 'oneapp_form') {
        $messages['oneapp_form'][1] .= '<br>Shortcode: <code>[oneapp_payment_form id="'.$post->ID.'"]</code>';
    }
    return $messages;
});

// --- AJAX File Upload Handler with Nonce ---
add_action('wp_ajax_oneapp_upload_file', 'oneapp_handle_file_upload');
add_action('wp_ajax_nopriv_oneapp_upload_file', 'oneapp_handle_file_upload');
function oneapp_handle_file_upload() {
    if (
        !isset($_POST['oneapp_upload_file_nonce']) ||
        !wp_verify_nonce($_POST['oneapp_upload_file_nonce'], 'oneapp_upload_file')
    ) {
        wp_send_json(['success' => false, 'error' => 'Security check failed.']);
    }
    if (!empty($_FILES)) {
        $file = $_FILES[array_key_first($_FILES)];
        $allowed = ['jpg','jpeg','png','pdf','doc','docx','txt'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            wp_send_json(['success' => false, 'error' => 'File type not allowed.']);
        }
        $upload = wp_handle_upload($file, ['test_form' => false]);
        if (!empty($upload['url'])) {
            wp_send_json(['success' => true, 'file_url' => $upload['url']]);
        }
        wp_send_json(['success' => false, 'error' => 'Upload failed.']);
    }
    wp_send_json(['success' => false, 'error' => 'No file found.']);
}

// --- Frontend Dynamic Form Shortcode ---
function oneapp_dynamic_payment_form_shortcode($atts) {
    $atts = shortcode_atts(['id' => 0], $atts);
    $fields = get_post_meta($atts['id'], '_oneapp_form_fields', true);
    if (!$fields) return 'Form not found.';
    ob_start();
    ?>
    <form id="oneapp-dyn-payment-form-<?php echo esc_attr($atts['id']); ?>" enctype="multipart/form-data" onsubmit="return oneappDynPaymentSubmit<?php echo esc_attr($atts['id']); ?>(event)">
        <?php wp_nonce_field('oneapp_upload_file', 'oneapp_upload_file_nonce'); ?>
        <?php foreach ($fields as $field): ?>
            <?php
            $label = esc_html($field['label']);
            $name = sanitize_title($label);
            $required = !empty($field['required']) ? 'required' : '';
            switch ($field['type']) {
                case 'text':
                case 'email':
                case 'number':
                case 'phone':
                case 'url':
                    echo "<label>$label</label><input type='{$field['type']}' name='$name' $required><br>";
                    break;
                case 'checkbox':
                    echo "<label><input type='checkbox' name='$name'> $label</label><br>";
                    break;
                case 'radio':
                    if (!empty($field['options'])) {
                        foreach (explode(',', $field['options']) as $opt) {
                            $opt = trim($opt);
                            echo "<label><input type='radio' name='$name' value='$opt'> $opt</label> ";
                        }
                    }
                    echo "<br>";
                    break;
                case 'dropdown':
                    echo "<label>$label</label><select name='$name'>";
                    if (!empty($field['options'])) {
                        foreach (explode(',', $field['options']) as $opt) {
                            $opt = trim($opt);
                            echo "<option value='$opt'>$opt</option>";
                        }
                    }
                    echo "</select><br>";
                    break;
                case 'file':
                    echo "<label>$label</label><input type='file' name='$name'><br>";
                    break;
                case 'label':
                    echo "<strong>$label</strong><br>";
                    break;
                case 'address':
                    echo "<label>$label</label><textarea name='$name' $required></textarea><br>";
                    break;
            }
            ?>
        <?php endforeach; ?>
        <button type="submit">Pay Now</button>
    </form>
    <div id="oneapp-dyn-payment-result-<?php echo esc_attr($atts['id']); ?>"></div>
    <script>
    function oneappDynPaymentSubmit<?php echo esc_attr($atts['id']); ?>(e) {
        e.preventDefault();
        var form = document.getElementById('oneapp-dyn-payment-form-<?php echo esc_attr($atts['id']); ?>');
        var formData = new FormData(form);

        // Extract payment fields (customize as needed)
        var amount = formData.get('amount') || 0;
        var email = formData.get('email') || '';
        var fname = formData.get('fname') || '';
        var lname = formData.get('lname') || '';

        // Handle file upload via AJAX if file field exists
        var fileInput = form.querySelector('input[type="file"]');
        if (fileInput && fileInput.files.length > 0) {
            formData.append('action', 'oneapp_upload_file');
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    document.getElementById('oneapp-dyn-payment-result-<?php echo esc_attr($atts['id']); ?>').innerHTML = '<div style="color:red;">File upload error: ' + (data.error || 'Unknown error') + '</div>';
                    return;
                }
                // After file upload, proceed to payment
                oneappMakePaymentHandler<?php echo esc_attr($atts['id']); ?>(amount, email, fname, lname, data.file_url);
            })
            .catch(function(error) {
                document.getElementById('oneapp-dyn-payment-result-<?php echo esc_attr($atts['id']); ?>').innerHTML = '<div style="color:red;">File upload failed. Please try again.</div>';
            });
        } else {
            // No file, just payment
            oneappMakePaymentHandler<?php echo esc_attr($atts['id']); ?>(amount, email, fname, lname, '');
        }
        return false;
    }

    // Payment handler with transaction logging and nonce
    function oneappMakePaymentHandler<?php echo esc_attr($atts['id']); ?>(amount, email, fname, lname, file_url) {
        var resultDivId = 'oneapp-dyn-payment-result-<?php echo esc_attr($atts['id']); ?>';
        if(typeof window.oneappMakePayment !== 'function') {
            alert('Payment handler not loaded.');
            return;
        }
        window.oneappMakePayment({
            amount: amount,
            email: email,
            fname: fname,
            lname: lname,
            reference: 'DYNFORM_' + Date.now(),
            file_url: file_url,
            onSuccess: function(resp) {
                // Log transaction
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'oneapp_log_transaction',
                        reference: resp.reference || '',
                        amount: amount,
                        email: email,
                        fname: fname,
                        lname: lname,
                        status: resp.status || 'success',
                        _ajax_nonce: (typeof oneappLogTxn !== 'undefined') ? oneappLogTxn.nonce : ''
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        document.getElementById(resultDivId).innerHTML = '<div style="color:red;">Could not log transaction. Please contact support.</div>';
                    }
                })
                .catch(function(error) {
                    document.getElementById(resultDivId).innerHTML = '<div style="color:red;">Could not log transaction. Please contact support.</div>';
                });
                document.getElementById(resultDivId).innerHTML = '<div style="color:green;">Payment successful!</div>';
            },
            onFail: function(resp) {
                document.getElementById(resultDivId).innerHTML = '<div style="color:red;">Payment failed. Please try again.</div>';
            }
        });
    }
    </script>
    <?php echo do_shortcode('[oneapp_payment_js]'); ?>
    <?php
    return ob_get_clean();
}
add_shortcode('oneapp_payment_form', 'oneapp_dynamic_payment_form_shortcode');
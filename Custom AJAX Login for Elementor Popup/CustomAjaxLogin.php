<?php
/*
Plugin Name: Custom AJAX Login for Elementor Popup
Description: Handles AJAX login within Elementor popups without redirecting to the WordPress login page.
Version: 1.0
Author: Ilham Mohomed
Author URI: https://www.linkedin.com/in/ilham-mohomed/
*/

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle login failure to prevent redirection and handle via AJAX.
 *
 * @param string $username The username that failed to login.
 */
function custom_login_fail($username) {
    // Check if the request is via AJAX and coming from our custom action
    if (defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && sanitize_text_field($_POST['action']) === 'ajax_login') {
        wp_send_json_error(['message' => __('Invalid login credentials. Please try again.', 'text-domain')]);
    }
}
add_action('wp_login_failed', 'custom_login_fail');

/**
 * Handle the AJAX login request securely.
 */
function ajax_login_handler() {
    // Verify nonce for security
    check_ajax_referer('ajax-login-nonce', 'security');

    // Capture and sanitize the login data
    $info = [];
    $info['user_login'] = sanitize_user($_POST['log'], true);
    $info['user_password'] = sanitize_text_field($_POST['pwd']);
    $info['remember'] = isset($_POST['rememberme']) && $_POST['rememberme'] === 'true' ? true : false;

    // Attempt to sign the user in
    $user_signon = wp_signon($info, false);

    // Check if login is successful
if (is_wp_error($user_signon)) {
    $error_message = html_entity_decode($user_signon->get_error_message());
    wp_send_json_error(['message' => esc_html($error_message)]);
} else {
    wp_send_json_success(['message' => __('Login successful. Redirecting...', 'text-domain')]);
}


    wp_die();
}
add_action('wp_ajax_nopriv_ajax_login', 'ajax_login_handler');
add_action('wp_ajax_ajax_login', 'ajax_login_handler');

/**
 * Enqueue scripts with localized AJAX URL and nonce.
 */
function enqueue_ajax_login_script() {
    // Ensure jQuery is loaded
    wp_enqueue_script('jquery');

    // Localize script with AJAX URL and nonce
    wp_localize_script('jquery', 'ajax_login_object', array(
        'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
        'nonce'   => wp_create_nonce('ajax-login-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_login_script');

/**
 * Inject the JavaScript to handle form submission via AJAX securely.
 */
function ajax_login_init() {
    ?>
   <script type="text/javascript">
    jQuery(document).ready(function ($) {
        $(document).on('elementor/popup/show', function (event, id, instance) {
            // Replace '1734' with the actual ID of your specific popup
            if (id === 1734 || id === 1723) {
                console.log("Opened popup", id);
                const loginForm = instance.$element.find('.elementor-login');

                if (loginForm.length > 0) {
                    loginForm.on('submit', function (event) {
                        event.preventDefault(); // Prevent the default form submission

                        // Prepare the form data
                        const formData = new FormData(this);
                        formData.append('action', 'ajax_login');
                        formData.append('security', ajax_login_object.nonce); // Adding the nonce for security

                        fetch(ajax_login_object.ajaxurl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                window.location.reload(); // Reload the page on successful login
                            } else {
                               

                                // Display the error message inside the popup
let errorContainer = loginForm.find('.elementor-message-area');

if (errorContainer.length === 0) {
    errorContainer = $('<div class="elementor-message-area"></div>');
    loginForm.append(errorContainer);
}

// Decode the message to render any encoded HTML tags properly
let errorMessage = $('<div>').html(data.data.message || 'An error occurred. Please try again.').text();

// Check if the error message already starts with "Error:" to avoid double prefix
if (!errorMessage.toLowerCase().startsWith('error:')) {
    errorMessage = `<strong>Error:</strong> ${errorMessage}`;
} else {
    errorMessage = `<strong>${errorMessage}</strong>`;
}

// Update the error message display with improved readability and style
errorContainer.html(
    `<div class="elementor-message elementor-message-danger">
        ${errorMessage}
    </div>`
);


                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    });
                }
            }
        });
    });
</script>

    <?php
}
add_action('wp_footer', 'ajax_login_init', 100);

# Custom AJAX Login for Elementor Popup

This plugin provides a custom solution for handling login functionality within Elementor popups without redirecting to the default WordPress login page when users enter invalid credentials or try to log in without providing them. This solution works specifically for Elementor popups, ensuring that login is processed via AJAX and users remain within the popup interface instead of being redirected.

## Features
- Prevents redirection to the WordPress login page on login failure.
- Handles AJAX login requests for Elementor popups.
- Displays error messages dynamically inside the popup, improving user experience.
- Provides an easy way to implement AJAX login securely using nonces.

## Installation
1. Download or clone this repository.
2. Upload the plugin to the `wp-content/plugins/` directory of your WordPress installation.
3. Activate the plugin via the **Plugins** menu in WordPress.

## How It Works
This plugin intercepts login failures and processes login requests via AJAX when the login form is submitted inside an Elementor popup. When an invalid login attempt is made, it prevents the user from being redirected to the WordPress login page and displays error messages directly within the popup.

### For Popups
When using this on a popup, the plugin checks for the popup ID to handle login correctly. You need to ensure that the correct popup ID is included in the script to trigger the AJAX functionality. The popup ID is passed dynamically when the popup opens, and the login form submission is handled via AJAX.

The JavaScript code listens for Elementor popup events and binds the login form submission to an AJAX handler.

### For Pages (Without Popup)
If you are not using a popup but have a login form embedded on a page, you can adjust the implementation to bind directly to the login form on the page, rather than listening for the `elementor/popup/show` event. Here's how to modify the script for page-based login:

1. Find the section in the JavaScript file where the popup ID is checked:

   ```javascript
   $(document).on('elementor/popup/show', function (event, id, instance) {
       
   });
   
2. Replace this with code that directly targets the login form on the page, such as:
   ```javascript
   $(document).ready(function () {
    const loginForm = $('.elementor-login');
    if (loginForm.length > 0) {
        loginForm.on('submit', function (event) {
            // Handle AJAX login here
        });
    }
});

   
This will ensure that the login form submission is handled via AJAX even when the login form is embedded directly on a page and not inside a popup.

### How to Change the Popup ID

In the JavaScript section of the plugin, you will see a condition checking for specific popup IDs:


   if (id === 1734 || id === 1723) {
   
}

### To customize this for your own popups:
- Open the Elementor editor and edit the login popup.
- Note down the popup ID from the URL (the ID will be visible in the popup's URL when editing it).
- Replace the IDs in the JavaScript with your own popup IDs.

For example, if your popup ID is 1750, update the code like this:

if (id === 1750) {

}

## Usage
### Once the plugin is activated:
- The login form in Elementor popups will be handled via AJAX.
- When a user enters incorrect login details, an error message will appear inside the popup without reloading the page.
- On successful login, the page will reload to reflect the authenticated state.

## Author
Ilham Mohomed
[https://www.linkedin.com/in/ilham-mohomed/](https://www.linkedin.com/in/ilham-mohomed/)

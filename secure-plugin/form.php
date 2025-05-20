<h2>Secure Plugin Form</h2>

<?php
// var_dump(get_option('secure_form_submission')); 
// $Submission = get_option('secure_form_submission');
// echo '<h3>Form Submitted Successfully</h3>';
// echo '<p>Name: ' . esc_html($Submission['name']) . '</p>';
// echo '<p>Email: ' . esc_html($Submission['email']) . '</p>';
// echo '<p>Age: ' . esc_html($Submission['age']) . '</p>';
// echo '<p>Message: ' . esc_textarea($Submission['message']) . '</p>';
global $wp;
?>

<div id="secure-plugin-form-response"></div>

<form id="secure-plugin-form" method="post">
    <input type="text" id="name" name="name" placeholder="Your Name" required>
    <input type="text" id="email" name="email" placeholder="Your Email" required>
    <input type="text" id="age" name="age" placeholder="Your Age" required>
    <textarea id="message" name="message" placeholder="Your Message" required></textarea>
    <!-- <input type="hidden" name="redirect_url" value="<?php // echo home_url($wp->request); ?>"> -->
    <!-- <?php // wp_nonce_field('secure_plugin_nonce'); ?> -->
    <button type="submit" name="secure_form_submit" value="Submit">Submit</button>
</form>
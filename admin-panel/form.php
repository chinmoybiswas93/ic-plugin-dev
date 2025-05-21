<?php
$settings = get_option('admin_panel_settings');
?>

<form id="admin-panel-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="text" id="name" name="name" placeholder="Your Name" value="<?php echo $settings['name'] ?? '' ?>"
        required>
    <input type="text" id="email" name="email" placeholder="Your Email" value="<?php echo $settings['email'] ?? '' ?>"
        required>
    <input type="text" id="age" name="age" placeholder="Your Age" value="<?php echo $settings['age'] ?? '' ?>" required>
    <textarea id="message" name="message" placeholder="Your Message"
        required><?php echo $settings['message'] ?? '' ?></textarea>
    <input type="hidden" name="action" value="admin_panel_settings">
    <?php wp_nonce_field('admin_panel_nonce'); ?>
    <button type="submit" name="admin_panel_form_submit" value="Submit">Submit</button>
</form>
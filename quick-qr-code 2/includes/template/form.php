<?php
$settings = get_option('quick_qr_code_settings');
?>

<form id="quick-qr-code-settings-form" method="post" action="#" autocomplete="off">
    <?php wp_nonce_field('quick_qr_code_ajax_nonce', 'quick_qr_code_ajax_nonce_field'); ?>
    <div class="form-group">
        <label for="qr-code-label">QR Code Label</label>
        <input type="text" id="qr-code-label" name="qr-code-label" placeholder="QR Code Label"
            value="<?php echo esc_attr($old['qr-code-label'] ?? $settings['qr-code-label'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label for="qr-code-size">QR Code Size (px)</label>
        <input type="number" id="qr-code-size" name="qr-code-size" min="50" max="1000"
            value="<?php echo esc_attr($settings['qr-code-size'] ?? 120); ?>">
    </div>
    <div class="form-group">
        <label for="qr-code-margin">QR Code Margin</label>
        <input type="number" id="qr-code-margin" name="qr-code-margin" min="0" max="20"
            value="<?php echo esc_attr($settings['qr-code-margin'] ?? 2); ?>">
    </div>
    <div class="form-group">
        <label for="qr-code-dark">Dark Color (hex)</label>
        <input type="text" id="qr-code-dark" name="qr-code-dark" placeholder="000000"
            value="<?php echo esc_attr($settings['qr-code-dark'] ?? '000000'); ?>">
    </div>
    <div class="form-group">
        <label for="qr-code-light">Light Color (hex)</label>
        <input type="text" id="qr-code-light" name="qr-code-light" placeholder="ffffff"
            value="<?php echo esc_attr($settings['qr-code-light'] ?? 'ffffff'); ?>">
    </div>
    <div class="form-group">
        <label for="qr-code-logo-url">Logo URL (optional)</label>
        <input type="url" id="qr-code-logo-url" name="qr-code-logo-url" placeholder="https://example.com/logo.png"
            value="<?php echo esc_attr($settings['qr-code-logo-url'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label for="qr-code-logo-size">Logo Size (%)</label>
        <input type="number" id="qr-code-logo-size" name="qr-code-logo-size" min="10" max="100"
            value="<?php echo esc_attr($settings['qr-code-logo-size'] ?? 50); ?>">
    </div>
    <div class="form-group">
        <label for="qr-code-font-size">Label Font Size (e.g. 12px, 1em)</label>
        <input type="text" id="qr-code-font-size" name="qr-code-font-size" placeholder="12px"
            value="<?php echo esc_attr($settings['qr-code-font-size'] ?? '12px'); ?>">
    </div>
    <div class="form-group">
        <label for="qr-code-position">Position</label>
        <select id="qr-code-position" name="qr-code-position">
            <option value="right" <?php selected($settings['qr-code-position'] ?? 'right', 'right'); ?>>Right</option>
            <option value="left" <?php selected($settings['qr-code-position'] ?? '', 'left'); ?>>Left</option>
        </select>
    </div>
    <input type="hidden" name="action" value="quick_qr_code_settings">
    <div class="form-group">
        <button type="submit" name="quick_qr_code_form_submit" value="Submit">Submit</button>
    </div>
</form>
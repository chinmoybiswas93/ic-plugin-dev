<?php
$settings = get_option('quick_qr_code_settings');
?>

<div class="quick-qr-dashboard">
    <div class="dashboard-header sticky-header">
        <div class="header-left">
            <span class="qr-logo" style="display:inline-block;vertical-align:middle;margin-right:10px;">
                <!-- Simple QR icon SVG -->
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                    <rect x="2" y="2" width="8" height="8" rx="2" fill="#FFB900" />
                    <rect x="18" y="2" width="8" height="8" rx="2" fill="#FFB900" />
                    <rect x="2" y="18" width="8" height="8" rx="2" fill="#FFB900" />
                    <rect x="10" y="10" width="8" height="8" rx="2" fill="#222" />
                </svg>
            </span>
            <span class="plugin-title">Quick QR Code</span>
        </div>
        <div class="header-right">
            <button type="submit" form="quick-qr-code-settings-form" class="header-submit-btn">Save Settings</button>
        </div>
    </div>
    <div class="dashboard-cards">
        <div class="qqc-card">
            <form id="quick-qr-code-settings-form" method="post" action="#" autocomplete="off">
                <div class="qqc-form-row">
                    <label for="qr-code-label">QR Code Label</label>
                    <input type="text" id="qr-code-label" name="qr-code-label" placeholder="QR Code Label"
                        value="<?php echo esc_attr($old['qr-code-label'] ?? $settings['qr-code-label'] ?? ''); ?>">
                </div>
                <div class="qqc-form-row">
                    <label for="qr-code-size">QR Code Size (px)</label>
                    <input type="number" id="qr-code-size" name="qr-code-size" min="50" max="1000"
                        value="<?php echo esc_attr($settings['qr-code-size'] ?? 120); ?>">
                </div>
                <div class="qqc-form-row">
                    <label for="qr-code-margin">QR Code Margin</label>
                    <input type="number" id="qr-code-margin" name="qr-code-margin" min="0" max="20"
                        value="<?php echo esc_attr($settings['qr-code-margin'] ?? 2); ?>">
                </div>
                <div class="qqc-form-row qqc-form-row-colors">
                    <label>QR Code Colors</label>
                    <div class="qqc-color-cols">
                        <div>
                            <label for="qr-code-dark" class="qqc-color-label">Dark (hex)</label>
                            <input type="text" id="qr-code-dark" name="qr-code-dark" placeholder="000000"
                                value="<?php echo esc_attr($settings['qr-code-dark'] ?? '000000'); ?>">
                        </div>
                        <div>
                            <label for="qr-code-light" class="qqc-color-label">Light (hex)</label>
                            <input type="text" id="qr-code-light" name="qr-code-light" placeholder="ffffff"
                                value="<?php echo esc_attr($settings['qr-code-light'] ?? 'ffffff'); ?>">
                        </div>
                    </div>
                </div>
                <div class="qqc-form-row">
                    <label for="qr-code-logo-url">Logo URL (optional)</label>
                    <input type="url" id="qr-code-logo-url" name="qr-code-logo-url"
                        placeholder="https://example.com/logo.png"
                        value="<?php echo esc_attr($settings['qr-code-logo-url'] ?? ''); ?>">
                </div>
                <div class="qqc-form-row">
                    <label for="qr-code-logo-size">Logo Size (%)</label>
                    <input type="number" id="qr-code-logo-size" name="qr-code-logo-size" min="10" max="100"
                        value="<?php echo esc_attr($settings['qr-code-logo-size'] ?? 50); ?>">
                </div>
                <div class="qqc-form-row">
                    <label for="qr-code-font-size">Label Font Size</label>
                    <input type="text" id="qr-code-font-size" name="qr-code-font-size" placeholder="12px"
                        value="<?php echo esc_attr($settings['qr-code-font-size'] ?? '12px'); ?>">
                </div>
                <div class="qqc-form-row">
                    <label for="qr-code-position">Position</label>
                    <select id="qr-code-position" name="qr-code-position">
                        <option value="right" <?php selected($settings['qr-code-position'] ?? 'right', 'right'); ?>>
                            Right
                        </option>
                        <option value="left" <?php selected($settings['qr-code-position'] ?? '', 'left'); ?>>Left
                        </option>
                    </select>
                </div>
                <input type="hidden" name="action" value="quick_qr_code_settings">
            </form>
        </div>
        <div class="qqc-card qqc-status-card">
            <h2>Preview</h2>
            <div id="qr-code-preview"></div>
        </div>
    </div>
</div>
<?php
/**
 * Settings Page
 */

if (!defined('ABSPATH')) {
    exit;
}

class EHEP_Settings {

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_submenu_page(
            'elementor',
            esc_html__('Hover Effects Settings', 'elementor-hover-effects'),
            esc_html__('Hover Effects', 'elementor-hover-effects'),
            'manage_options',
            'elementor-hover-effects',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('ehep_settings', 'ehep_enable_global');
        register_setting('ehep_settings', 'ehep_enable_mobile');
        register_setting('ehep_settings', 'ehep_performance_mode');
        register_setting('ehep_settings', 'ehep_debug_mode');
        register_setting('ehep_settings', 'ehep_effect_caching');
        register_setting('ehep_settings', 'ehep_lazy_load');
        register_setting('ehep_settings', 'ehep_minify_output');
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error('ehep_messages', 'ehep_message', esc_html__('Settings Saved', 'elementor-hover-effects'), 'updated');
        }

        settings_errors('ehep_messages');
        ?>
        <style>
            .ehep-settings-container {
                max-width: 900px;
                margin: 20px auto 0 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                background: #fdfdfd;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                border: 1px solid #e2e8f0;
            }
            .ehep-settings-header {
                background: linear-gradient(135deg, #7c3aed, #4f46e5);
                color: #ffffff;
                padding: 30px 40px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .ehep-settings-header h1 {
                color: #ffffff !important;
                margin: 0;
                font-size: 28px;
                font-weight: 800;
                letter-spacing: -0.5px;
            }
            .ehep-settings-header p {
                margin: 5px 0 0 0;
                opacity: 0.9;
                font-size: 14px;
            }
            .ehep-settings-badge {
                background: rgba(255, 255, 255, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 6px 14px;
                border-radius: 50px;
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .ehep-settings-body {
                padding: 40px;
            }
            .ehep-settings-section {
                margin-bottom: 30px;
            }
            .ehep-settings-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 24px 0;
                border-bottom: 1px solid #f1f5f9;
            }
            .ehep-settings-row:last-child {
                border-bottom: none;
            }
            .ehep-settings-label {
                flex: 1;
                padding-right: 40px;
            }
            .ehep-settings-label label {
                font-weight: 700;
                font-size: 16px;
                color: #1e293b;
                display: block;
                margin-bottom: 4px;
            }
            .ehep-settings-label p {
                margin: 0;
                color: #64748b;
                font-size: 13px;
                line-height: 1.5;
            }
            .ehep-settings-control {
                flex-shrink: 0;
            }

            /* Custom Modern iOS-style Toggle Switches */
            .ehep-toggle-switch {
                position: relative;
                display: inline-block;
                width: 52px;
                height: 28px;
            }
            .ehep-toggle-switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }
            .ehep-toggle-slider {
                position: absolute;
                cursor: pointer;
                top: 0; left: 0; right: 0; bottom: 0;
                background-color: #cbd5e1;
                transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
                border-radius: 34px;
            }
            .ehep-toggle-slider:before {
                position: absolute;
                content: "";
                height: 22px;
                width: 22px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
                border-radius: 50%;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .ehep-toggle-switch input:checked + .ehep-toggle-slider {
                background-color: #7c3aed;
            }
            .ehep-toggle-switch input:checked + .ehep-toggle-slider:before {
                transform: translateX(24px);
            }

            /* Standard modern select styling */
            .ehep-select {
                padding: 10px 16px;
                font-size: 14px;
                border-radius: 8px;
                border: 1px solid #cbd5e1;
                background-color: #ffffff;
                color: #334155;
                font-weight: 600;
                outline: none;
                min-width: 150px;
                cursor: pointer;
                transition: border-color 0.2s;
            }
            .ehep-select:focus {
                border-color: #7c3aed;
            }
            .ehep-submit-container {
                padding: 30px 40px;
                background: #f8fafc;
                border-top: 1px solid #e2e8f0;
                display: flex;
                justify-content: flex-end;
            }
            .ehep-submit-btn {
                background: linear-gradient(135deg, #7c3aed, #4f46e5) !important;
                color: #ffffff !important;
                border: none !important;
                padding: 14px 28px !important;
                font-size: 15px !important;
                font-weight: 700 !important;
                border-radius: 8px !important;
                cursor: pointer !important;
                transition: all 0.2s !important;
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25) !important;
            }
            .ehep-submit-btn:hover {
                transform: translateY(-1px) !important;
                box-shadow: 0 6px 16px rgba(124, 58, 237, 0.35) !important;
            }
        </style>

        <div class="ehep-settings-container">
            <div class="ehep-settings-header">
                <div>
                    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                    <p>Configure the V2 HoverSync Engine globals and performance settings.</p>
                </div>
                <div class="ehep-settings-badge">PRO EDITION</div>
            </div>

            <form action="options.php" method="post">
                <?php settings_fields('ehep_settings'); ?>

                <div class="ehep-settings-body">
                    <div class="ehep-settings-section">
                        <!-- Globally Enable -->
                        <div class="ehep-settings-row">
                            <div class="ehep-settings-label">
                                <label for="ehep_enable_global"><?php esc_html_e('Enable Globally', 'elementor-hover-effects'); ?></label>
                                <p><?php esc_html_e('Turn on or off HoverSync effects globally across all elements and pages.', 'elementor-hover-effects'); ?></p>
                            </div>
                            <div class="ehep-settings-control">
                                <label class="ehep-toggle-switch">
                                    <input type="checkbox" id="ehep_enable_global" name="ehep_enable_global" value="1" <?php checked(get_option('ehep_enable_global', 1), 1); ?>>
                                    <span class="ehep-toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Mobile Support -->
                        <div class="ehep-settings-row">
                            <div class="ehep-settings-label">
                                <label for="ehep_enable_mobile"><?php esc_html_e('Enable on Mobile', 'elementor-hover-effects'); ?></label>
                                <p><?php esc_html_e('Enable hover effect logic on mobile screens and touch interfaces.', 'elementor-hover-effects'); ?></p>
                            </div>
                            <div class="ehep-settings-control">
                                <label class="ehep-toggle-switch">
                                    <input type="checkbox" id="ehep_enable_mobile" name="ehep_enable_mobile" value="1" <?php checked(get_option('ehep_enable_mobile', 1), 1); ?>>
                                    <span class="ehep-toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Performance Mode -->
                        <div class="ehep-settings-row">
                            <div class="ehep-settings-label">
                                <label for="ehep_performance_mode"><?php esc_html_e('Performance Mode', 'elementor-hover-effects'); ?></label>
                                <p><?php esc_html_e('High mode uses native CSS hardware acceleration. Lower settings reduce execution frequency.', 'elementor-hover-effects'); ?></p>
                            </div>
                            <div class="ehep-settings-control">
                                <select id="ehep_performance_mode" name="ehep_performance_mode" class="ehep-select">
                                    <option value="low" <?php selected(get_option('ehep_performance_mode', 'high'), 'low'); ?>><?php esc_html_e('Low', 'elementor-hover-effects'); ?></option>
                                    <option value="medium" <?php selected(get_option('ehep_performance_mode', 'high'), 'medium'); ?>><?php esc_html_e('Medium', 'elementor-hover-effects'); ?></option>
                                    <option value="high" <?php selected(get_option('ehep_performance_mode', 'high'), 'high'); ?>><?php esc_html_e('High', 'elementor-hover-effects'); ?></option>
                                </select>
                            </div>
                        </div>

                        <!-- Effect Caching -->
                        <div class="ehep-settings-row">
                            <div class="ehep-settings-label">
                                <label for="ehep_effect_caching"><?php esc_html_e('Effect Caching', 'elementor-hover-effects'); ?></label>
                                <p><?php esc_html_e('Store processed target relationships in browser memory to eliminate redundant DOM searches.', 'elementor-hover-effects'); ?></p>
                            </div>
                            <div class="ehep-settings-control">
                                <label class="ehep-toggle-switch">
                                    <input type="checkbox" id="ehep_effect_caching" name="ehep_effect_caching" value="1" <?php checked(get_option('ehep_effect_caching', 1), 1); ?>>
                                    <span class="ehep-toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Lazy Load -->
                        <div class="ehep-settings-row">
                            <div class="ehep-settings-label">
                                <label for="ehep_lazy_load"><?php esc_html_e('Lazy Load Effects', 'elementor-hover-effects'); ?></label>
                                <p><?php esc_html_e('Delay initializing the hover listeners on elements until they enter the viewport.', 'elementor-hover-effects'); ?></p>
                            </div>
                            <div class="ehep-settings-control">
                                <label class="ehep-toggle-switch">
                                    <input type="checkbox" id="ehep_lazy_load" name="ehep_lazy_load" value="1" <?php checked(get_option('ehep_lazy_load', 1), 1); ?>>
                                    <span class="ehep-toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Minify Output -->
                        <div class="ehep-settings-row">
                            <div class="ehep-settings-label">
                                <label for="ehep_minify_output"><?php esc_html_e('Minify Output', 'elementor-hover-effects'); ?></label>
                                <p><?php esc_html_e('Compress the generated stylesheets and inline dynamic overrides.', 'elementor-hover-effects'); ?></p>
                            </div>
                            <div class="ehep-settings-control">
                                <label class="ehep-toggle-switch">
                                    <input type="checkbox" id="ehep_minify_output" name="ehep_minify_output" value="1" <?php checked(get_option('ehep_minify_output', 1), 1); ?>>
                                    <span class="ehep-toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Debug Mode -->
                        <div class="ehep-settings-row">
                            <div class="ehep-settings-label">
                                <label for="ehep_debug_mode"><?php esc_html_e('Debug Mode', 'elementor-hover-effects'); ?></label>
                                <p><?php esc_html_e('Print real-time diagnostic reports and sync logs directly to the developer browser console.', 'elementor-hover-effects'); ?></p>
                            </div>
                            <div class="ehep-settings-control">
                                <label class="ehep-toggle-switch">
                                    <input type="checkbox" id="ehep_debug_mode" name="ehep_debug_mode" value="1" <?php checked(get_option('ehep_debug_mode', 0), 1); ?>>
                                    <span class="ehep-toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ehep-submit-container">
                    <input type="submit" name="submit" id="submit" class="ehep-submit-btn" value="<?php esc_attr_e('Save Changes', 'elementor-hover-effects'); ?>">
                </div>
            </form>
        </div>
        <?php
    }
}
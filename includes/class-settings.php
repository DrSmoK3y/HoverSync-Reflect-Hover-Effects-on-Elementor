<?php
/**
 * HoverSync Pro - Modern WordPress Admin Settings Dashboard
 * Version: 1.2.0
 * Zero font icons - 100% Vector SVGs & Pure Responsive CSS.
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
            esc_html__('HoverSync Pro Settings', 'elementor-hover-effects'),
            esc_html__('HoverSync Pro', 'elementor-hover-effects'),
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
        
        $enable_global    = get_option('ehep_enable_global', 1);
        $enable_mobile    = get_option('ehep_enable_mobile', 1);
        $perf_mode        = get_option('ehep_performance_mode', 'high');
        $debug_mode       = get_option('ehep_debug_mode', 0);
        $effect_caching   = get_option('ehep_effect_caching', 1);
        $lazy_load        = get_option('ehep_lazy_load', 1);
        $minify_output    = get_option('ehep_minify_output', 1);
        ?>
        <div class="wrap hoversync-admin-wrap" style="max-width: 1040px; margin: 25px 20px 40px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;">
            
            <style>
                .hoversync-admin-card {
                    background: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 24px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
                    margin-bottom: 24px;
                }
                .hoversync-header-banner {
                    background: #110e0c;
                    border-radius: 14px;
                    padding: 28px 32px;
                    color: #ebe5cb;
                    margin-bottom: 24px;
                    border: 1px solid rgba(235, 229, 203, 0.2);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 16px;
                }
                .hoversync-switch {
                    position: relative;
                    display: inline-block;
                    width: 44px;
                    height: 24px;
                    flex-shrink: 0;
                }
                .hoversync-switch input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }
                .hoversync-slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background-color: #cbd5e1;
                    transition: .25s ease;
                    border-radius: 24px;
                }
                .hoversync-slider:before {
                    position: absolute;
                    content: "";
                    height: 18px;
                    width: 18px;
                    left: 3px;
                    bottom: 3px;
                    background-color: white;
                    transition: .25s ease;
                    border-radius: 50%;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                }
                input:checked + .hoversync-slider {
                    background-color: #10b981;
                }
                input:checked + .hoversync-slider:before {
                    transform: translateX(20px);
                }
                .hoversync-setting-row {
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    padding: 18px 0;
                    border-bottom: 1px solid #f1f5f9;
                    gap: 20px;
                }
                .hoversync-setting-row:last-child {
                    border-bottom: none;
                    padding-bottom: 0;
                }
                .hoversync-setting-row:first-child {
                    padding-top: 0;
                }
                .hoversync-radio-card {
                    flex: 1;
                    min-width: 160px;
                    border: 1px solid #e2e8f0;
                    border-radius: 10px;
                    padding: 14px 16px;
                    cursor: pointer;
                    transition: all .2s;
                }
                .hoversync-radio-card:hover {
                    border-color: #94a3b8;
                    background: #f8fafc;
                }
                .hoversync-radio-card.active {
                    border-color: #10b981;
                    background: #f0fdf4;
                }
            </style>

            <?php if (isset($_GET['settings-updated'])) : ?>
                <div class="notice notice-success is-dismissible" style="border-left-color: #10b981; border-radius: 6px; padding: 12px 16px;">
                    <p style="margin: 0; font-weight: 600; color: #065f46; display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <?php esc_html_e('HoverSync Pro configuration updated successfully.', 'elementor-hover-effects'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Premium Header Banner -->
            <div class="hoversync-header-banner">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #1c1713; border: 1px solid rgba(235,229,203,0.3); display: flex; align-items: center; justify-content: center;">
                        <svg width="28" height="28" fill="none" stroke="#ebe5cb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <h1 style="margin: 0; padding: 0; font-size: 20px; font-weight: 700; color: #ebe5cb; letter-spacing: 0.5px;">HoverSync Pro</h1>
                            <span style="font-size: 11px; padding: 2px 8px; border-radius: 20px; background: rgba(235,229,203,0.15); border: 1px solid rgba(235,229,203,0.25); color: #ebe5cb; font-family: monospace;">v1.2.0</span>
                        </div>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #cfc6a2;">
                            <?php esc_html_e('Next-Gen Cross-Element Animation & Interaction Engine for Elementor', 'elementor-hover-effects'); ?>
                        </p>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 12px; color: #10b981; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                        <?php esc_html_e('RAF 60/120 FPS Batching Active', 'elementor-hover-effects'); ?>
                    </span>
                </div>
            </div>

            <form action="options.php" method="post">
                <?php settings_fields('ehep_settings'); ?>

                <!-- Card 1: Core Engine Settings -->
                <div class="hoversync-admin-card">
                    <h2 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" fill="none" stroke="#475569" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        <?php esc_html_e('Core Engine Configuration', 'elementor-hover-effects'); ?>
                    </h2>

                    <!-- Row 1: Global Enable -->
                    <div class="hoversync-setting-row">
                        <div>
                            <strong style="font-size: 14px; color: #1e293b;"><?php esc_html_e('Enable Globally', 'elementor-hover-effects'); ?></strong>
                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;"><?php esc_html_e('Initialize the HoverSync Pro runtime across all Elementor pages and templates.', 'elementor-hover-effects'); ?></p>
                        </div>
                        <label class="hoversync-switch">
                            <input type="checkbox" id="ehep_enable_global" name="ehep_enable_global" value="1" <?php checked($enable_global, 1); ?>>
                            <span class="hoversync-slider"></span>
                        </label>
                    </div>

                    <!-- Row 2: Mobile Devices -->
                    <div class="hoversync-setting-row">
                        <div>
                            <strong style="font-size: 14px; color: #1e293b;"><?php esc_html_e('Mobile Device Support', 'elementor-hover-effects'); ?></strong>
                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;"><?php esc_html_e('Map touch tap events to hover states gracefully on mobile and tablet screens.', 'elementor-hover-effects'); ?></p>
                        </div>
                        <label class="hoversync-switch">
                            <input type="checkbox" id="ehep_enable_mobile" name="ehep_enable_mobile" value="1" <?php checked($enable_mobile, 1); ?>>
                            <span class="hoversync-slider"></span>
                        </label>
                    </div>

                    <!-- Row 3: Lazy Load -->
                    <div class="hoversync-setting-row">
                        <div>
                            <strong style="font-size: 14px; color: #1e293b;"><?php esc_html_e('Viewport Lazy Loading', 'elementor-hover-effects'); ?></strong>
                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;"><?php esc_html_e('Only attach animation listeners when trigger elements enter the browser viewport via IntersectionObserver.', 'elementor-hover-effects'); ?></p>
                        </div>
                        <label class="hoversync-switch">
                            <input type="checkbox" id="ehep_lazy_load" name="ehep_lazy_load" value="1" <?php checked($lazy_load, 1); ?>>
                            <span class="hoversync-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Card 2: Performance & Acceleration -->
                <div class="hoversync-admin-card">
                    <h2 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" fill="none" stroke="#475569" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <?php esc_html_e('Performance & Acceleration Mode', 'elementor-hover-effects'); ?>
                    </h2>

                    <!-- Performance Mode Select -->
                    <div style="margin-bottom: 20px;">
                        <label for="ehep_performance_mode" style="font-size: 14px; font-weight: 600; color: #1e293b; display: block; margin-bottom: 8px;">
                            <?php esc_html_e('Execution Profile', 'elementor-hover-effects'); ?>
                        </label>
                        <select id="ehep_performance_mode" name="ehep_performance_mode" style="width: 100%; max-width: 320px; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px;">
                            <option value="high" <?php selected($perf_mode, 'high'); ?>><?php esc_html_e('High Performance (60/120 FPS RAF Batching)', 'elementor-hover-effects'); ?></option>
                            <option value="medium" <?php selected($perf_mode, 'medium'); ?>><?php esc_html_e('Balanced (Standard Devices)', 'elementor-hover-effects'); ?></option>
                            <option value="low" <?php selected($perf_mode, 'low'); ?>><?php esc_html_e('Low Resource (Battery Saver)', 'elementor-hover-effects'); ?></option>
                        </select>
                        <p style="margin: 6px 0 0 0; font-size: 12px; color: #64748b;"><?php esc_html_e('High mode leverages window.requestAnimationFrame batching for buttery-smooth animations.', 'elementor-hover-effects'); ?></p>
                    </div>

                    <!-- Row 4: Effect Caching -->
                    <div class="hoversync-setting-row">
                        <div>
                            <strong style="font-size: 14px; color: #1e293b;"><?php esc_html_e('Target DOM Selector Caching', 'elementor-hover-effects'); ?></strong>
                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;"><?php esc_html_e('Cache resolved target nodes in memory to eliminate repeated document.querySelectorAll calls on hover.', 'elementor-hover-effects'); ?></p>
                        </div>
                        <label class="hoversync-switch">
                            <input type="checkbox" id="ehep_effect_caching" name="ehep_effect_caching" value="1" <?php checked($effect_caching, 1); ?>>
                            <span class="hoversync-slider"></span>
                        </label>
                    </div>

                    <!-- Row 5: Minify Output -->
                    <div class="hoversync-setting-row">
                        <div>
                            <strong style="font-size: 14px; color: #1e293b;"><?php esc_html_e('Minify CSS & Script Payload', 'elementor-hover-effects'); ?></strong>
                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;"><?php esc_html_e('Serve compressed assets to keep page load times under 20ms.', 'elementor-hover-effects'); ?></p>
                        </div>
                        <label class="hoversync-switch">
                            <input type="checkbox" id="ehep_minify_output" name="ehep_minify_output" value="1" <?php checked($minify_output, 1); ?>>
                            <span class="hoversync-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Card 3: Developer & Diagnostics -->
                <div class="hoversync-admin-card">
                    <h2 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" fill="none" stroke="#475569" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        <?php esc_html_e('Developer & Diagnostics', 'elementor-hover-effects'); ?>
                    </h2>

                    <!-- Row 6: Debug Mode -->
                    <div class="hoversync-setting-row">
                        <div>
                            <strong style="font-size: 14px; color: #1e293b;"><?php esc_html_e('Console Debug Logging', 'elementor-hover-effects'); ?></strong>
                            <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;"><?php esc_html_e('Output event dispatches, target matching stats, and execution timings to the browser developer console.', 'elementor-hover-effects'); ?></p>
                        </div>
                        <label class="hoversync-switch">
                            <input type="checkbox" id="ehep_debug_mode" name="ehep_debug_mode" value="1" <?php checked($debug_mode, 1); ?>>
                            <span class="hoversync-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; align-items: center; gap: 16px; margin-top: 24px;">
                    <button type="submit" class="button button-primary" style="background: #110e0c; border-color: #110e0c; color: #ebe5cb; font-weight: 600; padding: 8px 24px; height: auto; border-radius: 8px; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <?php esc_html_e('Save Engine Configuration', 'elementor-hover-effects'); ?>
                    </button>
                    <span style="font-size: 13px; color: #64748b;">
                        <?php esc_html_e('Settings take effect immediately across all live pages.', 'elementor-hover-effects'); ?>
                    </span>
                </div>
            </form>

        </div>
        <?php
    }
}

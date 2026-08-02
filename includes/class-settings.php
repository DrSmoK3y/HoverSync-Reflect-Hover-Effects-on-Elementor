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
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form action="options.php" method="post">
                <?php settings_fields('ehep_settings'); ?>
                
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="ehep_enable_global"><?php esc_html_e('Enable Globally', 'elementor-hover-effects'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="ehep_enable_global" name="ehep_enable_global" value="1" <?php checked(get_option('ehep_enable_global', 1), 1); ?>>
                                <p class="description"><?php esc_html_e('Enable hover effects across all pages', 'elementor-hover-effects'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="ehep_enable_mobile"><?php esc_html_e('Enable on Mobile', 'elementor-hover-effects'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="ehep_enable_mobile" name="ehep_enable_mobile" value="1" <?php checked(get_option('ehep_enable_mobile', 1), 1); ?>>
                                <p class="description"><?php esc_html_e('Enable hover effects on mobile devices', 'elementor-hover-effects'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="ehep_performance_mode"><?php esc_html_e('Performance Mode', 'elementor-hover-effects'); ?></label>
                            </th>
                            <td>
                                <select id="ehep_performance_mode" name="ehep_performance_mode">
                                    <option value="low" <?php selected(get_option('ehep_performance_mode', 'high'), 'low'); ?>><?php esc_html_e('Low', 'elementor-hover-effects'); ?></option>
                                    <option value="medium" <?php selected(get_option('ehep_performance_mode', 'high'), 'medium'); ?>><?php esc_html_e('Medium', 'elementor-hover-effects'); ?></option>
                                    <option value="high" <?php selected(get_option('ehep_performance_mode', 'high'), 'high'); ?>><?php esc_html_e('High', 'elementor-hover-effects'); ?></option>
                                </select>
                                <p class="description"><?php esc_html_e('Higher performance may limit some effects', 'elementor-hover-effects'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="ehep_effect_caching"><?php esc_html_e('Effect Caching', 'elementor-hover-effects'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="ehep_effect_caching" name="ehep_effect_caching" value="1" <?php checked(get_option('ehep_effect_caching', 1), 1); ?>>
                                <p class="description"><?php esc_html_e('Cache frequently used effects for better performance', 'elementor-hover-effects'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="ehep_lazy_load"><?php esc_html_e('Lazy Load Effects', 'elementor-hover-effects'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="ehep_lazy_load" name="ehep_lazy_load" value="1" <?php checked(get_option('ehep_lazy_load', 1), 1); ?>>
                                <p class="description"><?php esc_html_e('Load effects only when elements are visible', 'elementor-hover-effects'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="ehep_minify_output"><?php esc_html_e('Minify Output', 'elementor-hover-effects'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="ehep_minify_output" name="ehep_minify_output" value="1" <?php checked(get_option('ehep_minify_output', 1), 1); ?>>
                                <p class="description"><?php esc_html_e('Minify CSS and JS output', 'elementor-hover-effects'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="ehep_debug_mode"><?php esc_html_e('Debug Mode', 'elementor-hover-effects'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="ehep_debug_mode" name="ehep_debug_mode" value="1" <?php checked(get_option('ehep_debug_mode', 0), 1); ?>>
                                <p class="description"><?php esc_html_e('Enable console logging for debugging', 'elementor-hover-effects'); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <?php submit_button(esc_html__('Save Settings', 'elementor-hover-effects')); ?>
            </form>
        </div>
        <?php
    }
}
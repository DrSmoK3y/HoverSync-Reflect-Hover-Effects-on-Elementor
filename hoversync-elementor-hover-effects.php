<?php
/**
 * Plugin Name: HoverSync - Advanced Elementor Hover Effects Reflector
 * Plugin URI: https://lmwebdesigners.com/plugins/hoversync-advanced-elementor-hover-effects-reflector/
 * Description: Advanced hover effects system for Elementor - trigger effects on any element from any element
 * Version: 1.0.0
 * Author: LM Designers x DrSmoK3y
 * Author URI: https://github.com/DrSmoK3y
 * Plugin URI: https://github.com/DrSmoK3y/HoverSync-Reflect-Hover-Effects-on-Elementor
 * Text Domain: hoversync-elementor-hover-effects
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('EHEP_VERSION', '1.0.0');
define('EHEP_FILE', __FILE__);
define('EHEP_PATH', plugin_dir_path(__FILE__));
define('EHEP_URL', plugin_dir_url(__FILE__));

final class Elementor_Hover_Effects_Pro {
    
    private static $_instance = null;
    
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }
    
    public function init() {
        $this->load_textdomain();
        
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }
        
        if (!version_compare(ELEMENTOR_VERSION, '3.0.0', '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }
        
        $this->includes();
        add_action('elementor/init', [$this, 'elementor_init']);
    }
    
    private function includes() {
        require_once EHEP_PATH . 'includes/class-database.php';
        require_once EHEP_PATH . 'includes/class-controls.php';
        require_once EHEP_PATH . 'includes/class-renderer.php';
        require_once EHEP_PATH . 'includes/class-ajax-handler.php';
        require_once EHEP_PATH . 'includes/class-settings.php';
    }
    
    public function elementor_init() {
        EHEP_Database::instance();
        EHEP_Controls::instance();
        EHEP_Renderer::instance();
        EHEP_Ajax_Handler::instance();
        EHEP_Settings::instance();
    }
    
    private function load_textdomain() {
        load_plugin_textdomain('elementor-hover-effects', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function admin_notice_missing_elementor() {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'elementor-hover-effects'),
            '<strong>' . esc_html__('Elementor Hover Effects Pro', 'elementor-hover-effects') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'elementor-hover-effects') . '</strong>'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function admin_notice_minimum_elementor_version() {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-hover-effects'),
            '<strong>' . esc_html__('Elementor Hover Effects Pro', 'elementor-hover-effects') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'elementor-hover-effects') . '</strong>',
            '3.0.0'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}

Elementor_Hover_Effects_Pro::instance();

register_activation_hook(__FILE__, function() {
    require_once EHEP_PATH . 'includes/class-database.php';
    EHEP_Database::create_tables();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

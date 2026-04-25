<?php
/**
 * Enhanced Frontend Renderer with Custom CSS Support
 * File: includes/class-renderer.php
 */

if (!defined('ABSPATH')) {
    exit;
}

class EHEP_Renderer {
    
    private static $_instance = null;
    private $custom_css_elements = [];
    
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'enqueue_editor_scripts']);
        add_action('elementor/preview/enqueue_styles', [$this, 'enqueue_preview_styles']);
        add_action('wp_footer', [$this, 'output_custom_css'], 999);
        add_action('elementor/frontend/after_render', [$this, 'collect_custom_css']);
    }
    
    public function enqueue_frontend_scripts() {
        if (!\Elementor\Plugin::$instance->preview->is_preview_mode()) {
            wp_enqueue_style(
                'ehep-frontend',
                EHEP_URL . 'assets/css/frontend.css',
                [],
                EHEP_VERSION
            );
            
            wp_enqueue_script(
                'ehep-frontend',
                EHEP_URL . 'assets/js/frontend.js',
                ['jquery'],
                EHEP_VERSION,
                true
            );
            
            wp_localize_script('ehep-frontend', 'ehepConfig', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ehep_nonce'),
                'isEditor' => \Elementor\Plugin::$instance->editor->is_edit_mode(),
                'debugMode' => get_option('ehep_debug_mode', 0),
            ]);
        }
    }
    
    public function enqueue_editor_scripts() {
        wp_enqueue_style(
            'ehep-editor',
            EHEP_URL . 'assets/css/editor.css',
            [],
            EHEP_VERSION
        );
        
        wp_enqueue_script(
            'ehep-editor',
            EHEP_URL . 'assets/js/editor.js',
            ['jquery', 'elementor-editor'],
            EHEP_VERSION,
            true
        );
        
        wp_localize_script('ehep-editor', 'ehepEditor', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ehep_nonce'),
        ]);
    }
    
    public function enqueue_preview_styles() {
        wp_enqueue_style(
            'ehep-frontend',
            EHEP_URL . 'assets/css/frontend.css',
            [],
            EHEP_VERSION
        );
        
        wp_enqueue_script(
            'ehep-frontend',
            EHEP_URL . 'assets/js/frontend.js',
            ['jquery'],
            EHEP_VERSION,
            true
        );
        
        wp_localize_script('ehep-frontend', 'ehepConfig', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ehep_nonce'),
            'isEditor' => true,
            'debugMode' => get_option('ehep_debug_mode', 0),
        ]);
    }
    
    public function collect_custom_css($element) {
        // Global Custom CSS box removed. Custom properties now handled via repeater in JS.
    }
    
    public function output_custom_css() {
        if (empty($this->custom_css_elements)) {
            return;
        }
        
        $css_output = '';
        foreach ($this->custom_css_elements as $element) {
            $css_output .= "/* Custom CSS for element: {$element['id']} */\n";
            $css_output .= $element['css'] . "\n\n";
        }
        
        // Minify if enabled
        if (get_option('ehep_minify_output', 1)) {
            $css_output = $this->minify_css($css_output);
        }
        
        echo '<style id="ehep-custom-css">' . $css_output . '</style>';
    }
    
    private function scope_css($css, $scope) {
        // Remove comments
        $css = preg_replace('/\/\*.*?\*\//s', '', $css);
        
        // Split into rules
        $rules = explode('}', $css);
        $scoped_rules = [];
        
        foreach ($rules as $rule) {
            $rule = trim($rule);
            if (empty($rule)) continue;
            
            $parts = explode('{', $rule, 2);
            if (count($parts) !== 2) continue;
            
            $selectors = $parts[0];
            $declarations = $parts[1];
            
            // Split multiple selectors
            $selector_array = array_map('trim', explode(',', $selectors));
            $scoped_selectors = [];
            
            foreach ($selector_array as $selector) {
                if (empty($selector)) continue;
                
                // Don't scope if already starts with the scope
                if (strpos($selector, $scope) === 0) {
                    $scoped_selectors[] = $selector;
                }
                // Don't scope :root, @media, @keyframes, etc
                elseif (strpos($selector, ':root') === 0 || 
                        strpos($selector, '@') === 0) {
                    $scoped_selectors[] = $selector;
                }
                // Scope to element
                else {
                    $scoped_selectors[] = $scope . ' ' . $selector;
                }
            }
            
            if (!empty($scoped_selectors)) {
                $scoped_rules[] = implode(', ', $scoped_selectors) . ' { ' . trim($declarations) . ' }';
            }
        }
        
        return implode("\n", $scoped_rules);
    }
    
    private function minify_css($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove whitespace
        $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*([{}|:;,])\s*/', '$1', $css);
        
        return trim($css);
    }
}
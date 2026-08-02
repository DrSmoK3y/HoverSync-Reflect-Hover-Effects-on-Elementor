<?php
/**
 * AJAX Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class EHEP_Ajax_Handler {
    
    private static $_instance = null;
    
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_ehep_save_effect', [$this, 'save_effect']);
        add_action('wp_ajax_ehep_delete_effect', [$this, 'delete_effect']);
        add_action('wp_ajax_ehep_get_effects', [$this, 'get_effects']);
        add_action('wp_ajax_ehep_load_preset', [$this, 'load_preset']);
    }
    
    public function save_effect() {
        check_ajax_referer('ehep_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        $data = [
            'post_id' => intval($_POST['post_id'] ?? 0),
            'source_element_id' => sanitize_text_field($_POST['source_element_id'] ?? ''),
            'target_elements' => json_decode(stripslashes($_POST['target_elements'] ?? '[]'), true),
            'effects' => json_decode(stripslashes($_POST['effects'] ?? '[]'), true),
            'conditions' => json_decode(stripslashes($_POST['conditions'] ?? '[]'), true),
            'priority' => intval($_POST['priority'] ?? 10),
            'status' => sanitize_text_field($_POST['status'] ?? 'active'),
        ];
        
        if (isset($_POST['id']) && $_POST['id'] > 0) {
            $data['id'] = intval($_POST['id']);
        }
        
        $db = EHEP_Database::instance();
        $id = $db->save_effect($data);
        
        if ($id) {
            wp_send_json_success([
                'message' => 'Effect saved successfully',
                'id' => $id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to save effect']);
        }
    }
    
    public function delete_effect() {
        check_ajax_referer('ehep_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            wp_send_json_error(['message' => 'Invalid effect ID']);
        }
        
        $db = EHEP_Database::instance();
        $result = $db->delete_effect($id);
        
        if ($result) {
            wp_send_json_success(['message' => 'Effect deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete effect']);
        }
    }
    
    public function get_effects() {
        check_ajax_referer('ehep_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id'] ?? 0);
        
        if ($post_id <= 0) {
            wp_send_json_error(['message' => 'Invalid post ID']);
        }
        
        $db = EHEP_Database::instance();
        $effects = $db->get_effects_by_post($post_id);
        
        wp_send_json_success(['effects' => $effects]);
    }
    
    public function load_preset() {
        check_ajax_referer('ehep_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        
        $preset_id = sanitize_text_field($_POST['preset_id'] ?? '');
        
        if (empty($preset_id)) {
            wp_send_json_error(['message' => 'Invalid preset ID']);
        }
        
        $preset = EHEP_Preset_Manager::get_preset($preset_id);
        
        if ($preset) {
            wp_send_json_success(['preset' => $preset]);
        } else {
            wp_send_json_error(['message' => 'Preset not found']);
        }
    }
}
<?php
/**
 * Database handler for hover effects storage
 */

if (!defined('ABSPATH')) {
    exit;
}

class EHEP_Database {
    
    private static $_instance = null;
    private $table_name;
    
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'elementor_hover_effects';
    }
    
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'elementor_hover_effects';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id bigint(20) UNSIGNED NOT NULL,
            source_element_id varchar(255) NOT NULL,
            target_elements longtext NOT NULL,
            effects longtext NOT NULL,
            conditions longtext DEFAULT NULL,
            priority int(11) DEFAULT 10,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY source_element_id (source_element_id),
            KEY status (status)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function save_effect($data) {
        global $wpdb;
        
        $defaults = [
            'post_id' => 0,
            'source_element_id' => '',
            'target_elements' => '[]',
            'effects' => '[]',
            'conditions' => '[]',
            'priority' => 10,
            'status' => 'active'
        ];
        
        $data = wp_parse_args($data, $defaults);
        
        if (is_array($data['target_elements'])) {
            $data['target_elements'] = wp_json_encode($data['target_elements']);
        }
        if (is_array($data['effects'])) {
            $data['effects'] = wp_json_encode($data['effects']);
        }
        if (is_array($data['conditions'])) {
            $data['conditions'] = wp_json_encode($data['conditions']);
        }
        
        if (isset($data['id']) && $data['id'] > 0) {
            $wpdb->update(
                $this->table_name,
                $data,
                ['id' => $data['id']],
                ['%d', '%s', '%s', '%s', '%s', '%d', '%s'],
                ['%d']
            );
            return $data['id'];
        } else {
            $wpdb->insert(
                $this->table_name,
                $data,
                ['%d', '%s', '%s', '%s', '%s', '%d', '%s']
            );
            return $wpdb->insert_id;
        }
    }
    
    public function get_effects_by_post($post_id, $status = 'active') {
        global $wpdb;
        
        $query = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE post_id = %d AND status = %s ORDER BY priority ASC",
            $post_id,
            $status
        );
        
        $results = $wpdb->get_results($query, ARRAY_A);
        
        foreach ($results as &$result) {
            $result['target_elements'] = json_decode($result['target_elements'], true);
            $result['effects'] = json_decode($result['effects'], true);
            $result['conditions'] = json_decode($result['conditions'], true);
        }
        
        return $results;
    }
    
    public function get_effect_by_id($id) {
        global $wpdb;
        
        $query = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            $id
        );
        
        $result = $wpdb->get_row($query, ARRAY_A);
        
        if ($result) {
            $result['target_elements'] = json_decode($result['target_elements'], true);
            $result['effects'] = json_decode($result['effects'], true);
            $result['conditions'] = json_decode($result['conditions'], true);
        }
        
        return $result;
    }
    
    public function delete_effect($id) {
        global $wpdb;
        return $wpdb->delete(
            $this->table_name,
            ['id' => $id],
            ['%d']
        );
    }
    
    public function delete_effects_by_post($post_id) {
        global $wpdb;
        return $wpdb->delete(
            $this->table_name,
            ['post_id' => $post_id],
            ['%d']
        );
    }
    
    public function update_status($id, $status) {
        global $wpdb;
        return $wpdb->update(
            $this->table_name,
            ['status' => $status],
            ['id' => $id],
            ['%s'],
            ['%d']
        );
    }
}
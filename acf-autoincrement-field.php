<?php
/*
Plugin Name: ACF Autoincrement Field
Plugin URI: https://github.com/DiBenedettoGaetano/ACF-Autoincrement-field
Description: Adds a custom autoincrement field to Advanced Custom Fields.
Version: 0.1
Author: Your Name
Author URI: Gaetano di benedetto
*/

if (!defined('ABSPATH')) {
    exit;
}

class ACF_Autoincrement_Field_Plugin {
    
    private $debug = true; // Set to false in production
    private $autoincrement_fields = []; // Will be populated in the constructor

    public function __construct() {
        add_action('acf/include_field_types', array($this, 'include_field'), 1);
        add_filter('acf/load_value/type=autoincrement', array($this, 'load_value'), 10, 3);
        add_filter('acf/update_value/type=autoincrement', array($this, 'update_value'), 10, 3);
        add_action('acf/save_post', array($this, 'save_autoincrement_field'), 20);
        add_action('wp_insert_post', array($this, 'handle_post_creation'), 999, 3);
        add_action('save_post', array($this, 'handle_post_save'), 999, 3);

        // Load the configuration
        $this->load_config();
    }

    private function load_config() {
        // Load configuration from a file or database
        // For this example, we'll use a hardcoded array
        $this->autoincrement_fields = [
            [
                'post_type' => 'membership',
                'field_name' => 'numerotessera',
                'pattern' => '000000000'
            ],
            // Add more configurations here as needed
        ];
    }

    public function include_field() {
        require_once('fields/class-acf-field-autoincrement.php');
    }

    public function load_value($value, $post_id, $field) {
        if ($this->debug) error_log("ACF Autoincrement: Load value for field {$field['name']}, post ID: $post_id, current value: $value");
        return $value;
    }

    public function update_value($value, $post_id, $field) {
        if ($this->debug) error_log("ACF Autoincrement: Update value for field {$field['name']}, post ID: $post_id, value: $value");
        if (empty($value)) {
            $value = $this->generate_autoincrement_value($field, $post_id);
            if ($this->debug) error_log("ACF Autoincrement: Generated new value: $value");
        }
        return $value;
    }

    public function save_autoincrement_field($post_id) {
        if ($this->debug) error_log("ACF Autoincrement: ACF save_post action for post ID: $post_id");
        $this->process_autoincrement_fields($post_id);
    }

    public function handle_post_creation($post_id, $post, $update) {
        if (!$update) {
            if ($this->debug) error_log("ACF Autoincrement: New post created, ID: $post_id, Type: {$post->post_type}");
            $this->process_autoincrement_fields($post_id);
        }
    }

    public function handle_post_save($post_id, $post, $update) {
        if ($this->debug) error_log("ACF Autoincrement: Post saved, ID: $post_id, Type: {$post->post_type}, Status: {$post->post_status}");
        $this->process_autoincrement_fields($post_id);
    }

    private function process_autoincrement_fields($post_id) {
        $post_type = get_post_type($post_id);
        $all_meta = get_post_meta($post_id);
        if ($this->debug) error_log("ACF Autoincrement: All meta for post ID $post_id: " . print_r($all_meta, true));

        foreach ($this->autoincrement_fields as $field_config) {
            if ($post_type === $field_config['post_type']) {
                $field_name = $field_config['field_name'];
                if (isset($all_meta[$field_name]) && empty($all_meta[$field_name][0])) {
                    $new_value = $this->generate_autoincrement_value($field_config, $post_id);
                    update_post_meta($post_id, $field_name, $new_value);
                    if ($this->debug) error_log("ACF Autoincrement: Updated {$field_name} with new value: $new_value");
                }
            }
        }

        // Manteniamo la logica esistente per altri campi ACF
        foreach ($all_meta as $meta_key => $meta_value) {
            if (strpos($meta_key, 'field_') === 0) {
                $field = get_field_object($meta_key, $post_id);
                if ($field && $field['type'] === 'autoincrement') {
                    if ($this->debug) error_log("ACF Autoincrement: Processing ACF autoincrement field: {$field['name']}");
                    $current_value = get_post_meta($post_id, $field['name'], true);
                    if (empty($current_value)) {
                        $new_value = $this->generate_autoincrement_value($field, $post_id);
                        update_post_meta($post_id, $field['name'], $new_value);
                        if ($this->debug) error_log("ACF Autoincrement: Updated {$field['name']} with new value: $new_value");
                    }
                }
            }
        }
    }

    private function generate_autoincrement_value($field, $post_id) {
        $last_value = $this->get_last_value($field, $post_id);
        $pattern = !empty($field['pattern']) ? $field['pattern'] : '000000000';
        
        if (!$last_value) {
            $new_value = str_pad(1, strlen($pattern), '0', STR_PAD_LEFT);
        } else {
            $number = intval($last_value) + 1;
            $new_value = str_pad($number, strlen($pattern), '0', STR_PAD_LEFT);
        }

        if ($this->debug) error_log("ACF Autoincrement: Generated new value: $new_value (Last value was: $last_value)");
        return $new_value;
    }

    private function get_last_value($field, $post_id) {
        global $wpdb;
        $post_type = get_post_type($post_id);

        $query = $wpdb->prepare(
            "SELECT meta_value FROM {$wpdb->postmeta} pm
            JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE p.post_type = %s AND pm.meta_key = %s
            ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC LIMIT 1",
            $post_type,
            $field['field_name'] ?? $field['name']
        );

        $last_value = $wpdb->get_var($query);
        if ($this->debug) {
            error_log("ACF Autoincrement: Last value query: $query");
            error_log("ACF Autoincrement: Last value result: $last_value");
        }

        return $last_value;
    }
}

// Initialize the plugin
new ACF_Autoincrement_Field_Plugin();
<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('acf_field_autoincrement')) :

class acf_field_autoincrement extends acf_field {
    
    function __construct() {
        $this->name = 'autoincrement';
        $this->label = __('Autoincrement', 'acf');
        $this->category = 'basic';
        $this->defaults = array(
            'post_type'     => 'membership',
            'pattern'       => '000000000',
        );
        
        parent::__construct();
    }

    function render_field_settings($field) {
        acf_render_field_setting($field, array(
            'label'         => 'Post Type',
            'instructions'  => 'Select the Post Type for autoincrement',
            'type'          => 'select',
            'name'          => 'post_type',
            'choices'       => $this->get_post_types(),
            'required'      => 1,
        ));

        acf_render_field_setting($field, array(
            'label'         => 'Pattern',
            'instructions'  => 'Enter a pattern for the number (e.g. 000000000)',
            'type'          => 'text',
            'name'          => 'pattern',
        ));
    }

    function render_field($field) {
        $value = isset($field['value']) ? $field['value'] : '';
        echo '<input type="text" readonly="readonly" name="' . esc_attr($field['name']) . '" value="' . esc_attr($value) . '" />';
    }

    function update_field($field) {
        return $field;
    }

    function load_field($field) {
        return $field;
    }

    private function get_post_types() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $choices = array();
        foreach ($post_types as $post_type) {
            $choices[$post_type->name] = $post_type->label;
        }
        return $choices;
    }
}

// Initialize the field
new acf_field_autoincrement();

endif;
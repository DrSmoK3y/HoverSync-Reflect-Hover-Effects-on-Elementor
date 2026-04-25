<?php
/**
 * Enhanced Elementor Controls
 * Optimized to prevent memory recursion and loading errors.
 */

if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Controls_Manager;
use Elementor\Element_Base;

class EHEP_Controls {
    
    private static $_instance = null;
    
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct() {
        // Reduced hook complexity to avoid conflicts
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_controls'], 10, 2);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_controls'], 10, 2);
        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'register_controls'], 10, 2);
        
        add_action('elementor/frontend/before_render', [$this, 'before_render']);
    }
    
    public function register_controls(Element_Base $element, $args) {
        
        $element->start_controls_section(
            'ehep_hover_effects_section',
            [
                'label' => esc_html__('HOVERSYNC PRO ➜', 'elementor-hover-effects'),
                'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
            ]
        );
        
        $element->add_control(
            'ehep_enable',
            [
                'label' => esc_html__('Enable HoverSync Engine', 'elementor-hover-effects'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__('Active', 'elementor-hover-effects'),
                'label_off' => esc_html__('Disabled', 'elementor-hover-effects'),
                'return_value' => 'yes',
                'description' => esc_html__('Power up this element with advanced cross-element logic.', 'elementor-hover-effects'),
            ]
        );
        
        $element->add_control(
            'ehep_heading_config',
            [
                'label' => esc_html__('⚙ Identity & Role', 'elementor-hover-effects'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [ 'ehep_enable' => 'yes' ],
            ]
        );
        
        $element->add_control(
            'ehep_trigger_type',
            [
                'label' => esc_html__('Interaction Behavior', 'elementor-hover-effects'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'source',
                'options' => [
                    'source' => esc_html__('Primary Trigger (Source)', 'elementor-hover-effects'),
                    'target' => esc_html__('Passive Receiver (Target)', 'elementor-hover-effects'),
                    'both' => esc_html__('Hybrid (Source + Target)', 'elementor-hover-effects'),
                ],
                'description' => esc_html__('Define how this element participates in the sync network.', 'elementor-hover-effects'),
                'condition' => [ 'ehep_enable' => 'yes' ],
            ]
        );
        
        $element->add_control(
            'ehep_element_id',
            [
                'label' => esc_html__('Custom Element Alias', 'elementor-hover-effects'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => 'my-custom-id',
                'description' => esc_html__('Required for targeting. Use letters, numbers, and dashes only.', 'elementor-hover-effects'),
                'label_block' => true,
                'condition' => [ 'ehep_enable' => 'yes' ],
            ]
        );

        $element->add_control(
            'ehep_heading_effects',
            [
                'label' => esc_html__('🎯 Target Relationships', 'elementor-hover-effects'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [ 
                    'ehep_enable' => 'yes',
                    'ehep_trigger_type!' => 'target',
                ],
            ]
        );

        // --- EFFECTS ENGINE (REPEATER) ---
        $repeater = new \Elementor\Repeater();
        
        $repeater->add_control(
            'target_selector',
            [
                'label' => esc_html__('Target CSS Selector', 'elementor-hover-effects'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => '#my-id or .my-class',
                'label_block' => true,
                'description' => esc_html__('Identify the element to animate using its ID (#) or Class (.).', 'elementor-hover-effects'),
            ]
        );
        
        $repeater->add_control(
            'effect_type',
            [
                'label' => esc_html__('Animation Type', 'elementor-hover-effects'),
                'type' => Controls_Manager::SELECT,
                'default' => 'scale',
                'options' => [
                    'scale' => esc_html__('📏 Scale (Zoom)', 'elementor-hover-effects'),
                    'rotate' => esc_html__('🔄 Rotation', 'elementor-hover-effects'),
                    'translateX' => esc_html__('↔ Horizontal Move', 'elementor-hover-effects'),
                    'translateY' => esc_html__('↕ Vertical Move', 'elementor-hover-effects'),
                    'opacity' => esc_html__('👻 Opacity (Fade)', 'elementor-hover-effects'),
                    'blur' => esc_html__('🌫️ Glass Blur', 'elementor-hover-effects'),
                    'grayscale' => esc_html__('🌑 Grayscale', 'elementor-hover-effects'),
                    'background' => esc_html__('🎨 Background Color', 'elementor-hover-effects'),
                    'custom' => esc_html__('💻 Custom CSS Properties', 'elementor-hover-effects'),
                ],
            ]
        );
        
        // --- DYNAMIC CONTROL VALUES ---
        
        // Scale
        $repeater->add_control(
            'effect_value_scale',
            [
                'label' => esc_html__('Scale Factor', 'elementor-hover-effects'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'], // Dummy unit for scalar
                'range' => [ 'px' => [ 'min' => 0, 'max' => 3, 'step' => 0.1 ] ],
                'default' => [ 'size' => 1.2 ],
                'description' => esc_html__('1 = Normal, 1.2 = 20% Bigger, 0.8 = 20% Smaller.', 'elementor-hover-effects'),
                'condition' => [ 'effect_type' => 'scale' ],
            ]
        );
        
        // Movement (X/Y)
        $repeater->add_control(
            'effect_value_translate',
            [
                'label' => esc_html__('Distance (px)', 'elementor-hover-effects'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [ 'px' => [ 'min' => -500, 'max' => 500 ] ],
                'default' => [ 'size' => 20, 'unit' => 'px' ],
                'condition' => [ 'effect_type' => ['translateX', 'translateY'] ],
            ]
        );
        
        // Rotation
        $repeater->add_control(
            'effect_value_rotate',
            [
                'label' => esc_html__('Degrees', 'elementor-hover-effects'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['deg'],
                'range' => [ 'deg' => [ 'min' => -360, 'max' => 360 ] ],
                'default' => [ 'size' => 15, 'unit' => 'deg' ],
                'condition' => [ 'effect_type' => 'rotate' ],
            ]
        );

        // Opacity & Filters
        $repeater->add_control(
            'effect_value_intensity',
            [
                'label' => esc_html__('Intensity (0-1)', 'elementor-hover-effects'),
                'type' => Controls_Manager::SLIDER,
                'range' => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.1 ] ],
                'default' => [ 'size' => 0.5 ],
                'condition' => [ 'effect_type' => ['opacity', 'grayscale', 'blur'] ],
            ]
        );

        // Background Color
        $repeater->add_control(
            'effect_value_color',
            [
                'label' => esc_html__('Background Color', 'elementor-hover-effects'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffeb3b',
                'condition' => [ 'effect_type' => 'background' ],
            ]
        );

        // Custom CSS Rule
        $repeater->add_control(
            'effect_value_custom',
            [
                'label' => esc_html__('CSS Properties', 'elementor-hover-effects'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 3,
                'placeholder' => 'border: 2px solid red; border-radius: 50%; opacity: 0.8;',
                'description' => esc_html__('Enter valid CSS properties. Example: border: 1px solid green;', 'elementor-hover-effects'),
                'condition' => [ 'effect_type' => 'custom' ],
            ]
        );

        $repeater->add_control(
            'effect_duration',
            [
                'label' => esc_html__('Duration (ms)', 'elementor-hover-effects'),
                'type' => Controls_Manager::NUMBER,
                'default' => 300,
                'min' => 0,
                'step' => 50,
                'description' => esc_html__('Time in milliseconds. 1000ms = 1 second.', 'elementor-hover-effects'),
            ]
        );

        $element->add_control(
            'ehep_targets',
            [
                'label' => esc_html__('Effects Layer Manager', 'elementor-hover-effects'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '<i class="eicon-star"></i> {{{ effect_type }}} > {{{ target_selector }}}',
                'button_text' => 'ADD EFFECT',
                'condition' => [
                    'ehep_enable' => 'yes',
                    'ehep_trigger_type!' => 'target', // Hide if only a target
                ],
            ]
        );

        $element->end_controls_section();
    }
    
    // Optimized Render Function (Lightweight)
    public function before_render(Element_Base $element) {
        $settings = $element->get_settings_for_display();
        
        if (empty($settings['ehep_enable']) || $settings['ehep_enable'] !== 'yes') {
            return;
        }
        
        $element_id = !empty($settings['ehep_element_id']) ? $settings['ehep_element_id'] : $element->get_id();
        
        // Basic array cleanup
        $clean_targets = [];
        if (!empty($settings['ehep_targets'])) {
            foreach ($settings['ehep_targets'] as $target) {
                // Flatten structure for JS
                $clean_targets[] = [
                    'selector' => $target['target_selector'],
                    'type' => $target['effect_type'],
                    'val' => $this->resolve_value($target),
                    'dur' => $target['effect_duration']
                ];
            }
        }
        
        $config = [
            'id' => $element_id,
            'role' => $settings['ehep_trigger_type'],
            'fx' => $clean_targets
        ];
        
        // Add minimal attributes
        $element->add_render_attribute('_wrapper', [
            'data-hoversync' => wp_json_encode($config),
            'class' => 'hoversync-element'
        ]);
        
        if (!empty($settings['ehep_element_id'])) {
            $element->add_render_attribute('_wrapper', 'id', $settings['ehep_element_id']);
        }
    }

    private function resolve_value($target) {
        $type = $target['effect_type'];
        
        if (strpos($type, 'translate') !== false) {
             return $target['effect_value_translate']['size'] . ($target['effect_value_translate']['unit'] ?? 'px');
        }
        
        if ($type === 'rotate') {
             return $target['effect_value_rotate']['size'] . 'deg';
        }
        
        if (in_array($type, ['opacity', 'grayscale'])) {
             return $target['effect_value_intensity']['size'];
        }

        if ($type === 'blur') {
             return $target['effect_value_intensity']['size'] * 10 . 'px';
        }

        if ($type === 'background') {
             return $target['effect_value_color'];
        }

        if ($type === 'custom') {
             return $target['effect_value_custom'];
        }
        
        return $target['effect_value_scale']['size'] ?? 1.2;
    }
}
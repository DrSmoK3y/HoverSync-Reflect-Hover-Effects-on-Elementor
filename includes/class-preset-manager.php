<?php
/**
 * Preset Manager for EHEP
 */

if (!defined('ABSPATH')) {
    exit;
}

class EHEP_Preset_Manager {

    public static function get_presets() {
        return [
            'bouncy_zoom' => [
                'name' => esc_html__('✨ Bouncy Zoom Combo', 'elementor-hover-effects'),
                'effects' => [
                    [
                        'effect_type' => 'scale',
                        'effect_value' => '1.15',
                        'effect_duration' => 500,
                    ],
                    [
                        'effect_type' => 'rotate',
                        'effect_value' => '3',
                        'effect_duration' => 500,
                    ]
                ]
            ],
            'glass_frost' => [
                'name' => esc_html__('🌫️ Glass Frost', 'elementor-hover-effects'),
                'effects' => [
                    [
                        'effect_type' => 'blur',
                        'effect_value' => '8',
                        'effect_duration' => 400,
                    ],
                    [
                        'effect_type' => 'opacity',
                        'effect_value' => '0.8',
                        'effect_duration' => 400,
                    ]
                ]
            ],
            'smooth_shift' => [
                'name' => esc_html__('↔️ Smooth Shift Left', 'elementor-hover-effects'),
                'effects' => [
                    [
                        'effect_type' => 'translateX',
                        'effect_value' => '-15px',
                        'effect_duration' => 300,
                    ]
                ]
            ],
            'cinematic_tilt' => [
                'name' => esc_html__('🎬 Cinematic Saturation', 'elementor-hover-effects'),
                'effects' => [
                    [
                        'effect_type' => 'saturate',
                        'effect_value' => '1.5',
                        'effect_duration' => 600,
                    ],
                    [
                        'effect_type' => 'scale',
                        'effect_value' => '1.05',
                        'effect_duration' => 600,
                    ]
                ]
            ],
        ];
    }

    public static function get_preset($id) {
        $presets = self::get_presets();
        return isset($presets[$id]) ? $presets[$id] : null;
    }
}

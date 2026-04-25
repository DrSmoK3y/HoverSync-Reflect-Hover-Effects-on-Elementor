(function($) {
    'use strict';

    class ElementorHoverEffectsEditor {
        constructor() {
            this.config = window.ehepEditor || {};
            this.init();
        }

        init() {
            this.bindEvents();
            this.enhanceControls();
            this.log('Editor initialized');
        }

        bindEvents() {
            elementor.hooks.addAction('panel/open_editor/widget', (panel, model, view) => {
                this.onPanelOpen(panel, model, view);
            });

            elementor.hooks.addAction('panel/open_editor/section', (panel, model, view) => {
                this.onPanelOpen(panel, model, view);
            });

            elementor.hooks.addAction('panel/open_editor/container', (panel, model, view) => {
                this.onPanelOpen(panel, model, view);
            });
        }

        onPanelOpen(panel, model, view) {
            setTimeout(() => {
                this.initPresetSelector();
                this.initLivePreview();
            }, 100);
        }

        enhanceControls() {
            elementor.channels.editor.on('section:activated', (sectionName, editor) => {
                if (sectionName === 'ehep_hover_effects_section') {
                    this.enhanceEffectsSection(editor);
                }
            });
        }

        enhanceEffectsSection(editor) {
            const $section = $('.elementor-control-ehep_hover_effects_section');
            if ($section.length === 0) return;

            this.log('Effects section enhanced');
        }

        initPresetSelector() {
            const $presetControl = $('.elementor-control-ehep_preset select');
            if ($presetControl.length === 0) return;

            $presetControl.on('change', (e) => {
                const presetId = $(e.target).val();
                if (presetId) {
                    this.loadPreset(presetId);
                }
            });
        }

        loadPreset(presetId) {
            $.ajax({
                url: this.config.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'ehep_load_preset',
                    nonce: this.config.nonce,
                    preset_id: presetId
                },
                success: (response) => {
                    if (response.success && response.data.preset) {
                        this.applyPresetToEditor(response.data.preset);
                        this.showNotice('Preset loaded successfully!', 'success');
                    }
                },
                error: () => {
                    this.showNotice('Failed to load preset', 'error');
                }
            });
        }

        applyPresetToEditor(preset) {
            const editor = elementor.getPanelView().getCurrentPageView();
            if (!editor) return;

            if (preset.effects && preset.effects.length > 0) {
                const targets = preset.effects.map(effect => {
                    const target = {
                        target_selector: '',
                        effect_type: effect.effect_type || 'scale',
                        effect_duration: effect.effect_duration || 300,
                        effect_delay: effect.effect_delay || 0,
                        effect_easing: effect.effect_easing || 'ease-out'
                    };
                    
                    // Set appropriate value field based on effect type
                    const effectType = effect.effect_type || 'scale';
                    const valueKey = 'effect_value_' + effectType.toLowerCase().replace('-', '');
                    
                    // Handle different value formats
                    if (effect.effect_value) {
                        if (effectType === 'shadow') {
                            target[valueKey] = this.parseShadowValue(effect.effect_value);
                        } else if (effectType === 'rotate' || effectType === 'hue-rotate') {
                            const numValue = parseFloat(effect.effect_value);
                            target[valueKey] = { size: numValue, unit: 'deg' };
                        } else if (effectType === 'translateX' || effectType === 'translateY') {
                            const numValue = parseFloat(effect.effect_value);
                            const unit = effect.effect_value.includes('%') ? '%' : 'px';
                            target[valueKey] = { size: numValue, unit: unit };
                        } else if (effectType === 'blur') {
                            const numValue = parseFloat(effect.effect_value);
                            target[valueKey] = { size: numValue, unit: 'px' };
                        } else if (effectType === 'background') {
                            target[valueKey] = effect.effect_value;
                        } else {
                            const numValue = parseFloat(effect.effect_value);
                            target[valueKey] = { size: numValue };
                        }
                    }
                    
                    return target;
                });
                
                editor.model.setSetting('ehep_targets', targets);
            }

            this.log('Preset applied:', preset.name);
        }

        parseShadowValue(shadowString) {
            // Parse shadow string "0 10px 30px 0 rgba(0,0,0,0.3)"
            const parts = shadowString.trim().split(/\s+/);
            return {
                horizontal: parseInt(parts[0]) || 0,
                vertical: parseInt(parts[1]) || 10,
                blur: parseInt(parts[2]) || 30,
                spread: parseInt(parts[3]) || 0,
                color: parts[4] || 'rgba(0,0,0,0.3)'
            };
        }

        initLivePreview() {
            const debouncedPreview = this.debounce(() => {
                this.updatePreview();
            }, 500);

            $('.elementor-control-ehep_targets input, .elementor-control-ehep_targets select').on('input change', debouncedPreview);
        }

        updatePreview() {
            const $preview = elementor.$preview;
            if (!$preview || $preview.length === 0) return;

            const editor = elementor.getPanelView().getCurrentPageView();
            if (!editor) return;

            const settings = editor.model.get('settings').attributes;
            if (!settings.ehep_enable || settings.ehep_enable !== 'yes') return;

            this.log('Preview updated');
        }

        showNotice(message, type = 'success') {
            const $notice = $(`<div class="ehep-editor-notice ehep-notice-${type}">${message}</div>`);
            $('body').append($notice);

            // Position at top center
            $notice.css({
                position: 'fixed',
                top: '20px',
                left: '50%',
                transform: 'translateX(-50%)',
                zIndex: 9999999,
                padding: '15px 30px',
                background: type === 'success' ? '#10b981' : '#ef4444',
                color: 'white',
                borderRadius: '5px',
                boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
                fontWeight: 600,
                fontSize: '14px'
            });

            setTimeout(() => {
                $notice.fadeOut(() => $notice.remove());
            }, 3000);
        }

        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        log(...args) {
            if (this.config.debug || window.ehepDebug) {
                console.log('[EHEP Editor]', ...args);
            }
        }
    }

    // Initialize when Elementor is ready
    $(window).on('elementor:init', function() {
        new ElementorHoverEffectsEditor();
    });

})(jQuery);
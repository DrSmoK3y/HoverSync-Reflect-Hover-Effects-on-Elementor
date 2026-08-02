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
                this.enhanceRepeaterTitles();
            }, 100);
        }

        enhanceRepeaterTitles() {
            // Watch for changes in repeater titles to format them into high-contrast badges
            const self = this;
            const formatTitles = () => {
                $('.elementor-control-ehep_targets .elementor-repeater-row-item-title').each(function() {
                    const $title = $(this);
                    if ($title.data('hoversync-enhanced')) return;
                    $title.data('hoversync-enhanced', true);

                    const rawText = $title.text().trim();
                    // Example raw format from PHP: <i class="eicon-star"></i> scale > #my-id
                    // Or standard text format if HTML was stripped
                    const match = rawText.match(/^(.*?)\s*>\s*(.*)$/);
                    if (match) {
                        const effect = match[1].replace(/eicon-[a-z-]+/g, '').trim();
                        const selector = match[2].trim();
                        $title.html(`
                            <span class="ehep-badge-effect">${effect}</span>
                            <span class="ehep-badge-arrow">➜</span>
                            <span class="ehep-badge-selector">${selector || 'No Target'}</span>
                        `);
                    }
                });
            };

            // Run instantly & set up observer for future additions
            formatTitles();
            const observer = new MutationObserver(formatTitles);
            const panelEl = document.getElementById('elementor-panel');
            if (panelEl) {
                observer.observe(panelEl, { childList: true, subtree: true });
            }
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
                        target_selector: '.your-target-class',
                        effect_type: effect.effect_type || 'scale',
                        effect_duration: effect.effect_duration || 300,
                        effect_easing: effect.effect_easing || 'ease'
                    };
                    
                    const effectType = effect.effect_type || 'scale';
                    
                    if (effect.effect_value !== undefined) {
                        if (effectType === 'scale') {
                            target.effect_value_scale = { size: parseFloat(effect.effect_value) };
                        } else if (['rotate', 'skewX', 'skewY'].includes(effectType)) {
                            target.effect_value_rotate = { size: parseFloat(effect.effect_value), unit: 'deg' };
                        } else if (['translateX', 'translateY'].includes(effectType)) {
                            const size = parseFloat(effect.effect_value);
                            const unit = effect.effect_value.includes('%') ? '%' : 'px';
                            target.effect_value_translate = { size: size, unit: unit };
                        } else if (['opacity', 'grayscale', 'blur'].includes(effectType)) {
                            // Blur filter uses value multiplier of 10 inside the resolver, so let's adjust or leave direct
                            const size = parseFloat(effect.effect_value);
                            target.effect_value_intensity = { size: effectType === 'blur' ? size / 10 : size };
                        } else if (['brightness', 'saturate'].includes(effectType)) {
                            target.effect_value_filter_multiplier = { size: parseFloat(effect.effect_value) };
                        } else if (effectType === 'hue-rotate') {
                            target.effect_value_hue = { size: parseFloat(effect.effect_value) };
                        } else if (effectType === 'background') {
                            target.effect_value_color = effect.effect_value;
                        } else if (effectType === 'custom') {
                            target.effect_value_custom = effect.effect_value;
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